#!/usr/bin/env node

/**
 * Generate Critical CSS from compiled Tailwind output.
 *
 * Reads the full compiled CSS (preserving @layer structure) and the above-the-fold
 * PHP templates, then outputs only the CSS rules needed for initial render.
 *
 * Zero dependencies — uses only Node built-ins.
 */

import { readFileSync, writeFileSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');
const DIST = resolve(ROOT, 'dist');

// ---------------------------------------------------------------------------
// 1. Read inputs
// ---------------------------------------------------------------------------

const manifest = JSON.parse(
  readFileSync(resolve(DIST, '.vite/manifest.json'), 'utf8')
);
const entry = manifest['src/theme.js'];
if (!entry?.css?.[0]) {
  console.error('No CSS entry found in manifest');
  process.exit(1);
}
const cssFile = resolve(DIST, entry.css[0]);
const fullCSS = readFileSync(cssFile, 'utf8');

// Above-the-fold templates
const templateFiles = [
  'header.php',
  'template-parts/header-logo.php',
  'template-parts/header-nav-desktop.php',
  'template-parts/header-actions.php',
  'template-parts/header-mobile-menu-button.php',
  'template-parts/header-mobile-menu.php',
  'template-parts/header-search-bar.php',
  'template-parts/slider.php',
  'template-parts/search-form.php',
];

const templateContent = templateFiles
  .map((f) => readFileSync(resolve(ROOT, f), 'utf8'))
  .join('\n');

// ---------------------------------------------------------------------------
// 2. Extract class names from templates
// ---------------------------------------------------------------------------

const classes = new Set();

// class="..." and class='...'
for (const m of templateContent.matchAll(/class\s*=\s*"([^"]*)"/g)) {
  m[1].split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
}
for (const m of templateContent.matchAll(/class\s*=\s*'([^']*)'/g)) {
  m[1].split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
}

// PHP wp_nav_menu args: 'menu_class' => '...', 'li_class' => '...', 'link_class' => '...'
for (const m of templateContent.matchAll(
  /['"](?:menu_class|li_class|link_class)['"]\s*=>\s*'([^']*)'/g
)) {
  m[1].split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
}
for (const m of templateContent.matchAll(
  /['"](?:menu_class|li_class|link_class)['"]\s*=>\s*"([^"]*)"/g
)) {
  m[1].split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
}

// body_class('...')
for (const m of templateContent.matchAll(/body_class\s*\(\s*'([^']*)'\s*\)/g)) {
  m[1].split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
}

// PHP ternary string literals inside class attributes: extract quoted strings
// e.g. <?php echo $is_first ? 'opacity-100' : 'opacity-0 translate-x-full pointer-events-none'; ?>
for (const m of templateContent.matchAll(/<\?php[^?]*\?>/g)) {
  for (const s of m[0].matchAll(/'([^']+)'/g)) {
    // Only add if it looks like CSS classes (no PHP operators, assignments, etc.)
    const candidate = s[1];
    if (
      !candidate.includes('=>') &&
      !candidate.includes('(') &&
      !candidate.includes('$')
    ) {
      candidate.split(/\s+/).filter(Boolean).forEach((c) => classes.add(c));
    }
  }
}

// Supplemental WordPress-generated classes (not in templates but rendered at runtime)
const wpClasses = [
  'menu-item',
  'sub-menu',
  'current-menu-item',
  'current-menu-ancestor',
  'current-menu-parent',
  'menu-item-has-children',
  'no-js',
];
wpClasses.forEach((c) => classes.add(c));

// Clean up non-class tokens that may have slipped in
for (const c of classes) {
  if (
    c.startsWith('<?') ||
    c.startsWith('$') ||
    c.includes('(') ||
    c.includes('=') ||
    c === '|' ||
    c === '||' ||
    c === '&&'
  ) {
    classes.delete(c);
  }
}

// Also add content-[""] pseudo-class variants (after:content-[""], after:content-[''])
// These are used in template nav link_class
classes.add('after:content-[""]');
classes.add("after:content-['']");

// ---------------------------------------------------------------------------
// 3. Parse CSS structure (brace-counting parser)
// ---------------------------------------------------------------------------

/**
 * Extract a brace-balanced block starting at `start` (the opening brace).
 * Returns the content between braces and the index after the closing brace.
 */
function extractBlock(css, start) {
  let depth = 0;
  let i = start;
  while (i < css.length) {
    if (css[i] === '{') depth++;
    else if (css[i] === '}') {
      depth--;
      if (depth === 0) return { content: css.slice(start + 1, i), end: i + 1 };
    }
    i++;
  }
  return { content: css.slice(start + 1), end: css.length };
}

/**
 * Parse the top-level structure of the CSS into named blocks.
 */
function parseTopLevel(css) {
  const blocks = [];
  let i = 0;
  while (i < css.length) {
    // Skip whitespace
    while (i < css.length && /\s/.test(css[i])) i++;
    if (i >= css.length) break;

    // Check for @layer
    if (css.startsWith('@layer ', i)) {
      const nameStart = i + 7;
      const braceIdx = css.indexOf('{', nameStart);
      if (braceIdx === -1) break;
      const name = css.slice(nameStart, braceIdx).trim();
      const { content, end } = extractBlock(css, braceIdx);
      blocks.push({ type: 'layer', name, content, raw: css.slice(i, end) });
      i = end;
    }
    // @font-face
    else if (css.startsWith('@font-face', i)) {
      const braceIdx = css.indexOf('{', i);
      if (braceIdx === -1) break;
      const { content, end } = extractBlock(css, braceIdx);
      blocks.push({
        type: 'font-face',
        content,
        raw: css.slice(i, end),
      });
      i = end;
    }
    // @property
    else if (css.startsWith('@property ', i)) {
      const braceIdx = css.indexOf('{', i);
      if (braceIdx === -1) break;
      const { content, end } = extractBlock(css, braceIdx);
      blocks.push({
        type: 'property',
        raw: css.slice(i, end),
      });
      i = end;
    }
    // Comment
    else if (css.startsWith('/*', i)) {
      const endComment = css.indexOf('*/', i);
      if (endComment === -1) break;
      // Skip comments (e.g. tailwind header comment)
      i = endComment + 2;
    }
    // Other top-level rules (e.g. html,body{overflow-x:hidden}, .prose{...})
    else {
      // Find the next block
      const braceIdx = css.indexOf('{', i);
      if (braceIdx === -1) break;
      const selector = css.slice(i, braceIdx).trim();
      const { content, end } = extractBlock(css, braceIdx);
      blocks.push({
        type: 'rule',
        selector,
        content,
        raw: css.slice(i, end),
      });
      i = end;
    }
  }
  return blocks;
}

// ---------------------------------------------------------------------------
// 4. Filter utilities by class matching
// ---------------------------------------------------------------------------

/**
 * Unescape a CSS selector class to get the original Tailwind class name.
 * E.g. `.lg\:block` → `lg:block`, `.w-\[129px\]` → `w-[129px]`
 */
function unescapeSelector(sel) {
  return sel.replace(/\\(.)/g, '$1');
}

/**
 * Extract class names referenced in a CSS selector.
 * Returns array of unescaped class names.
 */
function extractSelectorClasses(selector) {
  const classNames = [];
  // Match escaped class selectors: \.classname (may contain escaped chars)
  const re = /\.(-?(?:[a-zA-Z_]|\\.)(?:[a-zA-Z0-9_-]|\\.)*)/g;
  let m;
  while ((m = re.exec(selector)) !== null) {
    classNames.push(unescapeSelector(m[1]));
  }
  return classNames;
}

/**
 * Check if a CSS rule block (possibly inside @media) matches any of our classes.
 * The `ruleText` might be a single rule or contain @media wrappers.
 */
function ruleMatchesClasses(ruleText, classSet) {
  const selectorClasses = extractSelectorClasses(ruleText);
  return selectorClasses.some((c) => classSet.has(c));
}

/**
 * Parse individual rules from a layer block content and filter them.
 * Handles both simple rules and @media-wrapped rules.
 */
function filterRules(layerContent, classSet) {
  const kept = [];
  let i = 0;

  while (i < layerContent.length) {
    while (i < layerContent.length && /\s/.test(layerContent[i])) i++;
    if (i >= layerContent.length) break;

    // @media or @supports block
    if (layerContent[i] === '@') {
      const braceIdx = layerContent.indexOf('{', i);
      if (braceIdx === -1) break;
      const atRule = layerContent.slice(i, braceIdx).trim();
      const { content, end } = extractBlock(layerContent, braceIdx);

      // Filter the rules inside the @media block
      const innerFiltered = filterRules(content, classSet);
      if (innerFiltered.length > 0) {
        kept.push(atRule + '{' + innerFiltered + '}');
      }
      i = end;
    }
    // :where(...) or other pseudo selectors at the start
    else {
      // Find the rule's opening brace
      const braceIdx = layerContent.indexOf('{', i);
      if (braceIdx === -1) break;
      const selector = layerContent.slice(i, braceIdx).trim();
      const { content, end } = extractBlock(layerContent, braceIdx);

      if (ruleMatchesClasses(selector, classSet)) {
        kept.push(selector + '{' + content + '}');
      }
      i = end;
    }
  }

  return kept.join('');
}

/**
 * Filter @layer components: only keep header/menu-item rules,
 * skip footer-nav, article-card, wpcf7, expanded-footnote, wp-image-*.
 */
function filterComponents(content) {
  const skipPatterns = [
    'footer-nav',
    'article-card',
    'wpcf7',
    'expanded-footnote',
    'wp-image-',
  ];

  const kept = [];
  let i = 0;

  while (i < content.length) {
    while (i < content.length && /\s/.test(content[i])) i++;
    if (i >= content.length) break;

    // @media block inside components
    if (content[i] === '@') {
      const braceIdx = content.indexOf('{', i);
      if (braceIdx === -1) break;
      const atRule = content.slice(i, braceIdx).trim();
      const { content: innerContent, end } = extractBlock(content, braceIdx);

      // Filter inner rules
      const innerFiltered = filterComponentRules(innerContent, skipPatterns);
      if (innerFiltered.length > 0) {
        kept.push(atRule + '{' + innerFiltered + '}');
      }
      i = end;
    } else {
      const braceIdx = content.indexOf('{', i);
      if (braceIdx === -1) break;
      const selector = content.slice(i, braceIdx).trim();
      const { content: ruleContent, end } = extractBlock(content, braceIdx);

      const shouldSkip = skipPatterns.some((p) => selector.includes(p));
      if (!shouldSkip) {
        kept.push(selector + '{' + ruleContent + '}');
      }
      i = end;
    }
  }

  return kept.join('');
}

function filterComponentRules(content, skipPatterns) {
  const kept = [];
  let i = 0;

  while (i < content.length) {
    while (i < content.length && /\s/.test(content[i])) i++;
    if (i >= content.length) break;

    const braceIdx = content.indexOf('{', i);
    if (braceIdx === -1) break;
    const selector = content.slice(i, braceIdx).trim();
    const { content: ruleContent, end } = extractBlock(content, braceIdx);

    const shouldSkip = skipPatterns.some((p) => selector.includes(p));
    if (!shouldSkip) {
      kept.push(selector + '{' + ruleContent + '}');
    }
    i = end;
  }

  return kept.join('');
}

// ---------------------------------------------------------------------------
// 5. Build critical CSS
// ---------------------------------------------------------------------------

const blocks = parseTopLevel(fullCSS);
const output = [];

for (const block of blocks) {
  if (block.type === 'layer') {
    switch (block.name) {
      case 'properties':
      case 'theme':
      case 'base':
        // Include fully
        output.push(`@layer ${block.name}{${block.content}}`);
        break;

      case 'components': {
        const filtered = filterComponents(block.content);
        if (filtered) {
          output.push(`@layer components{${filtered}}`);
        }
        break;
      }

      case 'utilities': {
        const filtered = filterRules(block.content, classes);
        if (filtered) {
          output.push(`@layer utilities{${filtered}}`);
        }
        break;
      }

      default:
        // Unknown layer — include it to be safe
        output.push(block.raw);
    }
  } else if (block.type === 'font-face') {
    output.push(block.raw);
  } else if (block.type === 'property') {
    output.push(block.raw);
  } else if (block.type === 'rule') {
    // Top-level rules: include html,body{overflow-x:hidden}
    // Skip .prose, WP block styles, alignment helpers (not above-the-fold)
    const fullText = block.selector + block.content;
    const skipPrefixes = [
      '.prose', '.wp-block-', '.alignleft', '.alignright', '.aligncenter',
      '.alignwide', '.alignfull', '.has-drop-cap', '.has-small-font-size',
      '.has-regular-font-size', '.has-normal-font-size', '.has-medium-font-size',
      '.has-large-font-size', '.has-x-large-font-size', '.has-larger-font-size',
      '.has-huge-font-size', '.has-background', '.has-text-color',
      '.has-text-decoration', '.has-text-align', '.is-vertically-aligned',
      '.is-not-stacked',
    ];
    if (skipPrefixes.some((p) => fullText.includes(p))) {
      continue;
    }
    output.push(block.raw);
  }
}

const criticalCSS = output.join('');

// ---------------------------------------------------------------------------
// 6. Write output
// ---------------------------------------------------------------------------

const outPath = resolve(DIST, 'critical.css');
writeFileSync(outPath, criticalCSS, 'utf8');

// Summary
const sizeKB = (Buffer.byteLength(criticalCSS, 'utf8') / 1024).toFixed(1);
const ruleCount = (criticalCSS.match(/\{/g) || []).length;
console.log(`\n✓ Critical CSS generated: dist/critical.css`);
console.log(`  Classes extracted: ${classes.size}`);
console.log(`  CSS blocks: ${ruleCount}`);
console.log(`  Size: ${sizeKB} KB\n`);

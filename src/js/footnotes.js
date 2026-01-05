// Footnotes tooltip - vanilla JS

export function initFootnotes() {
    const container = document.querySelector('.content-full');
    if (!container) return;

    let tooltip = null;
    let hideTimer = null;

    function isMobile() {
        return window.innerWidth <= 1000;
    }

    function getFootnoteText(element) {
        const clone = element.parentNode.cloneNode(true);
        clone.querySelector(`a[id^="sdfootnote"], a[id^="_ftn"]`)?.remove();
        clone.querySelectorAll(`a[href^="#sdfootnote"], a[href^="#_ftnref"]`).forEach(link => link.remove());
        clone.querySelector("sup")?.remove();

        return clone.innerHTML
            .trim()
            .replace(/^\s*\[\d+\]\s*/, "")
            .replace(/^\s*\d+\s*/, "");
    }

    function createTooltip() {
        if (tooltip) return tooltip;
        
        tooltip = document.createElement('div');
        tooltip.id = 'footnote-tooltip';
        tooltip.className = 'footnote-tooltip bg-white border border-gray-200 p-4 rounded-lg shadow-lg fixed text-lg leading-relaxed z-50 max-w-md opacity-0 transition-opacity duration-200 pointer-events-none';
        document.body.appendChild(tooltip);
        
        tooltip.addEventListener('mouseover', () => clearTimeout(hideTimer));
        tooltip.addEventListener('mouseleave', () => hideTooltip());
        
        return tooltip;
    }

    function showTooltip(element, content) {
        clearTimeout(hideTimer);
        const tip = createTooltip();
        
        tip.innerHTML = content;
        tip.classList.remove('opacity-0', 'pointer-events-none');
        tip.classList.add('opacity-100', 'pointer-events-auto');

        // Position tooltip
        const rect = element.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        tip.style.left = '0px'; // reset for width calculation
        let left = Math.max(10, Math.min(rect.left, viewportWidth - tip.offsetWidth - 20));
        let top = rect.bottom + 5;

        if (rect.bottom + tip.offsetHeight > viewportHeight && rect.top > tip.offsetHeight) {
            top = rect.top - tip.offsetHeight - 5;
        }

        tip.style.left = `${left}px`;
        tip.style.top = `${top}px`;
    }

    function hideTooltip() {
        hideTimer = setTimeout(() => {
            if (tooltip) {
                tooltip.classList.add('opacity-0', 'pointer-events-none');
                tooltip.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }, 200);
    }

    function toggleMobileFootnote(link, content, footnoteId) {
        const existingNote = document.getElementById(`expanded-footnote-${footnoteId}`);

        // Close other expanded footnotes
        document.querySelectorAll('.expanded-footnote').forEach(el => {
            if (el.id !== `expanded-footnote-${footnoteId}`) {
                el.classList.add('removing');
                el.classList.remove('entering');
                setTimeout(() => el.remove(), 200);
            }
        });

        if (existingNote) {
            existingNote.classList.add('removing');
            existingNote.classList.remove('entering');
            setTimeout(() => existingNote.remove(), 200);
            return;
        }

        const expandedNote = document.createElement('div');
        expandedNote.id = `expanded-footnote-${footnoteId}`;
        expandedNote.className = 'expanded-footnote';
        expandedNote.innerHTML = content;

        insertExpandedNote(link, expandedNote);

        requestAnimationFrame(() => {
            expandedNote.classList.add('entering');
            expandedNote.style.maxHeight = expandedNote.scrollHeight + 'px';
        });

        expandedNote.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            expandedNote.classList.add('removing');
            expandedNote.classList.remove('entering');
            setTimeout(() => expandedNote.remove(), 200);
        });
    }

    function insertExpandedNote(eventTarget, expandedNote) {
        const supElement = eventTarget.closest('sup') || eventTarget.querySelector('sup');
        const nextNode = (supElement || eventTarget).nextSibling;

        if (nextNode?.nodeType === Node.TEXT_NODE &&
            (nextNode.textContent.startsWith('.') ||
             nextNode.textContent.startsWith(',') ||
             nextNode.textContent.startsWith(' '))) {
            const [firstChar, ...rest] = nextNode.textContent;
            const charNode = document.createTextNode(firstChar);
            const restNode = document.createTextNode(rest.join(''));

            nextNode.parentNode.replaceChild(charNode, nextNode);
            charNode.parentNode.insertBefore(expandedNote, charNode.nextSibling);
            expandedNote.parentNode.insertBefore(restNode, expandedNote.nextSibling);
        } else {
            (supElement || eventTarget).insertAdjacentElement('afterend', expandedNote);
        }
    }

    // Find footnote separator to determine which links are in the footnotes section
    const footnoteSeparator = container.querySelector('.wp-block-separator');
    const isInFootnotes = (element) =>
        footnoteSeparator?.compareDocumentPosition(element) & Node.DOCUMENT_POSITION_FOLLOWING;

    // Find all footnote links
    const links = container.querySelectorAll('a[href*="_ftn"], a[href*="sdfootnote"]');

    links.forEach((link, index) => {
        const href = link.getAttribute('href');
        const targetId = href.substring(1).replace(/^sdfootnoteanc/, 'sdfootnotesym');
        const footnoteContent = document.getElementById(targetId);

        if (!footnoteContent) return;

        if (isInFootnotes(link)) {
            // Links in footnotes section - scroll back to reference
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetRef = document.getElementById(href.replace('_ftn', '_ftnref').substring(1));
                targetRef?.scrollIntoView({ behavior: 'smooth' });
            });
            return;
        }

        const footnoteText = getFootnoteText(footnoteContent);

        if (isMobile()) {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileFootnote(link, footnoteText, index);
            });
        } else {
            link.addEventListener('mouseover', () => showTooltip(link, footnoteText));
            link.addEventListener('mouseout', () => hideTooltip());
            link.addEventListener('click', (e) => {
                e.preventDefault();
                footnoteContent.scrollIntoView({ behavior: 'smooth' });
            });
        }
    });
}

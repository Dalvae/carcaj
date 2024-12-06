## WordPress theme for carcaj-next

Modern rewrite of carcaj.cl theme using Tailwind CSS

### Features

- Tailwind CSS for styling
- Dynamic tooltips and notes
- Reading progress bar
- Turbo hotwire integration
- Vite-powered asset pipeline
- Full responsive design
- Custom post types
- AlpineJS for interactivity

### Prerequisites

- PHP 8.0+
- Node.js 16+
- WordPress 6.0+
- Docker (optional)

### Development Setup

```bash
pnpm install
pnpm run setup
pnpm run build
```

For hot reload development:

1. Set `IS_VITE_DEVELOPMENT = true` in functions.php
2. Run `pnpm run watch`

### Project Structure

```
src/
├── theme.js      # Main JavaScript
├── theme.scss    # Main styles
└── assets/       # Images, fonts, etc.

dist/            # Compiled assets
template-parts/  # Components
```

### Docker Setup

1. Place the full backup from cpanel in a `backup` folder in root directory
2. Run setup:

```bash
pnpm run setup
```

3. Start containers:

```bash
docker-compose up -d
```

4. Access WordPress: http://localhost:8080
5. Development:

```bash
pnpm install
pnpm run watch   # Development with hot reload
pnpm run build   # Production build
```

Note: For Apple Silicon, uncomment `platform: linux/x86_64` in docker-compose.yml

```
## Packages

- Vite
- TailwindCSS
- PostCSS
- AlpineJS
- Turbo (Loaded via NPM)
- Favicon - https://realfavicongenerator.net/

## Theme Functions / Features

I recommend that you read the entire functions.php and src/ folder. Uncomment / comment out features which you
need in your theme.
```

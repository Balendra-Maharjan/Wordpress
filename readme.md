# Origami Risk WordPress Theme

This project is built using the [Sage 11](https://roots.io/sage/) starter theme by Roots and uses [Vite](https://vitejs.dev/) for modern frontend tooling.

## Requirements

- **PHP** >= 8.2  
- **Node.js** >= 22  
- **Composer**
- **Yarn**

## Project Setup

1. Navigate to the Sage theme directory.
2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install Node dependencies:

   ```bash
   yarn
   ```

4. Build assets:

   ```bash
   yarn build
   ```

   For development with hot reload:

   ```bash
   yarn dev
   ```

## Gutenberg Blocks

- This theme uses `block.json` files to register custom Gutenberg blocks.
- All block definitions reside in:  
  `resources/views/blocks/`

## ACF Integration

- Uses a custom plugin named **"Outside ACF JSON Module"** to make Advanced Custom Fields recognize blocks defined via `block.json`.

## Build Output

- Compiled assets are stored in:  
  `public/build/assets/`
- Vendor libraries are bundled inside:  
  `public/build/assets/vendor/`

## Git Flow

- **Base branch:** `main`
- **Pull Requests:**
  - Each PR triggers a GitHub Action that creates a multidev environment on **Pantheon**.
- **Merge to `main`:**
  - Automatically deploys the updated theme to the `dev` environment on **Pantheon**.

## Customization

The theme can be customized through:

- **Style Customization** `resources/styles` – SCSS and CSS files for styling

- **Script Customization** `resources/scripts` – JavaScript files

- **Template Customization**  `resources/views/` – Blade templates

- **PHP Functionality** `app/` – Core PHP files for theme logic and configuration

## Documentation

- Sage 11: [https://roots.io/sage/docs/](https://roots.io/sage/docs/)
- Vite: [https://vitejs.dev/guide/](https://vitejs.dev/guide/)

---

> Built with ❤️ using [Roots Sage](https://roots.io/sage/)

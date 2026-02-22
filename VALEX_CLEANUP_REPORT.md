# Valex Admin Template – Cleanup Report

## Scan summary

References to `/public` assets were collected from:
- **Resources:** `resources/views/**/*.blade.php` (asset(), url())
- **Compiled CSS:** `public/assets/css/style.css` (@import and url() to iconfonts and images)

Anything not referenced is treated as unused Valex material and is safe to remove for debloating.

---

## What is KEPT (referenced)

### Laravel / app (not Valex)
- `public/build/` – Vite build output
- `public/hot` – Vite dev
- `public/index.php` – Laravel entry (if present)

### CSS
- `public/assets/css/style.css` – main Valex styles (used by master layout)

### JavaScript (only these 7 files)
- `public/assets/js/main.js`
- `public/assets/js/defaultmenu.js`
- `public/assets/js/switch.js`
- `public/assets/js/sticky.js`
- `public/assets/js/custom-switcher.js`
- `public/assets/js/custom.js`
- `public/assets/js/us-merc-en.js`

### Libs (only these 6 folders)
- `public/assets/libs/simplebar/`
- `public/assets/libs/@simonwep/pickr/`
- `public/assets/libs/jsvectormap/`
- `public/assets/libs/@popperjs/core/`
- `public/assets/libs/preline/`
- `public/assets/libs/apexcharts/`

### Images
- `public/assets/images/brand-logos/` – favicon + logos (referenced in views)
- `public/assets/images/stitch/` – avatars, hero-map (welcome page)
- `public/assets/images/faces/` – 6.jpg, 9.jpg (header, modals)
- `public/assets/images/media/` – loader.svg + files used in style.css (landing, backgrounds, etc.)
- `public/assets/images/authentication/` – 10.jpg (referenced in style.css)
- `public/assets/images/menu-bg-images/` – bg-img1–5, transparent.png (style.css)
- `public/assets/iconfonts/` – all 13 icon font folders (imported by style.css)

---

## What is DELETED (unused)

| Target | File count | Size (approx.) | Notes |
|--------|------------|----------------|-------|
| **public/html/** | 155 | **30.48 MB** | All Valex demo HTML pages |
| **public/assets/scss/** | 62 | **0.42 MB** | Source SCSS (compiled output is style.css) |
| **public/assets/libs/** (unused folders) | ~3,400+ | **26.42 MB** | All libs except the 6 listed above |
| **public/assets/js/** (unused files) | 136 | **1.36 MB** | All .js except the 7 listed above |
| **public/assets/images/crypto-currencies/** | 78 | **~0.62 MB** | Not referenced anywhere |
| **Total** | **~3,830+** | **~60.3 MB** | |

### Examples of files/folders to be removed

**HTML (Valex demos):**
- `public/html/index.html`, `public/html/index1.html` … `public/html/index11.html`
- `public/html/landing.html`, `public/html/sign-in-basic.html`, `public/html/data-tables.html`
- …all 155 files under `public/html/`

**SCSS (source only):**
- `public/assets/scss/style.scss`, `public/assets/scss/_variables.scss`
- `public/assets/scss/layout/*.scss`, `public/assets/scss/pages/*.scss`, etc.

**Unused libs (entire folders):**
- `public/assets/libs/xlsx/`, `public/assets/libs/echarts/`, `public/assets/libs/remixicon/`
- `public/assets/libs/tom-select/`, `public/assets/libs/swiper/`, `public/assets/libs/flatpickr/`
- `public/assets/libs/chart.js/`, `public/assets/libs/fullcalendar/`, `public/assets/libs/quill/`
- …and all other lib folders except: simplebar, @simonwep, jsvectormap, @popperjs, preline, apexcharts

**Unused JS:**
- `public/assets/js/wishlist.js`, `public/assets/js/datatables.js`, `public/assets/js/apexcharts-pie.js`
- …all 136 files except the 7 kept files listed above

**Unused images:**
- `public/assets/images/crypto-currencies/` (entire folder)

---

## Execution

- **No files have been deleted yet.**
- A script **`cleanup-valex-unused.ps1`** was created in the project root.
- When you are ready, say **"go"** and the script will be run to remove all identified unused assets.
- Folder structure for **remaining** assets is left intact.

---

## After cleanup

- Stitch (Vite) layout is unchanged; it does not use Valex under `public/assets`.
- Master layout (TODA edit/create, drivers, etc.) will still have:
  - `assets/css/style.css`
  - The 7 JS files and 6 lib folders above
  - All referenced images and iconfonts

If anything breaks, restore from version control or backup.

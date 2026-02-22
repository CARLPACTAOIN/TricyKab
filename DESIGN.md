# Design System: TricyKab Admin
**Project ID:** 4774192084894527534

## 1. Visual Theme & Atmosphere
The design presents a **clean, modern, and professional** utilitarian aesthetic. It emphasizes readability and specialized data visualization for fleet management. The interface uses a "Light/Dark" dual-mode capability (though primarily light in the samples) with a distinct "Sidebar + Header" layout. Shadows are subtle (`shadow-sm`), and corners are moderately rounded (`rounded-lg` to `rounded-xl`), creating a friendly but structured feel.

## 2. Color Palette & Roles
*   **Primary Purple (#6258ca)**: Used for active states, primary buttons, branding, and chart elements.
*   **Background Light (#f6f6f8)**: The main canvas background color in light mode.
*   **Background Dark (#14151e)**: The main canvas background color in dark mode.
*   **Success Teal (#09ad95)**: Used for "Active" statuses, positive trends, and completion badges.
*   **Warning Orange (#f5a623)**: Used for "Pending" or "Maintenance" statuses.
*   **Info Blue (#17a2b8)**: Used for informational elements (implied from standard palette, though explicit usage varies).
*   **Slate/Gray Scale**:
    *   `slate-900`: Headings, primary text.
    *   `slate-500`: Secondary text, labels, icons.
    *   `slate-200`: Borders.

## 3. Typography Rules
*   **Font Family**: `Inter` (Google Fonts).
*   **Headings**: Bold (`font-bold`), typically `text-xl` or `text-2xl`.
*   **Body**: `text-sm` is the standard for tables and forms.
*   **Labels**: `text-xs font-semibold uppercase tracking-wider` for table headers and some metric labels.

## 4. Component Stylings
*   **Buttons**:
    *   *Primary*: `bg-primary text-white rounded-lg shadow-md`.
    *   *Icon Actions*: `text-slate-400 hover:text-primary`.
*   **Cards/Containers**: `bg-white rounded-xl border border-slate-200 shadow-sm`.
*   **Tables**:
    *   Headers: `bg-slate-50 border-b border-slate-200`.
    *   Rows: Hover effect `bg-slate-50` (or custom class).
    *   Badges: `rounded-full px-3 py-1 text-xs font-semibold`.
*   **Navigation**:
    *   Sidebar Items: `rounded-lg text-slate-600 hover:bg-slate-50`.
    *   Active Item: `background-color: rgba(88, 98, 202, 0.1); color: #5862ca; border-right: 4px solid`.

## 5. Layout Principles
*   **Sidebar**: Fixed width (`w-64`), dark/light variants.
*   **Header**: Sticky (`sticky top-0`), height `h-16`, flexbox alignment.
*   **Grid**: Extensive use of `grid` and `flex` for dashboard widgets.
*   **Spacing**: Consistent padding `p-6` or `p-8` for main content areas.

Create the complete design system for **ArchTech Suite** — an AI-powered platform for architecture firms.

## Brand Identity

- **Personality:** Modern, premium, trustworthy, innovative. No generic AI aesthetics.
- **Concept:** "Organic precision" — where architectural rigor meets natural fluidity

## Color System

- **Primary:** `#1A5C8A` (deep teal-blue) — trust, professionalism
- **Secondary:** `#E8C87A` (warm gold) — premium accent, highlights
- **Neutral:** `#F7F6F3` (warm white) backgrounds · `#E2E0DB` borders · `#5C5A55` text
- **Success:** `#3A9D6E` · **Warning:** `#D4A24C` · **Error:** `#C44A4A`
- **Gradients:** Subtle warm-to-cool for cards and heroes

## Typography

- **Display:** Satoshi or Instrument Sans (geometric, architectural)
- **Body:** Inter (clean, highly readable)
- **Scale:** 14px body · 16px large · 20px h4 · 24px h3 · 32px h2 · 48px h1

## Architecture-Specific Elements

- **Grid system:** 12-column, 24px gutter — references architectural blueprints
- **Cards:** Thin borders (`1px #E2E0DB`), subtle shadow (`0 2px 8px rgba(0,0,0,0.04)`)
- **Data displays:** Clean tables with sticky headers, subtle row stripes
- **Timeline:** Gantt-style with phase colors, draggable milestones

## Leaf-Inspired Page Transitions

- **Page enter:** Elements fade in with a slight upward drift + scale (0.95→1), staggered at 60ms intervals
- **Page exit:** Elements fade out with a gentle downward drift + scale (1→0.97)
- **Section reveals:** CSS `clip-path: inset(0 0 100% 0)` → `inset(0)` — simulates an unfurling leaf
- **Card hover:** Lifts 4px with a soft shadow bloom — like picking up a paper from a desk
- **Navigation:** Active tab has an underline that sweeps in from left, like a pen stroke
- **Duration:** 300-500ms, `cubic-bezier(0.25, 0.1, 0.25, 1)` — organic ease

## Components (mandatory patterns)

- **Sidebar nav:** Collapsible, with micro-animated icons (stroke → fill on active)
- **Data tables:** Row hover highlight, sortable columns, inline actions
- **Modals:** Centered, backdrop blur `4px`, enter from bottom with scale
- **Buttons:** Primary (filled `#1A5C8A`), Secondary (outlined), Ghost (text only)
- **Status badges:** Dot + label pattern — success (green), pending (gold), error (red)
- **Forms:** Floating labels, focus ring in primary color, inline validation

## Dashboard Layout

- **Left sidebar** (240px collapsed → 64px) · **Top bar** with search + user menu
- **Main content** with max-width `1200px`, centered
- **Widgets** in 2-4 column responsive grid, equal height

## File Format

Deliver as a complete design system: colors, typography scale, component library, spacing tokens, and a sample dashboard layout. Use light mode only.

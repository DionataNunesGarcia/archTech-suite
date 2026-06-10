Create a modern, premium, high-conversion website design for **ArchTech Suite** — an AI-powered platform for architecture firms.

The design must fix common AI layout issues: low contrast, weak hierarchy, poor readability. No generic AI aesthetics.

---

## 🎨 TOKEN-BASED DESIGN SYSTEM (MUST MATCH front_theme SCSS)

All colors must be expressed as CSS custom properties matching the existing `_colors.scss` token file.

### Colors (use OKLCH)

```css
:root {
	/* Primary — deep teal-blue (trust, architecture) */
	--color-primary: oklch(45% 0.098 248.5); /* #1A5C8A */
	--color-primary-light: oklch(62% 0.118 248.5);
	--color-primary-dark: oklch(35% 0.078 248.5);
	--color-primary-subtle: oklch(96% 0.018 248.5);
	--color-primary-muted: oklch(92% 0.038 248.5);
	--color-primary-foreground: #ffffff;

	/* Secondary — warm gold (premium, highlights) */
	--color-secondary: oklch(72% 0.118 82.5); /* #E8C87A */
	--color-secondary-light: oklch(82% 0.078 82.5);
	--color-secondary-dark: oklch(62% 0.098 82.5);
	--color-secondary-subtle: oklch(98% 0.018 82.5);
	--color-secondary-muted: oklch(95% 0.048 82.5);
	--color-secondary-foreground: #1a1a18;

	/* Neutrals — warm off-white */
	--color-neutral-50: oklch(99% 0.004 85);
	--color-neutral-100: oklch(97% 0.006 85);
	--color-neutral-200: oklch(93% 0.008 85);
	--color-neutral-300: oklch(87% 0.008 85);
	--color-neutral-400: oklch(72% 0.008 85);
	--color-neutral-500: oklch(58% 0.008 85);
	--color-neutral-600: oklch(46% 0.008 85);
	--color-neutral-700: oklch(38% 0.008 85);
	--color-neutral-800: oklch(28% 0.008 85);
	--color-neutral-900: oklch(18% 0.008 85);

	/* Semantic */
	--background: #fcfcf9;
	--foreground: var(--color-neutral-900);
	--muted: #f2f1ed;
	--muted-foreground: #6b6a66;
	--border: rgba(0, 0, 0, 0.08);
}
```

### Typography (MUST match \_typography.scss tokens)

```css
:root {
	--font-family: 'Instrument Sans', system-ui, sans-serif;
	--font-family-serif: 'PT Serif', Georgia, serif;
	--font-size-base: 1rem; /* 16px */
	--text-sm: 0.875rem;
	--text-base: 1rem;
	--text-lg: 1.125rem;
	--text-xl: 1.25rem;
	--text-2xl: 1.5rem;
	--text-3xl: 1.875rem;
	--text-4xl: 2.25rem;
	--text-5xl: 3rem;
	--text-6xl: 3.75rem;
	--text-7xl: 4.5rem;
}
```

### Spacing (MUST match \_spacing.scss tokens)

`--space-1` through `--space-16` (0.25rem increments). Max content width: 1200px.

### Effects (MUST match \_effects.scss tokens)

`--radius: 0.5rem`, `--shadow-sm`, `--shadow-md`, `--blur-sm: 4px`.

---

## ⚠️ CRITICAL UI FIXES (MANDATORY)

1. **Hero readability** — dark gradient overlay over background image. Backdrop blur on text container. Strong text contrast.
2. **Header** — solid white with `box-shadow` on scroll. Backdrop blur if transparent. Menu links with hover underline animation.
3. **Visual hierarchy** — dominant headline (`text-5xl`/`text-6xl`), clear subheadline, visually prominent CTA in `--color-primary`.
4. **CTAs** — maximum contrast. Primary filled button. Ghost button as secondary. Hover: lift 2px + shadow.
5. **Cards** — `1px solid var(--border)`, `background: white`, `border-radius: var(--radius)`, hover shadow.

---

## 🧱 LAYOUT SYSTEM

- 12-column grid, max-width 1200px centered
- Auto Layout components
- Desktop + Mobile versions
- Reusable component library

---

## 🧩 WEBSITE STRUCTURE (Twig-ready)

Each section must be producible as a Drupal Twig template:

1. **HEADER** — `templates/includes/header.html.twig`
   - Logo (left) · Primary menu (center) · CTA button (right)
   - Sticky on scroll with shadow

2. **HERO** — `templates/layout/page.html.twig`
   - Full-viewport background image with dark overlay (gradient: black 40% → transparent)
   - Headline: "Tecnologia que transforma a maneira como você projeta"
   - Subheadline: "A plataforma de IA que potencializa seu escritório de arquitetura"
   - Two CTAs: "Começar agora" (primary) · "Ver demonstração" (outline)
   - Floating mockup or dashboard preview below

3. **CLIENTS / TRUST** — logo carousel (grayscale → color on hover, infinite auto-scroll)
4. **SOLUTIONS** — 3-column card grid: "Para Arquitetos", "Para Escritórios", "Para Grandes Projetos"
5. **FEATURES** — alternating image + text rows with scroll-reveal animation
6. **HOW IT WORKS** — numbered step cards with connecting line
7. **TESTIMONIALS** — carousel with avatar + quote + name + firm
8. **PRICING** — 3-tier cards (Professional, Studio, Enterprise)
9. **CTA FINAL** — dark background, headline + subheadline + input email + CTA
10. **FOOTER** — `templates/includes/footer.html.twig`

---

## ✨ ANIMATIONS

- **Scroll reveal:** fade-up + translateY(20px), staggered by 100ms
- **Hero:** elements fade in sequentially (headline → subheadline → CTAs → mockup)
- **Cards hover:** lift 4px + shadow bloom, 300ms ease
- **Logo carousel:** infinite horizontal scroll, paused on hover
- **Page transitions:** content fades with slight upward drift

---

## 📱 MOBILE

- Vertical stack layout
- Collapsible hamburger menu
- Simplified hero (smaller text, single CTA)
- Cards full-width

---

## 🎯 UX GOALS

- High conversion (clear CTAs, social proof, risk reversal)
- Trust signals (client logos, testimonials, stats)
- Readability first (WCAG AA minimum)
- Premium architectural feel (clean geometry, warm neutrals, gold accents)

---

## 🌐 LANGUAGE

All headings, labels and body text in **Brazilian Portuguese**.

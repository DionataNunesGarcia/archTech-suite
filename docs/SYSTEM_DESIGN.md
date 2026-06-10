# Duralux Admin - System Design

> Documento reutilizável de design system para replicar o padrão visual e arquitetural em novos projetos.

---

## 1. Visão Geral

| Propriedade         | Valor                         |
| ------------------- | ----------------------------- |
| **Projeto**         | Duralux - CRM Admin Dashboard |
| **Framework Base**  | Bootstrap 5                   |
| **Pré-processador** | SCSS                          |
| **Icon Library**    | Feather Icons                 |
| **Chart Library**   | ApexCharts                    |
| **Font Padrão**     | Inter (400, 500, 600, 700)    |
| **Body BG**         | `#f0f2f8`                     |
| **Body Color**      | `#4b5563`                     |
| **Font Size Base**  | `0.84rem` ($font-body)        |
| **Line Height**     | `1.6`                         |

---

## 2. Paleta de Cores

### 2.1 Cores Primárias (Theme Colors)

| Token         | Variável                   | Hex       | Uso                                  |
| ------------- | -------------------------- | --------- | ------------------------------------ |
| **Primary**   | `$primary` / `$blue`       | `#3454d1` | Ações principais, links ativos, CTAs |
| **Secondary** | `$secondary` / `$gray-600` | `#64748b` | Ações secundárias                    |
| **Success**   | `$success` / `$green`      | `#25b865` | Confirmações, status positivo        |
| **Warning**   | `$warning` / `$yellow`     | `#ffa21d` | Alertas, atenção                     |
| **Danger**    | `$danger` / `$red`         | `#d13b4c` | Erros, ações destrutivas             |
| **Info**      | `$info` / `$cyan`          | `#3dc7be` | Informações, dicas                   |
| **Light**     | `$light` / `$gray-100`     | `#eff0f6` | Backgrounds leves                    |
| **Dark**      | `$dark`                    | `#283c50` | Textos principais, headings          |
| **Darken**    | `$darken`                  | `#001327` | Modo escuro - fundo                  |

### 2.2 Escala de Cinza

| Token    | Variável    | Hex       |
| -------- | ----------- | --------- |
| White    | `$white`    | `#ffffff` |
| Gray 100 | `$gray-100` | `#eff0f6` |
| Gray 200 | `$gray-200` | `#e9ecef` |
| Gray 300 | `$gray-300` | `#e5e7eb` |
| Gray 400 | `$gray-400` | `#ced4da` |
| Gray 500 | `$gray-500` | `#91a1b6` |
| Gray 600 | `$gray-600` | `#64748b` |
| Gray 700 | `$gray-700` | `#495057` |
| Gray 800 | `$gray-800` | `#343a40` |
| Gray 900 | `$gray-900` | `#212529` |
| Black    | `$black`    | `#000000` |

### 2.3 Cores Semânticas da Marca

| Token       | Variável       | Hex       | Uso                             |
| ----------- | -------------- | --------- | ------------------------------- |
| Brand Body  | `$brand-body`  | `#6b7885` | Cor padrão de texto de corpo    |
| Brand Dark  | `$brand-dark`  | `#283c50` | Títulos, textos fortes          |
| Brand Muted | `$brand-muted` | `#7587a7` | Textos secundários, labels      |
| Brand Light | `$brand-light` | `#eaebef` | Backgrounds leves, hover states |

### 2.4 Cores Soft (Backgrounds com Opacidade)

| Token              | Valor                 |
| ------------------ | --------------------- |
| `$bg-soft-primary` | `rgb(#3454d1, 0.075)` |
| `$bg-soft-success` | `rgb(#25b865, 0.075)` |
| `$bg-soft-danger`  | `rgb(#d13b4c, 0.075)` |
| `$bg-soft-info`    | `rgb(#3dc7be, 0.075)` |
| `$bg-soft-warning` | `rgb(#ffa21d, 0.075)` |
| `$bg-soft-teal`    | `rgb(#41b2c4, 0.075)` |
| `$bg-soft-cyan`    | `rgb(#3dc7be, 0.075)` |
| `$bg-soft-indigo`  | `rgb(#6610f2, 0.075)` |
| `$bg-soft-darken`  | `rgb(#001327, 0.075)` |

### 2.5 Tema Escuro (Dark Mode)

| Token              | Variável                   | Hex       | Uso              |
| ------------------ | -------------------------- | --------- | ---------------- |
| Dark Body BG       | `$dark-theme-color-dark`   | `#121a2d` | Fundo do body    |
| Dark Navigation BG | `$dark-theme-color-darker` | `#0f172a` | Fundo da sidebar |
| Dark Text          | `$dark-theme-color`        | `#b1b4c0` | Texto default    |
| Dark Hover         | `$dark-theme-color-hover`  | `#1c2438` | Hover states     |
| Dark Border        | `$dark-theme-color-border` | `#1b2436` | Bordas           |

---

## 3. Tipografia

### 3.1 Famílias de Fontes Disponíveis

| Nome               | Variável                | Uso Principal          |
| ------------------ | ----------------------- | ---------------------- |
| **Inter** (padrão) | `$font-inter`           | Body text, UI elements |
| Lato               | `$font-lato`            | -                      |
| Rubik              | `$font-rubik`           | -                      |
| Cinzel             | `$font-cinzel`          | -                      |
| Nunito             | `$font-nunito`          | -                      |
| Roboto             | `$font-roboto`          | -                      |
| Ubuntu             | `$font-ubuntu`          | -                      |
| Poppins            | `$font-poppins`         | -                      |
| Raleway            | `$font-raleway`         | -                      |
| Noto Sans          | `$font-noto-sans`       | -                      |
| Fira Sans          | `$font-fira-sans`       | -                      |
| Work Sans          | `$font-work-sans`       | -                      |
| Maven Pro          | `$font-maven-pro`       | -                      |
| Open Sans          | `$font-open-sans`       | -                      |
| Quicksand          | `$font-quicksand`       | -                      |
| Roboto Slab        | `$font-roboto-slab`     | -                      |
| Montserrat         | `$font-montserrat`      | -                      |
| Josefin Sans       | `$font-josefin-sans`    | -                      |
| IBM Plex Sans      | `$font-ibm-plex-sans`   | -                      |
| Source Sans Pro    | `$font-source-sans-pro` | -                      |
| Montserrat Alt     | `$font-montserrat-alt`  | -                      |
| System UI          | `$font-system-ui`       | Fallback do sistema    |

### 3.2 Escala de Fontes (px)

| Token      | Valor |
| ---------- | ----- |
| `$font-5`  | 5px   |
| `$font-6`  | 6px   |
| `$font-7`  | 7px   |
| `$font-8`  | 8px   |
| `$font-9`  | 9px   |
| `$font-10` | 10px  |
| `$font-11` | 11px  |
| `$font-12` | 12px  |
| `$font-13` | 13px  |
| `$font-14` | 14px  |
| `$font-15` | 15px  |
| `$font-16` | 16px  |
| `$font-17` | 17px  |
| `$font-18` | 18px  |
| `$font-19` | 19px  |
| `$font-20` | 20px  |
| `$font-22` | 22px  |
| `$font-24` | 24px  |
| `$font-26` | 26px  |
| `$font-28` | 28px  |
| `$font-30` | 30px  |

### 3.3 Headings

| Nível | Tamanho | Peso |
| ----- | ------- | ---- |
| h1    | 36px    | 700  |
| h2    | 28px    | 700  |
| h3    | 24px    | 700  |
| h4    | 20px    | 700  |
| h5    | 16px    | 700  |
| h6    | 15px    | 700  |

### 3.4 Font Weights

| Token       | Valor | Nome Bootstrap |
| ----------- | ----- | -------------- |
| `$font-100` | 100   | Thin           |
| `$font-200` | 200   | Light          |
| `$font-300` | 300   | Lighter        |
| `$font-400` | 400   | Normal         |
| `$font-500` | 500   | Medium         |
| `$font-600` | 600   | Semibold       |
| `$font-700` | 700   | Bold           |
| `$font-800` | 800   | Bolder         |
| `$font-900` | 900   | Black          |

### 3.5 Letter Spacing

| Token                | Valor  |
| -------------------- | ------ |
| `$text-spacing-none` | 0      |
| `$text-spacing-xs`   | 0.15px |
| `$text-spacing-sm`   | 0.25px |
| `$text-spacing-md`   | 0.5px  |
| `$text-spacing-xl`   | 1px    |
| `$text-spacing-xxl`  | 1.5px  |
| `$text-spacing-xxxl` | 2px    |

---

## 4. Espaçamento e Layout

### 4.1 Header

| Propriedade          | Valor                          |
| -------------------- | ------------------------------ |
| `$header-height`     | `80px`                         |
| Header BG (Light)    | `$white` (#ffffff)             |
| Header BG (Dark)     | `$header-background` (#0f172a) |
| Header Color (Light) | `#2c3344`                      |
| Header Link Color    | `#6b7280`                      |
| Header Border        | `#1b2436` (dark mode)          |

### 4.2 Navegação / Sidebar

| Propriedade                   | Valor                 |
| ----------------------------- | --------------------- |
| `$navigation-width`           | `280px`               |
| `$navigation-collapsed-width` | `100px`               |
| Nav BG (Dark)                 | `#0f172a`             |
| Nav BG (Light)                | `$white`              |
| Nav Text Color                | `#b1b4c0` (dark mode) |
| Nav Active Color              | `$white`              |
| Nav Caption Color             | `$white`              |
| Nav Hover BG                  | `#1c2438`             |
| Nav Border                    | `#1b2436`             |

### 4.3 Content Area

| Propriedade                   | Valor           |
| ----------------------------- | --------------- |
| Main Content Padding          | `30px 30px 5px` |
| Page Header Min Height        | `65px`          |
| Page Header Padding           | `0 30px`        |
| Footer Padding                | `20px 30px`     |
| Card Padding                  | `25px`          |
| Content Sidebar Header Height | `75px`          |
| Content Area Header Height    | `75px`          |

### 4.4 Topbar (Horizontal Menu)

| Propriedade      | Valor     |
| ---------------- | --------- |
| `$topbar-height` | `60px`    |
| Topbar Color     | `#b5bdca` |
| Topbar BG        | `#1b2335` |

---

## 5. Bordas e Raius

### 5.1 Border Radius

| Token            | Valor |
| ---------------- | ----- |
| `$radius-none`   | 0     |
| `$radius-xs`     | 3px   |
| `$radius-sm`     | 5px   |
| `$radius-md`     | 10px  |
| `$radius-lg`     | 15px  |
| `$radius-xl`     | 20px  |
| `$radius-xxl`    | 25px  |
| `$radius-pill`   | 30px  |
| `$radius-circle` | 50px  |

### 5.2 Border Colors

| Token                       | Valor                  | Uso            |
| --------------------------- | ---------------------- | -------------- |
| `$border-none`              | transparent            | Sem borda      |
| `$border-soft`              | darken($gray-100, 1%)  | Bordas sutis   |
| `$border-normal`            | darken($gray-100, 2%)  | Bordas normais |
| `$border-medium`            | darken($gray-100, 5%)  | Bordas médias  |
| `$border-hard`              | darken($gray-100, 8%)  | Bordas fortes  |
| `$border-contrast`          | darken($gray-100, 12%) | Alto contraste |
| `$border-color` (Bootstrap) | `#e5e7eb`              | Default        |
| `$border-color-2`           | `#dcdee4`              | Alternativa    |

### 5.3 Border Widths

Valores disponíveis: 0, 1px, 2px, 3px, 4px, 5px, 6px, 7px, 8px, 9px, 10px

---

## 6. Shadows

| Token                 | Valor                                                         |
| --------------------- | ------------------------------------------------------------- |
| `$shadow-none`        | none                                                          |
| `$shadow-sm`          | `0 1px 5px rgba($dark, 0.15)`                                 |
| `$shadow-md`          | `0 5px 15px rgba($dark, 0.15)`                                |
| `$shadow-lg`          | `0 10px 25px rgba($dark, 0.15)`                               |
| `$shadow-xl`          | `0 15px 35px rgba($dark, 0.15)`                               |
| `$shadow-xxl`         | `0 20px 45px rgba($dark, 0.15)`                               |
| `$card-shadow`        | `0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1)` |
| Dropdown Shadow       | `0 10px 24px 0 rgba(62, 57, 107, 0.18)`                       |
| Dark Theme Box Shadow | `0 0 20px rgb(0 0 0 / 50%)`                                   |

---

## 7. Componentes

### 7.1 Cards

```scss
// Estrutura base
.card {
	margin-bottom: 24px;
	border-radius: 10px; // $radius-md
	border: 1px solid transparent;
	box-shadow: $card-shadow; // sm shadow
	transition: all 0.3s ease;
}

.card-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	border-bottom: 1px solid $border-color;
	background-color: transparent;
}

.card-title {
	color: $brand-dark; // #283c50
	font-size: 16px; // $font-16
	font-weight: 700; // $font-700
}

// Padding: 25px ($card-spacer-y / $card-spacer-x)
```

**Classes utilitárias de card**:

- `.card` — Container base com shadow
- `.card-header` — Cabeçalho flex com título e ações
- `.card-body` — Corpo do card
- `.card-footer` — Rodapé do card
- `.card-title` — Tópic com estilo h6

### 7.2 Buttons

```scss
.btn {
	display: flex;
	padding: 12px 16px;
	font-size: 10px; // $font-10
	font-weight: 700; // $font-700
	text-transform: uppercase; // $font-uppercase
	letter-spacing: 0.5px; // $text-spacing-md
	border-radius: 3px; // $radius-xs
	transition: all 0.3s ease;
	align-items: center;
	justify-content: center;
}
```

**Variantes de botão**:

- `.btn-primary` — Azul #3454d1
- `.btn-secondary` — Cinza #64748b
- `.btn-success` — Verde #25b865
- `.btn-warning` — Amarelo #ffa21d
- `.btn-danger` — Vermelho #d13b4c
- `.btn-info` — Ciano #3dc7be
- `.btn-light` — Cinza claro #eff0f6
- `.btn-dark` — Escuro #283c50
- `.btn-darken` — Muito escuro #001327
- `.btn-light-{color}` — Versão soft (BG com 7.5% opacity, texto colorido, hover preenche)

### 7.3 Badges

```scss
.badge {
  font-size: 11px;               // $font-11
  font-weight: 600;              // $font-600
  padding: 5px 6px;
  border-radius: 3px;            // $radius-xs
}

// Variantes soft
.badge.bg-light-{color} {
  color: $color;
  background: rgb($color, 0.075);
  border-color: rgb($color, 0.075);
}
```

### 7.4 Forms

```scss
// Inputs
.form-control,
.form-select,
input {
	color: $dark; // #283c50
	padding: 12px 15px;
	border-color: $border-color; // #e5e7eb
	border-radius: 5px; // $radius-sm

	&:focus {
		border-color: $primary !important; // #3454d1
		box-shadow: none !important;
	}

	&::placeholder {
		color: $gray-500 !important; // #91a1b6
	}
}

.form-label,
.col-form-label {
	color: $dark !important;
	font-size: 12px !important; // $font-12
	font-weight: 600 !important; // $font-600
}

$input-font-size: 0.845rem;
$input-btn-padding-y: 0.5rem;
```

### 7.5 Tables

```scss
.table {
	color: $brand-dark; // #283c50
	border-color: $border-color;

	thead th {
		padding: 8px 15px;
		color: $brand-dark;
		font-size: 10px; // $font-10
		font-weight: 700; // $font-700
		text-transform: uppercase;
		border-bottom: 1px solid $border-color;
	}

	tbody td {
		padding: 15px 15px;
		white-space: nowrap;
		vertical-align: middle;
	}
}
```

### 7.6 Alerts (Soft Messages)

| Classe                        | Cor       | BG                      | Border                             |
| ----------------------------- | --------- | ----------------------- | ---------------------------------- |
| `.alert-soft-success-message` | `#25b865` | `lighten(#25b865, 52%)` | `1px dashed lighten(#25b865, 25%)` |
| `.alert-soft-warning-message` | `#ffa21d` | `lighten(#ffa21d, 40%)` | `1px dashed lighten(#ffa21d, 25%)` |
| `.alert-soft-danger-message`  | `#d13b4c` | `lighten(#d13b4c, 35%)` | `1px dashed lighten(#d13b4c, 28%)` |
| `.alert-soft-teal-message`    | `#41b2c4` | `lighten(#41b2c4, 45%)` | `1px dashed lighten(#41b2c4, 20%)` |

### 7.7 Navs & Tabs

```scss
.nav-tabs-custom-style {
	border-bottom: none;

	.nav-link {
		padding: 20px 25px;
		color: $brand-dark; // #283c50
		font-weight: 600;
		border-radius: 0;
		border-bottom: 3px solid transparent;

		&.active,
		&.hover {
			color: $primary; // #3454d1
			border-bottom: 3px solid $primary;
			background-color: $bg-soft-primary;
		}
	}
}
```

### 7.8 Accordions

```scss
.accordion-item {
	background-color: $white;
	border-color: $border-color;
	border-radius: 5px; // $radius-sm

	.accordion-button {
		color: $dark;
		font-size: 14px; // $font-14
		font-weight: 700; // $font-700
		border-color: $border-color;

		&:not(.collapsed) {
			box-shadow: none;
			background-color: $gray-200; // #e9ecef
		}
	}

	.accordion-body {
		color: $brand-body; // #6b7885
		padding: 25px;
	}
}
```

### 7.9 Modals

```scss
.modal-content {
	border-radius: 10px; // $radius-md
}

.modal-backdrop {
	background-color: #022142;
}

// Modal aberto aplica blur(3px) no fundo
body.modal-open .nxl-header,
body.modal-open .nxl-navigation,
body.modal-open .page-header,
body.modal-open .nxl-container {
	filter: blur(3px);
}
```

### 7.10 Dropdowns

```scss
.dropdown-menu {
	padding: 15px 0;
	border: 1px solid $border-color;
	box-shadow: 0 10px 24px 0 rgba(62, 57, 107, 0.18);
	width: 225px;

	// Bordas arredondadas direcionais
	&[data-popper-placement='bottom-*'] {
		border-radius: 0 0 10px 10px;
	}
	&[data-popper-placement='top-*'] {
		border-radius: 10px 10px 0 0;
	}
}

.dropdown-item {
	margin: 3px 10px;
	padding: 10px 15px;
	color: $brand-dark;
	font-size: 14px;
	text-transform: capitalize;
}
```

### 7.11 Avatars

```scss
.avatar-text,
.avatar-image {
	width: 40px;
	height: 40px;
	min-width: 40px;
	min-height: 40px;
	max-width: 40px;
	max-height: 40px;
	overflow: hidden;
	border-radius: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: 700;
	background-color: $white;
	border: 1px solid $border-color-2;
}

// Tamanhos: .avatar-xs (12px), .avatar-sm, .avatar-md, .avatar-lg, .avatar-xl, .avatar-xxl
```

### 7.12 Pagination

```scss
ul.pagination-common-style li a {
	width: 30px;
	height: 30px;
	color: $dark;
	font-size: 12px; // $font-12
	border-radius: 100%;
	border: 1px solid darken($gray-100, 2%);

	&:hover,
	&.active {
		color: $white !important;
		background-color: $primary;
	}
}
```

---

## 8. Layout Architecture

### 8.1 Estrutura HTML Base

```
┌────────────────────────────────────────────────────┐
│  <nav class="nxl-navigation">                      │
│    ┌─────────────────────────────┐                 │
│    │ .m-header (Logo)            │  280px width    │
│    │ .navbar-content             │                 │
│    │   ul.nxl-navbar             │                 │
│    │     li.nxl-item.nxl-caption │                 │
│    │     li.nxl-item.nxl-hasmenu │                 │
│    │       a.nxl-link             │                 │
│    │         span.nxl-micon      │                 │
│    │         span.nxl-mtext      │                 │
│    │         span.nxl-arrow     │                 │
│    │       ul.nxl-submenu        │                 │
│    │         li.nxl-item         │                 │
│    └─────────────────────────────┘                 │
└────────────────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  <header class="nxl-header">            │  80px height
│    .header-wrapper                       │
│      .header-left                        │
│        .nxl-head-mobile-toggler          │
│        .nxl-navigation-toggle            │
│      .header-center (mega menu)          │
│      .header-right                       │
│        .nxl-h-item > .nxl-head-link      │
│        .dropdown (notifications)         │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  .page-header                            │  65px min-height
│    h5 (Page Title)                       │
│    .breadcrumb                           │
│    .page-header-right                    │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  .nxl-container                          │
│    .nxl-content                          │
│      .main-content                       │  padding: 30px
│        [cards, widgets, tables...]       │
│    .footer                               │
└──────────────────────────────────────────┘
```

### 8.2 Nomenclatura de Classes CSS (Convenção BEM-like)

| Padrão      | Exemplo           | Elemento                   |
| ----------- | ----------------- | -------------------------- |
| Bloco       | `.nxl-navigation` | Sidebar/Nav                |
| Bloco       | `.nxl-header`     | Header                     |
| Bloco       | `.nxl-container`  | Container principal        |
| Bloco       | `.nxl-content`    | Conteúdo                   |
| Elemento    | `.m-header`       | Logo header na nav         |
| Elemento    | `.navbar-content` | Conteúdo scrollável da nav |
| Elemento    | `.nxl-navbar`     | Lista de menu              |
| Elemento    | `.nxl-item`       | Item de menu               |
| Modificador | `.nxl-hasmenu`    | Item com submenu           |
| Elemento    | `.nxl-link`       | Link do item               |
| Elemento    | `.nxl-micon`      | Ícone do menu              |
| Elemento    | `.nxl-mtext`      | Texto do menu              |
| Elemento    | `.nxl-arrow`      | Seta de submenu            |
| Elemento    | `.nxl-submenu`    | Submenu                    |
| Elemento    | `.nxl-caption`    | Label de seção do menu     |

### 8.3 Estados do Layout

| Classe                  | Efeito                            |
| ----------------------- | --------------------------------- |
| `.minimenu`             | Sidebar colapsada (280px → 100px) |
| `.app-navigation-light` | Nav com fundo claro               |
| `.app-navigation-dark`  | Nav com fundo escuro              |
| `.app-header-light`     | Header com fundo claro            |
| `.app-header-dark`      | Header com fundo escuro           |
| `.app-skin-light`       | Skin geral claro                  |
| `.app-skin-dark`        | Skin geral escuro                 |

---

## 9. Páginas

### 9.1 Dashboard & Analytics

| Arquivo          | Descrição                               |
| ---------------- | --------------------------------------- |
| `index.html`     | Dashboard CRM (KPIs, gráficos, tabelas) |
| `analytics.html` | Analytics (métricas avançadas, mapas)   |

### 9.2 Reports

| Arquivo                   | Descrição               |
| ------------------------- | ----------------------- |
| `reports-sales.html`      | Relatório de vendas     |
| `reports-leads.html`      | Relatório de leads      |
| `reports-project.html`    | Relatório de projetos   |
| `reports-timesheets.html` | Relatório de timesheets |

### 9.3 Applications

| Arquivo              | Descrição                  |
| -------------------- | -------------------------- |
| `apps-chat.html`     | Chat em tempo real         |
| `apps-email.html`    | Cliente de email           |
| `apps-tasks.html`    | Gestão de tarefas (Kanban) |
| `apps-notes.html`    | Notas pessoais             |
| `apps-storage.html`  | Gerenciador de arquivos    |
| `apps-calendar.html` | Calendário de eventos      |

### 9.4 CRM Entities

| Arquivo                 | Descrição           |
| ----------------------- | ------------------- |
| `proposal.html`         | Lista de propostas  |
| `proposal-view.html`    | Visualizar proposta |
| `proposal-edit.html`    | Editar proposta     |
| `proposal-create.html`  | Criar proposta      |
| `payment.html`          | Pagamentos          |
| `invoice-view.html`     | Visualizar fatura   |
| `invoice-create.html`   | Criar fatura        |
| `customers.html`        | Lista de clientes   |
| `customers-view.html`   | Visualizar cliente  |
| `customers-create.html` | Criar cliente       |
| `leads.html`            | Lista de leads      |
| `leads-view.html`       | Visualizar lead     |
| `leads-create.html`     | Criar lead          |
| `projects.html`         | Lista de projetos   |
| `projects-view.html`    | Visualizar projeto  |
| `projects-create.html`  | Criar projeto       |

### 9.5 Widgets

| Arquivo                      | Descrição               |
| ---------------------------- | ----------------------- |
| `widgets-lists.html`         | Widgets de listas       |
| `widgets-tables.html`        | Widgets de tabelas      |
| `widgets-charts.html`        | Widgets de gráficos     |
| `widgets-statistics.html`    | Widgets de estatísticas |
| `widgets-miscellaneous.html` | Widgets diversos        |

### 9.6 Settings

| Arquivo                       | Descrição              |
| ----------------------------- | ---------------------- |
| `settings-general.html`       | Configurações gerais   |
| `settings-seo.html`           | Configurações SEO      |
| `settings-tags.html`          | Gestão de tags         |
| `settings-email.html`         | Configurações de email |
| `settings-tasks.html`         | Config. de tarefas     |
| `settings-leads.html`         | Config. de leads       |
| `settings-support.html`       | Config. de suporte     |
| `settings-finance.html`       | Config. financeiras    |
| `settings-gateways.html`      | Gateways de pagamento  |
| `settings-customers.html`     | Config. de clientes    |
| `settings-localization.html`  | Localização/idioma     |
| `settings-recaptcha.html`     | Config. reCAPTCHA      |
| `settings-miscellaneous.html` | Config. diversas       |

### 9.7 Authentication (3 variantes: Cover, Minimal, Creative)

| Tipo           | Cover                         | Minimal                         | Creative                         |
| -------------- | ----------------------------- | ------------------------------- | -------------------------------- |
| Login          | `auth-login-cover.html`       | `auth-login-minimal.html`       | `auth-login-creative.html`       |
| Register       | `auth-register-cover.html`    | `auth-register-minimal.html`    | `auth-register-creative.html`    |
| 404            | `auth-404-cover.html`         | `auth-404-minimal.html`         | `auth-404-creative.html`         |
| Reset Password | `auth-reset-cover.html`       | `auth-reset-minimal.html`       | `auth-reset-creative.html`       |
| Resetting      | `auth-resetting-cover.html`   | `auth-resetting-minimal.html`   | `auth-resetting-creative.html`   |
| Verify OTP     | `auth-verify-cover.html`      | `auth-verify-minimal.html`      | `auth-verify-creative.html`      |
| Maintenance    | `auth-maintenance-cover.html` | `auth-maintenance-minimal.html` | `auth-maintenance-creative.html` |

### 9.8 Help Center

| Arquivo                   | Descrição            |
| ------------------------- | -------------------- |
| `help-knowledgebase.html` | Base de conhecimento |

---

## 10. Estrutura de Arquivos SCSS

```
assets/scss/
├── theme.scss                          # Arquivo principal (importa tudo)
├── bootstrap/                          # Bootstrap 5 override
│   └── *.scss                          # Variáveis BS customizadas
└── themes/
    ├── _variables.scss                  # Variáveis do tema (cores, fonts, spacing)
    ├── _bs-custom-variables.scss        # Overwrites do Bootstrap
    ├── components/
    │   ├── _general.scss                # Reset, avatares, utilidades gerais
    │   ├── _alert.scss                  # Alertas soft
    │   ├── _accordion.scss              # Accordions
    │   ├── _badge.scss                  # Badges e soft badges
    │   ├── _button.scss                 # Botões + variantes light-*
    │   ├── _card.scss                   # Cards
    │   ├── _dropdown.scss               # Dropdowns
    │   ├── _form.scss                   # Forms, inputs, checkbox, radio
    │   ├── _modal.scss                  # Modais com blur
    │   ├── _navs-tabs.scss              # Nav tabs customizadas
    │   ├── _table.scss                  # Tabelas
    │   ├── _offcanvas.scss              # Offcanvas
    │   ├── _search.scss                 # Search global
    │   ├── _language.scss               # Seletor de idioma
    │   └── _miscellaneous.scss           # Pagination, img-group, ladda, etc.
    ├── layouts/
    │   ├── _nxl-common.scss             # Container, page-header, footer
    │   ├── _nxl-header.scss              # Header fixo
    │   ├── _nxl-navigation.scss         # Sidebar/navigation
    │   ├── _nxl-sidebar.scss             # Sidebar styles
    │   └── _nxl-responsive.scss         # Breakpoints e responsive
    ├── applications/
    │   ├── _apps-common.scss             # Estilos comuns de apps
    │   ├── _chat.scss                    # Chat app
    │   ├── _email.scss                   # Email app
    │   ├── _tasks.scss                   # Tasks app
    │   ├── _notes.scss                   # Notes app
    │   ├── _calendar.scss                # Calendar app
    │   └── _storage.scss                 # Storage app
    ├── pages/
    │   ├── _dashboard.scss               # Dashboard
    │   ├── _analytics.scss               # Analytics
    │   ├── _proposal.scss                # Proposals
    │   ├── _customers-view.scss          # Customer detail
    │   ├── _customers-create.scss        # Customer form
    │   ├── _projects.scss                # Projects
    │   ├── _invoice-create.scss          # Invoice form
    │   ├── _authentication.scss          # Auth pages
    │   ├── _report-sales.scss            # Reports
    │   ├── _report-leads.scss
    │   ├── _report-projects.scss
    │   ├── _report-tmesheets.scss
    │   ├── _help-knowledgebase.scss
    │   ├── _maintaince.scss
    │   └── _icon-lauouts.scss
    ├── widgets/
    │   ├── _widgets-lists.scss
    │   ├── _widgets-tables.scss
    │   ├── _widgets-charts.scss
    │   ├── _widgets-statistics.scss
    │   └── _widgets-miscellaneous.scss
    ├── plugins/
    │   ├── _pace.scss
    │   ├── _select2.scss
    │   ├── _daterange.scss
    │   ├── _dataTables.scss
    │   ├── _sweetalert2.scss
    │   ├── _jauery-steps.scss
    │   ├── _circle-progress.scss
    │   ├── _maxlength.scss
    │   ├── _tags-input.scss
    │   ├── _perfect-scrollbar.scss
    │   ├── _notification.scss
    │   ├── _lightbox.scss
    │   ├── _pnotify.scss
    │   ├── _bar-rating.scss
    │   └── _tags-input.scss
    └── options/
        ├── _theme-options.scss            # Customizer base
        ├── _theme-options-navigation.scss
        ├── _theme-options-header.scss
        ├── _theme-options-dark-theme.scss
        ├── _theme-options-font.scss
        └── _theme-options-customizer.scss
```

---

## 11. Plugins & Vendors

| Plugin                | Uso                         |
| --------------------- | --------------------------- |
| **ApexCharts**        | Gráficos e charts           |
| **Select2**           | Selects avançados           |
| **DataTables**        | Tabelas com paginação/busca |
| **SweetAlert2**       | Alerts/modais bonitos       |
| **Quill**             | Editor rich text            |
| **Date Range Picker** | Seletores de data           |
| **jQuery Steps**      | Wizards multi-step          |
| **Perfect Scrollbar** | Scrollbars customizadas     |
| **Tagify**            | Input de tags               |
| **Cleave.js**         | Máscaras de input           |
| **Circle Progress**   | Progresso circular          |
| **jQuery Validate**   | Validação de forms          |
| **jQuery Calendar**   | Calendário                  |
| **TUI Calendar**      | Calendário avançado         |
| **Vectormap**         | Mapas interativos           |
| **EmojioneArea**      | Emoji picker                |
| **Pace.js**           | Loading bars                |
| **Time-to**           | Countdowns                  |
| **Print**             | Impressão de páginas        |
| **Chance.js**         | Dados aleatórios            |

---

## 12. Iconografia

**Feather Icons** é a library de ícones principal. Todos os ícones são referenciados com a classe `feather-{nome}`.

Exemplos comuns usados no template:

- `feather-airplay` — Dashboard
- `feather-cast` — Reports
- `feather-send` — Applications
- `feather-at-sign` — Proposals
- `feather-dollar-sign` — Payment
- `feather-users` — Customers
- `feather-alert-circle` — Leads
- `feather-briefcase` — Projects
- `feather-layout` — Widgets
- `feather-settings` — Settings
- `feather-power` — Authentication
- `feather-life-buoy` — Help Center
- `feather-plus` — Criar novo
- `feather-chevron-right` — Setas de submenu
- `feather-align-left` — Toggle menu
- `feather-arrow-right` / `feather-arrow-left` — Navegação

---

## 13. Classes Utilitárias Comuns

### Spacing (Bootstrap 5)

- `.p-*`, `.px-*`, `.py-*`, `.pt-*`, `.pb-*`, `.ps-*`, `.pe-*`
- `.m-*`, `.mx-*`, `.my-*`, `.mt-*`, `.mb-*`, `.ms-*`, `.me-*`
- Valores: 0, 1, 2, 3, 4, 5

### Font Size (Custom)

- `.fs-4` (4px) até `.fs-30` (30px) e intermediários

### Font Weight

- `.fw-light` (200), `.fw-lighter` (300), `.fw-normal` (400), `.fw-medium` (500), `.fw-semibold` (600), `.fw-bold` (700), `.fw-bolder` (800), `.fw-black` (900)

### Text Transform

- `.text-uppercase`, `.text-lowercase`, `.text-capitalize`

### Backgrounds Soft

- `.bg-light-primary`, `.bg-light-success`, `.bg-light-danger`, `.bg-light-info`, `.bg-light-warning`, `.bg-light-teal`, `.bg-light-cyan`, `.bg-light-indigo`, `.bg-light-darken`

### Avatar Sizes

- `.avatar-xs` (12px), `.avatar-sm`, `.avatar-md` (padrão 40px), `.avatar-lg`, `.avatar-xl`, `.avatar-xxl`

### Width/Height Helpers

- `.wd-*` / `.ht-*` — Tamanhos customizados (5, 10, 15, 20... até 100)

### Image Group

- `.img-group` — Grupo de avatares sobrepostos (margin-left: -10px)

---

## 14. Theme Customizer (Variáveis Dinâmicas)

O template suporta personalização em tempo real via customizer:

| Opção                | Classes                                        | Descrição                       |
| -------------------- | ---------------------------------------------- | ------------------------------- |
| **Navigation Theme** | `app-navigation-light` / `app-navigation-dark` | Tema claro/escuro da sidebar    |
| **Header Theme**     | `app-header-light` / `app-header-dark`         | Tema claro/escuro do header     |
| **Skin**             | `app-skin-light` / `app-skin-dark`             | Skin geral do app               |
| **Font Family**      | `app-font-family-{name}`                       | Troca de tipografia (22 opções) |

---

## 15. Responsive Breakpoints

Baseado no Bootstrap 5:

| Breakpoint | Width     |
| ---------- | --------- |
| xs         | < 576px   |
| sm         | >= 576px  |
| md         | >= 768px  |
| lg         | >= 992px  |
| xl         | >= 1200px |
| xxl        | >= 1400px |

Comportamentos responsive:

- Sidebar colapsa em mobile (`.minimenu` ativo via JS)
- Header se adapta com hamburger menu em mobile
- Mega menu oculto em < lg (`.d-lg-none`)
- Cards empilham verticalmente
- Tabelas com scroll horizontal (`.table-responsive`)

---

## 16. Animações e Transições

```scss
// Transição global
transition: all 0.3s ease;

// Modal com blur
body.modal-open .nxl-header,
body.modal-open .nxl-navigation,
body.modal-open .page-header,
body.modal-open .nxl-container {
	filter: blur(3px);
	transition: all 0.2s linear;
}

// Fade-scale para modais
.fade-scale {
	opacity: 0;
	transform: scale(0.9);
	transition: all 0.2s linear;
	&.show {
		opacity: 1;
		transform: scale(1);
	}
}

// Dropdown header animation
@keyframes fadein {
	from {
		opacity: 0;
		transform: translate3d(0, 8px, 0);
	}
	to {
		opacity: 1;
		transform: translate3d(0, 0, 0);
	}
}

// Avatar hover
.avatar-image:hover,
.avatar-text:hover {
	transform: translateY(-4px) scale(1.07);
	transition: all 0.3s ease;
}
```

---

## 17. Checklist para Implementar em Novos Projetos

### Setup Inicial

- [ ] Definir paleta de cores primárias (substituir `$blue`, `$green`, etc.)
- [ ] Definir fonte padrão (padrão: Inter)
- [ ] Configurar `$body-bg` e `$body-color`
- [ ] Ajustar `$header-height` e `$navigation-width` se necessário

### Layout

- [ ] Implementar estrutura `nxl-navigation` + `nxl-header` + `nxl-container`
- [ ] Configurar menu lateral com `nxl-navbar` / `nxl-item` / `nxl-submenu`
- [ ] Implementar toggle de mini-menu (colapsar sidebar)
- [ ] Implementar header com mega menu e notificações

### Componentes Base

- [ ] Cards com `.card` e sombra padrão
- [ ] Botões com variante `btn-light-{color}`
- [ ] Badges com `bg-light-{color}`
- [ ] Forms com focus em primary border
- [ ] Tables com header uppercase 10px/700
- [ ] Modals com blur no fundo

### Plugins

- [ ] Instalar ApexCharts para gráficos
- [ ] Instalar Select2 para selects
- [ ] Instalar DataTables para tabelas
- [ ] Instalar SweetAlert2 para alerts
- [ ] Instalar Perfect Scrollbar para scrollbars

### Responsivo

- [ ] Testar sidebar collapse em mobile
- [ ] Verificar mega menu em < lg
- [ ] Validar cards empilhados em mobile

### Dark Mode

- [ ] Importar variáveis dark (`$dark-theme-color-*`)
- [ ] Implementar toggle `app-skin-dark`
- [ ] Verificar contraste em todos os componentes

---

## 18. Assets

### Imagens

- **Logo**: `assets/images/logo-full.png` (logo expandida), `assets/images/logo-abbr.png` (logo abreviada)
- **Favicon**: `assets/images/favicon.ico`
- **Avatars**: `assets/images/avatar/1-12.png`
- **Auth BGs**: `assets/images/auth/auth-cover-*.svg`
- **Brands**: `assets/images/brand/` (Figma, GitHub, PayPal, etc.)
- **File Icons**: `assets/images/file-icons/` (pdf, png, js, html, etc.)
- **Payment Icons**: `assets/images/payment/` (Visa, Mastercard, etc.)
- **Gallery**: `assets/images/gallery/1-12.png`

### CSS Compilado

- `assets/css/bootstrap.min.css` — Bootstrap 5 customizado
- `assets/css/theme.min.css` — Tema compilado
- `assets/vendors/css/vendors.min.css` — Plugins compilados

### JS Inicializadores

Cada página tem seu init file: `assets/js/{page}-init.min.js`

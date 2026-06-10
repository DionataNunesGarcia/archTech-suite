# ArchTech Suite — Design System

> Baseado no Duralux Admin Design System (`docs/SYSTEM_DESIGN.md`), adaptado para Next.js 15 + TailwindCSS v4.

---

## 1. Paleta de Cores

| Token                 | Hex       | Uso                                  |
| --------------------- | --------- | ------------------------------------ |
| `--color-primary`     | `#3454d1` | Ações principais, CTAs, links ativos |
| `--color-secondary`   | `#64748b` | Ações secundárias                    |
| `--color-success`     | `#25b865` | Confirmações, status positivo        |
| `--color-warning`     | `#ffa21d` | Alertas, atenção                     |
| `--color-danger`      | `#d13b4c` | Erros, ações destrutivas             |
| `--color-info`        | `#3dc7be` | Informações, dicas                   |
| `--color-body-bg`     | `#f0f2f8` | Background do corpo                  |
| `--color-body-text`   | `#4b5563` | Cor do texto padrão                  |
| `--color-brand-dark`  | `#283c50` | Títulos, textos fortes               |
| `--color-brand-muted` | `#7587a7` | Textos secundários                   |

---

## 2. Tipografia

| Propriedade | Valor                      |
| ----------- | -------------------------- |
| Font Family | Inter (400, 500, 600, 700) |
| Base Size   | `0.84rem` (13.44px)        |
| Line Height | `1.6`                      |

**Headings:** h1: 36px | h2: 28px | h3: 24px | h4: 20px | h5: 16px | h6: 15px — todos 700 weight, cor `brand-dark`.

---

## 3. Componentes

### Button

```tsx
import { Button } from '@/components/ui';
<Button variant="primary" size="md">
	Label
</Button>;
```

Variantes: `primary` | `secondary` | `success` | `warning` | `danger` | `info` | `light` | `outline`
Tamanhos: `sm` | `md` | `lg`

### Card

```tsx
import { Card } from '@/components/ui';
<Card title="Título" headerRight={<Badge>Online</Badge>}>
	Conteúdo
</Card>;
```

### Badge

```tsx
import { Badge } from '@/components/ui';
<Badge variant="success">Ativo</Badge>;
```

Variantes: `primary` | `secondary` | `success` | `warning` | `danger` | `info`

### Input

```tsx
import { Input } from '@/components/ui';
<Input label="Email" name="email" placeholder="exemplo@email.com" error="Campo obrigatório" />;
```

---

## 4. Tokens TailwindCSS v4

Definidos em `src/app/globals.css` via `@theme`:

- Cores: `primary`, `secondary`, `success`, `warning`, `danger`, `info`
- Backgrounds: `bg-primary/10` (soft), `bg-body-bg`
- Textos: `text-[10px]` até `text-[36px]`
- Radius: `rounded-xs` (3px), `rounded-sm` (5px), `rounded-md` (10px)
- Shadows: `shadow-card`, `shadow-dropdown`

---

## 5. Referência Completa

Consulte `docs/SYSTEM_DESIGN.md` para o catálogo completo de componentes (Duralux Bootstrap 5 → SCSS).
As implementações React seguem o mesmo design language.

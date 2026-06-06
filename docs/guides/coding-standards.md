# Coding Standards

Regras obrigatórias para todo código gerado no ArchTech Suite.

## Geral

- Sem abreviações em nomes: `$definition` não `$def`, `$parameters` não `$params`, `$temporary` não `$tmp`
- Exceções aceitas: `$io`, `src`, `href`, `url`, `id`, `$config`, `html`, `csv`, `api`, `sql`, `php`, `$langcode`

## PHP

- Services: `autowire` obrigatório, sempre criar interface, métodos `protected` (nunca `private`)
- Ordem de injeção: do mais geral para o mais específico (`ConfigFactoryInterface` → `EntityTypeManagerInterface`)
- Sem `final` em classes, sem `private` em métodos
- Ternary operator: sempre usar parênteses na condição: `($condition) ? true : false`
- `in_array()` sem `$strict`
- PHPDoc: usar `array` simples, sem `array<...>`, `int[]` ou `string[]`
- Comentários obrigatórios em toda função, método, classe e construtor

## Drupal

- **Config:** usar `#config_target` ao estender `ConfigFormBase`
- **Config schema:** confiar no tipo do schema em vez de adicionar casts defensivos
- **Services:** autowire + interface + métodos protected
- **Hooks:** usar OOP hooks em vez de procedural hooks
- **Markup:** usar render arrays (`['#markup' => t('...')]`) em vez de `Markup::create()`
- **Testes:** preferir 1 test method por classe quando cenários compartilham bootstrap
- **Assertions:** comentários começando com `// Check that ...`
- **Nullsafe:** evitar `?->` em testes

## PHPUnit

- `::setUp` e `::test` antes de métodos protected helper
- 1 test method por Kernel/Functional/Browser quando possível
- Foco em comportamento esperado, não em labels/markup exatos

## HTML/CSS

- HTML: returns apenas após block tags (`<p>`, `<div>`, `<ul>`, `<li>`, `<br/>`)
- CSS: namespacing de seletores por módulo

## Git

- Commits de IA devem terminar com: `AI-assisted by {code agent name}`
- Nunca force push
- Commits apenas quando explicitamente solicitado

## Python

- Usar `python3` em vez de `python`

# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: a11y.spec.ts >> Accessibility (WCAG AA) >> home page has zero WCAG AA violations
- Location: e2e/a11y.spec.ts:5:6

# Error details

```
Error: expect(received).toEqual(expected) // deep equality

- Expected  -  1
+ Received  + 93

- Array []
+ Array [
+   Object {
+     "description": "Ensure the contrast between foreground and background colors meets WCAG 2 AA minimum contrast ratio thresholds",
+     "help": "Elements must meet minimum color contrast ratio thresholds",
+     "helpUrl": "https://dequeuniversity.com/rules/axe/4.11/color-contrast?application=playwright",
+     "id": "color-contrast",
+     "impact": "serious",
+     "nodes": Array [
+       Object {
+         "all": Array [],
+         "any": Array [
+           Object {
+             "data": Object {
+               "bgColor": "#0284c7",
+               "contrastRatio": 4.09,
+               "expectedContrastRatio": "4.5:1",
+               "fgColor": "#ffffff",
+               "fontSize": "10.5pt (14px)",
+               "fontWeight": "normal",
+               "messageKey": null,
+             },
+             "id": "color-contrast",
+             "impact": "serious",
+             "message": "Element has insufficient color contrast of 4.09 (foreground color: #ffffff, background color: #0284c7, font size: 10.5pt (14px), font weight: normal). Expected contrast ratio of 4.5:1",
+             "relatedNodes": Array [
+               Object {
+                 "html": "<button type=\"submit\" class=\"w-full rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50\">Sign in</button>",
+                 "target": Array [
+                   "button",
+                 ],
+               },
+             ],
+           },
+         ],
+         "failureSummary": "Fix any of the following:
+   Element has insufficient color contrast of 4.09 (foreground color: #ffffff, background color: #0284c7, font size: 10.5pt (14px), font weight: normal). Expected contrast ratio of 4.5:1",
+         "html": "<button type=\"submit\" class=\"w-full rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50\">Sign in</button>",
+         "impact": "serious",
+         "none": Array [],
+         "target": Array [
+           "button",
+         ],
+       },
+       Object {
+         "all": Array [],
+         "any": Array [
+           Object {
+             "data": Object {
+               "bgColor": "#f9fafb",
+               "contrastRatio": 3.91,
+               "expectedContrastRatio": "4.5:1",
+               "fgColor": "#0284c7",
+               "fontSize": "10.5pt (14px)",
+               "fontWeight": "normal",
+               "messageKey": null,
+             },
+             "id": "color-contrast",
+             "impact": "serious",
+             "message": "Element has insufficient color contrast of 3.91 (foreground color: #0284c7, background color: #f9fafb, font size: 10.5pt (14px), font weight: normal). Expected contrast ratio of 4.5:1",
+             "relatedNodes": Array [
+               Object {
+                 "html": "<div class=\"flex min-h-screen items-center justify-center bg-gray-50 px-4\">",
+                 "target": Array [
+                   ".flex",
+                 ],
+               },
+             ],
+           },
+         ],
+         "failureSummary": "Fix any of the following:
+   Element has insufficient color contrast of 3.91 (foreground color: #0284c7, background color: #f9fafb, font size: 10.5pt (14px), font weight: normal). Expected contrast ratio of 4.5:1",
+         "html": "<a class=\"text-primary-600 hover:text-primary-700 font-medium\" href=\"/register\">Create one</a>",
+         "impact": "serious",
+         "none": Array [],
+         "target": Array [
+           "a",
+         ],
+       },
+     ],
+     "tags": Array [
+       "cat.color",
+       "wcag2aa",
+       "wcag143",
+       "TTv5",
+       "TT13.c",
+       "EN-301-549",
+       "EN-9.1.4.3",
+       "ACT",
+       "RGAAv4",
+       "RGAA-3.2.1",
+     ],
+   },
+ ]
```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - generic [ref=e3]:
    - generic [ref=e4]:
      - heading "OpenResume ATS" [level=1] [ref=e5]
      - paragraph [ref=e6]: Sign in to your account
    - generic [ref=e7]:
      - generic [ref=e8]:
        - generic [ref=e9]: Email
        - textbox "Email" [ref=e10]:
          - /placeholder: you@example.com
      - generic [ref=e11]:
        - generic [ref=e12]: Password
        - textbox "Password" [ref=e13]:
          - /placeholder: ••••••••
      - button "Sign in" [ref=e14] [cursor=pointer]
    - paragraph [ref=e15]:
      - text: Don't have an account?
      - link "Create one" [ref=e16] [cursor=pointer]:
        - /url: /register
  - alert [ref=e17]
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import AxeBuilder from '@axe-core/playwright';
  3  | 
  4  | test.describe('Accessibility (WCAG AA)', () => {
  5  | 	test('home page has zero WCAG AA violations', async ({ page }) => {
  6  | 		await page.goto('/');
  7  | 		const results = await new AxeBuilder({ page })
  8  | 			.withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
  9  | 			.analyze();
> 10 | 		expect(results.violations).toEqual([]);
     |                              ^ Error: expect(received).toEqual(expected) // deep equality
  11 | 	});
  12 | 
  13 | 	test('404 page has no critical violations', async ({ page }) => {
  14 | 		await page.goto('/non-existent', { waitUntil: 'networkidle' });
  15 | 		const results = await new AxeBuilder({ page })
  16 | 			.withTags(['wcag2a', 'wcag2aa'])
  17 | 			.analyze();
  18 | 		const critical = results.violations.filter(v => v.impact === 'critical');
  19 | 		expect(critical).toEqual([]);
  20 | 	});
  21 | });
  22 | 
```
# Accessibility — Payment Gateway for Redsys & WooCommerce Lite

> Target: WCAG 2.2 AA floor, AAA where feasible (D-007). The automated pass below has been run and is repeatable; the guided assistive-technology pass has NOT been run yet. An automated scan covers part of WCAG only, so nothing here says the plugin "is accessible".

## Scope
- Admin settings screens (WooCommerce → Settings → Payments → [gateway]) — rendered by WooCommerce's own Settings API, so the markup of every field is WooCommerce's; this plugin contributes the field labels and descriptions, its two admin notices (`assets/css/redsys-notice.css`) and its About page (`about-redsys.php`, `includes/class-redsys-lite-apps-plugins.php`, `assets/css/welcome.css`).
- Checkout UI — classic checkout (WooCommerce-rendered) and Blocks checkout (`includes/blocks/*-support.php` + `assets/js/frontend/blocks.js`), where this plugin contributes the gateway's title, description, icon and radio option per payment method, and the payment form on the order-pay page for the card, Bizum and Google Pay gateways.

## Automated pass
**Built and run (S-029, D-067).** `tests/e2e/accessibility.spec.js` runs axe-core (4.14) in the playground through Playwright, limited to the region the plugin renders on each screen, with the rules tagged WCAG 2.0, 2.1 and 2.2 at levels A and AA. Command: `npx playwright test tests/e2e/accessibility.spec.js`. The raw result of every scan is written to `test-results/a11y/` (ignored by Git).

Environment of the run of 2026-10-06: WordPress 7.0, WooCommerce 7.4, PHP 7.4, Chromium, the default theme of the playground, English.

| Screen | State | Region scanned | Result |
|---|---|---|---|
| Classic checkout | each of the four gateways selected, billing country Spain | the gateway's row (`li.payment_method_<id>`) | no violation |
| Order-pay page | the payment form of the card, Bizum and Google Pay gateways | `#redsys_payment_form` | no violation |
| Blocks checkout | each of the four payment options selected in turn | the payment-method block | no violation |
| Gateway settings | the four gateway screens, as loaded | `#mainform`, without the plugin notices | no violation; axe could not decide two rules on WooCommerce's own field markup (below) |
| Admin notices | both notices shown, on the dashboard | `.woocommerce-redsys-messages` | 4 nodes, colour contrast — recorded, not fixed (below) |
| About page | as loaded | `.about-wrap-redsys` | 32 nodes, colour contrast — fixed in S-056; no violation after |

### Found and fixed
- **About page, colour contrast (WCAG 1.4.3), 32 nodes.** The host name under each card was `#8c8f94` on white at 11.5px (3.24:1), and three of the coloured initials were between 3.9:1 and 4.45:1. Fixed in S-056: the host name is `#646970` (5.53:1) and the three text colours are darker; every colour pair declared in the page's data is now at or above 4.5:1.

### Found and not fixed
- **Admin notices, colour contrast, 4 nodes (S-057, deferred).** The four link buttons of the "new version" notice use WordPress's `button-primary` class inside a WooCommerce message. On WooCommerce 7.4 that stylesheet gives them white text with a text shadow that axe measures at 4.22:1. The colours are WooCommerce's, not this plugin's; the plugin chose the classes. Giving the buttons their own colours is a visual decision for the owner, and the result on a current WooCommerce has not been observed. The spec lists this one item as known, by rule and selector, so it does not hide anything else.

### Not decided by the tool
- Gateway settings: axe reports "needs review" for colour contrast on WooCommerce's selects and textarea, and for multiple labels on WooCommerce's checkboxes (the Settings API prints a label in the heading cell and wraps the checkbox in another). Both are WooCommerce markup.
- About page: 21 nodes whose contrast axe could not compute (text over a background it cannot resolve). Not looked at by hand.

### Risk areas recorded at adoption
- Text alternatives for the payment-method icons: the scans of the gateway rows (classic and Blocks) pass axe's image rules, so each icon has a text alternative or is marked decorative. What a screen reader actually announces for each row is for the guided pass.
- Labels of the settings fields: no violation; the plugin defines no custom field type.

### Not covered by the automated pass
- States: an error notice on the checkout (for example a Bizum or Inespay order above the transaction limit), the order-received page, the refused-payment return, the test-mode banners, the order screen's payment details box.
- A current WooCommerce and WordPress, another theme, the Spanish locale, a narrow viewport, a dark or high-contrast scheme.
- Everything axe cannot test: keyboard order, focus visibility in use, the meaning of what is announced, timing (the payment form submits itself after a moment — WCAG 2.2.1 and 3.2.5 need a person to judge it).

## Guided assistive-technology pass
**Not run (S-030).** It needs a person with a screen reader (`ASSISTIVE-TECH`); the assistant cannot drive it. The script below is ready: the assistant gives one step at a time and records the answer per item.

Preparation: the playground running (`docs/playground.md`), a product in the cart, VoiceOver (macOS, Safari) or NVDA (Windows, Firefox).

1. Keyboard only, no screen reader. Classic checkout (`/checkout/`), billing country Spain. Tab to the payment methods. Can each of the four be reached and selected with the arrow keys or Space? Is the focus always visible? After selecting one, does focus stay on it?
2. Same page, screen reader on. For each payment method: what is announced? Expected: the gateway's name once, its position in the group, selected or not. Is the icon announced, and does it add noise (the name twice) or information?
3. Select each method: is its description announced, or reachable right after the radio?
4. Place the order with the card gateway. On the order-pay page: is the message read? The form submits itself after a moment — was there time to understand what is happening before the page left? Is the "Pay" button reachable and named? Is the "Cancel order" link reachable and named?
5. Repeat step 4 with Bizum and with Google Pay.
6. Blocks checkout (`/checkout-blocks/`): repeat steps 1 to 3.
7. Bizum with a total above its limit (set a low limit in the Bizum settings first): choose Bizum and place the order. Is the error announced without moving focus by hand? Is it clear what to do next?
8. Admin, keyboard and screen reader: WooCommerce → Settings → Payments → Redsys. Is every field announced with its label and its description? Is the "Logo" field's purpose clear?
9. Admin notices: are the two notices announced as notices? Can each be dismissed from the keyboard, and is the dismiss control named?
10. About page: do the headings give the page a structure that can be navigated by heading? Is each card announced as one link with a meaningful name? Do the links that open a new tab say so?
11. Zoom the browser to 200% on the classic checkout and on the About page: is anything cut off or overlapping?

## Known limits of this record
One environment, one theme, one language. The automated result is evidence for the regions and states in the table and for nothing else.

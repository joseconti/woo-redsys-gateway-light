# Getting started

> The shortest path from an installed plugin to a first test payment, and how to check that a payment was really verified. Written from the code and from `docs/playground.md` on 2026-10-06.
>
> What has and has not been proven in this project: building the signed payment form and rejecting or accepting notifications are covered by automated tests. A complete round trip against the real Redsys or Inespay test environment has **not** been run here — it needs credentials issued to a merchant, which this project does not hold. The steps that depend on it are marked.

## 1. Collect the credentials

From your bank, for the Redsys **test** environment:

- the merchant code (FUC),
- the terminal number,
- the SHA-256 signing secret for test mode.

Bizum and wallet payments must be enabled by the bank on that terminal before those gateways can work. For Inespay, the API key and API token of a sandbox project.

Keep these out of tickets, chats and screenshots. They are entered once, on the settings screen.

## 2. Configure the card gateway in test mode

WooCommerce → Settings → Payments → "Redsys Lite":

1. Tick **Enable/Disable**.
2. Fill in **Commerce number (FUC)**, **Commerce Name** and **Terminal number**.
3. Paste the test secret into **TEST MODE: Encryption secret passphrase SHA-256**.
4. Leave **Running in test mode** ticked — it is ticked by default.
5. Save.

The store currency must be one Redsys accepts, or the gateway switches itself off. Every setting is described in `docs/usage/configuration.md`.

While test mode is on, the checkout shows a warning banner to every visitor. That is the reminder to turn it off before going live.

## 3. Place a test order

1. Add a product to the cart and go to the checkout.
2. Choose the Redsys payment method and place the order.
3. You land on the order-pay page for a moment and are forwarded to the Redsys test page. The address must be on the Redsys **test** host.
4. Pay with the test card data your bank gave you. *(Depends on real test credentials — not run in this project.)*
5. Redsys sends you back to the store's order-received page.

## 4. Check that the payment was verified

A payment counts only when the **notification** from Redsys was accepted — not because the customer came back to the store. Check all three:

1. **Order status.** WooCommerce → Orders: the order has left "Pending payment".
2. **Order notes.** The order shows "HTTP Notification received - payment completed" and "Authorization code: …".
3. **Payment details.** The order screen shows the Redsys order number, date, hour and authorisation code under the billing address.

If the order is still "Pending payment":

| Symptom | Likely cause | What to do |
|---------|--------------|------------|
| No note at all | The notification did not reach the site | Check that `https://<your-site>/?wc-api=WC_Gateway_redsys` is reachable from the internet; if Redsys does not accept the site's certificate, try **HTTPS SNI Compatibility**. |
| Debug log says "no SHA256 secret configured" | No secret for the active mode | Fill in the secret; see "Test mode and live mode" in `docs/usage/configuration.md`. |
| Debug log says "Received INVALID notification" | The secret does not match the one Redsys signs with | Re-enter the secret exactly as issued, for the right mode and the right terminal. |
| Order is "On hold" with an amount note | The amount Redsys reported differs from the order total | Check the order by hand before shipping. |
| Order is "Cancelled" | Redsys denied the payment | Nothing to fix; the customer can try again. |

To see the log, tick **Debug Log**, repeat the payment and open WooCommerce → Status → Logs, source `redsys`. The log contains secrets; see the warning in `docs/usage/configuration.md`, and untick the option afterwards.

## 5. Check that a forged notification is refused

This needs no credentials and can be run on any site, including production, because it changes nothing:

```bash
curl -i -X POST "https://<your-site>/?wc-api=WC_Gateway_redsys" \
  --data-urlencode "Ds_SignatureVersion=HMAC_SHA256_V1" \
  --data-urlencode "Ds_MerchantParameters=<any-base64-text>" \
  --data-urlencode "Ds_Signature=<not-a-valid-signature>"
```

The answer must be the text "Do not access this page directly (Redsys redirección Lite)", and no order may change. The same check for the other gateways, with their own URLs, is in `docs/reference/endpoints.md`.

## 6. Go live

For each gateway you use:

1. Fill in the **live** secret in **Encryption secret passphrase SHA-256**, and the live merchant code and terminal if they differ from the test ones.
2. Untick **Running in test mode**.
3. Untick **Debug Log**.
4. Place one real, small order and repeat the checks in step 4.

## The other gateways

- **Bizum:** same steps on the "Bizum Lite" screen, with its own merchant code, terminal and secrets. Optional: a transaction limit.
- **Google Pay redirection:** same steps on its own screen. **In test mode it does not appear at checkout** unless the stored setting `testshowgateway` has been written by hand, because the settings screen has no field for it — see `docs/usage/configuration.md` ("Stored keys with no field on the settings screen") and `docs/playground.md` for the command the playground uses.
- **Inespay:** enter the API key and API token, keep test mode on for the sandbox. It is offered only to customers in Spain, Portugal or Italy. Orders must be in EUR: a confirmed payment for an order in another currency is put on hold instead of completed. Check a payment by the order note "Inespay payment completed. ID: …, Status: …".

## Without any credentials: the local playground

Everything except the real round trip can be run locally:

```bash
npx wp-env start
npx playwright test
```

The one-time setup the tests need (permalinks, gateway settings, test pages) is listed in `docs/playground.md`, "Automated checkout smoke test". The tests stop every request to Redsys before it leaves the machine and replace the Inespay API with a local stub, so they prove the plugin's own half of each flow and nothing about the processors.

## Related
- `docs/usage/configuration.md`, `docs/usage/examples.md`
- `docs/flows/` — each journey step by step.

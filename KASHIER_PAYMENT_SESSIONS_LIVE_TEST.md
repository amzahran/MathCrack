# Kashier Payment Sessions live setup and test

## Production environment

Set these non-secret runtime values and rebuild Laravel's config cache:

```dotenv
APP_URL=https://mathcrack.com
KASHIER_MODE=live
KASHIER_CURRENCY=EGP
KASHIER_PAYMENT_SESSIONS_URL=https://api.kashier.io/v3/payment/sessions
KASHIER_ALLOWED_METHODS=card,wallet
KASHIER_SESSION_TTL_MINUTES=30
KASHIER_WEBHOOK_URL=https://mathcrack.com/kashier-webhook
```

The existing Kashier gateway settings supply the credentials:

- `KASHIER_ACCOUNT_KEY`: Kashier merchant ID (`MID`).
- `KASHIER_IFRAME_KEY`: Payment API key, sent as the `api-key` header and used
  for webhook signature validation.
- `KASHIER_TOKEN`: secret key, sent as the `Authorization` header.

Do not put credential values in source control, logs, support screenshots, or
this document.

## Webhook configuration

The application endpoint that Kashier must call is:

```text
POST https://mathcrack.com/kashier-webhook
```

It is separate from the authenticated browser redirect route. It must be
publicly reachable without a login, locale redirect, CDN challenge, or CSRF
token.

Each Payment Sessions request sends this URL in `serverWebhook`. Kashier also
supports saving one fixed merchant webhook URL with:

```text
PUT https://api.kashier.io/merchant?action=webhook&operation=updatemerchantuser
```

That `api.kashier.io` URL is Kashier's management API. It is used from Kashier's
dashboard/integration setup or as a one-time authenticated configuration call;
it is not a URL that Kashier calls on MathCrack, and it is not used during
normal payment creation by this application.

For the live merchant, configure its `webhookUrl` value as
`https://mathcrack.com/kashier-webhook`. Never paste the secret key into logs or
support messages.

## Safe live test

1. Deploy the commit and clear/rebuild Laravel's config cache.
2. Confirm `APP_URL` and `KASHIER_WEBHOOK_URL` resolve to HTTPS URLs on
   `mathcrack.com`.
3. Confirm a direct unauthenticated `POST` to `/kashier-webhook` reaches Laravel
   and returns HTTP 200. An unsigned probe must not update an invoice.
4. In Kashier's live merchant settings, confirm the stored webhook URL exactly
   matches `https://mathcrack.com/kashier-webhook`.
5. Sign in as a test student and start a low-value purchase for each application
   flow as applicable: single test, all course tests, lecture/course, and live.
6. In the embedded Kashier session, confirm both Card and Wallet appear. Confirm
   the browser URL loaded in the frame is under `payments.kashier.io/session/`.
7. Complete one low-value Card transaction. Confirm the signed webhook or
   Kashier's verified browser response changes only the matching invoice from
   pending to paid, and the success page loads.
8. Start a new low-value Wallet transaction with an active Egyptian wallet.
   Request the SMS, enter the received OTP, and complete payment.
9. Confirm Kashier sends a signed `pay` event to the MathCrack webhook with the
   session's merchant order ID and that only the matching invoice becomes paid.
10. Confirm a cancelled, failed, or abandoned session leaves the invoice
    pending/failed and never marks it paid without a valid webhook or verified
    provider response.
11. Repeat the paid return with missing browser query parameters to confirm the
    existing pending-invoice fallback still reaches the paid success page after
    the webhook has completed.

Record only safe diagnostics: timestamp, invoice ID, merchant order ID, session
ID, payment method, status, and HTTP status. Do not record API keys, secret
keys, signatures, card data, OTPs, or complete phone numbers.

## Rollback behavior

The legacy `kashier-checkout.js` template remains in the repository because it
is vendor-package compatibility code, but the application no longer calls it
for Kashier payment creation. There is intentionally no automatic fallback to
the legacy flow: creating a second checkout for the same purchase risks
duplicate/conflicting orders, and Kashier has confirmed that flow is not the
supported Wallet path.

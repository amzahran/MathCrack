# Kashier Payment Sessions authentication review

## Finding

The failed implementation used the header names and format currently shown in
Kashier's official Payment Sessions documentation:

```text
Authorization: <secret key>
api-key: <Payment API key>
```

The documentation does not show a `Bearer` prefix, `x-api-key`, an HMAC request
signature, or MID-only authentication for creating a session. `merchantId`
(MID) is also sent in the JSON body.

Official references:

- https://developers.kashier.io/payment/payment-sessions
- https://developers.kashier.io/webhooks/setup
- https://developers.kashier.io/webhooks/endpoints

Because the header shape matched the docs, HTTP 401 most likely means one or
both supplied values were not the live Payment Sessions credentials accepted
for that MID/store. Other plausible causes are test/live credential mixing, a
revoked key, or Payment Sessions API access not being enabled for that key.

## Existing setting mapping

No credential values were read into this report.

- `KASHIER_ACCOUNT_KEY` is used as the merchant ID (MID).
- `KASHIER_IFRAME_KEY` is the legacy hosted checkout/payment API key. It also
  validates webhook signatures.
- `KASHIER_TOKEN` is the legacy API authorization value used by the package's
  order verification request.

Those uses make the old names plausible compatibility candidates, but the names
alone do not prove they are the live Secret Key and Payment API Key authorized
for Payment Sessions v3.

## Safe rollout

Hosted checkout remains the default:

```dotenv
KASHIER_USE_PAYMENT_SESSIONS=false
```

To perform a controlled Payment Sessions test, obtain the live values directly
from Kashier's dashboard Integrations section or support and set:

```dotenv
KASHIER_USE_PAYMENT_SESSIONS=true
KASHIER_PAYMENT_SESSIONS_SECRET_KEY=<live Payment Sessions Secret Key>
KASHIER_PAYMENT_SESSIONS_API_KEY=<live Payment API Key>
KASHIER_PAYMENT_SESSIONS_URL=https://api.kashier.io/v3/payment/sessions
KASHIER_ALLOWED_METHODS=card,wallet
KASHIER_WEBHOOK_URL=https://mathcrack.com/kashier-webhook
```

If the two explicit Payment Sessions credential variables are absent, the code
can use `KASHIER_TOKEN` and `KASHIER_IFRAME_KEY` as compatibility fallbacks and
logs only the fallback credential names, never their values.

After changing environment values, rebuild Laravel's configuration cache. Test
with a low-value controlled transaction. If authentication fails, turn the flag
off; the next checkout uses the unchanged hosted flow. Do not automatically
retry a failed session request through hosted checkout in the same purchase
attempt because an uncertain API outcome could create conflicting orders.

## Support question

Send Kashier Support this question without attaching keys, signatures, phone
numbers, or card data:

> For our live merchant, `POST https://api.kashier.io/v3/payment/sessions`
> returned HTTP 401. Your Payment Sessions documentation shows the Secret Key
> as a raw `Authorization` header and the Payment API Key as the `api-key`
> header, with MID in `merchantId` (no Bearer prefix, x-api-key, or request
> HMAC). Please confirm which exact dashboard credential labels must be used for
> those two headers, whether they are different from the credentials used by
> the legacy hosted checkout and order-status API, and whether Payment Sessions
> v3 access must be enabled or allowlisted separately for our live MID/store.
> Can you also confirm whether the 401 indicates the Authorization key, the
> api-key, the MID/key association, or environment mismatch?

Provide Kashier only a safe timestamp and merchant order ID from the failed
attempt if they need to trace it.

# Receipt Status: Diskrepanz zwischen PHP-Validierung und JSON-Schema

## Status: BEHOBEN (2026-02-17, Branch feature/receipt-status-expansion)

---

## Problem (war)

Die `Receipt`-Klasse akzeptierte nur 4 Status (`draft`, `pending`, `completed`, `cancelled`),
waehrend das JSON-Schema und der ameax-Server 13 Status kennen, die je nach Belegtyp
unterschiedlich gelten.

## Loesung

### PHP-Konstanten erweitert

Alle 13 Server-Status als Konstanten definiert:
`STATUS_DRAFT`, `STATUS_ON_HOLD`, `STATUS_READY_FOR_DISPATCH`, `STATUS_IN_PROGRESS`,
`STATUS_OUTSTANDING_PAYMENT`, `STATUS_COMPLETED`, `STATUS_CANCELLATION`, `STATUS_OUTSTANDING`,
`STATUS_OBSOLET`, `STATUS_REFUSED`, `STATUS_ACCEPTED`, `STATUS_CANCELLED`, `STATUS_PAUSED`

Plus `STATUS_PENDING` (deprecated, fuer Abwaertskompatibilitaet).

### Typspezifische Status-Zuordnung

Neue Konstante `STATUSES_BY_TYPE` definiert die erlaubten Status je Belegtyp
(entspricht `ReceiptStatusEnum::availableByType()` auf dem Server):

| Belegtyp | Erlaubte Status |
|----------|----------------|
| offer | draft, outstanding, accepted, obsolet, refused |
| order | draft, in_progress, completed, cancelled |
| invoice | draft, read_for_dispatch, on_hold, outstanding, completed |
| credit_note | draft, read_for_dispatch, on_hold, outstanding, completed |
| cancellation_document | draft, read_for_dispatch, on_hold, outstanding, completed |

### Abwaertskompatibilitaet

- `STATUS_PENDING` bleibt als `@deprecated` Konstante
- `setStatus('pending')` wirft keinen Fehler
- Aber: Der Server wird `pending` ablehnen (nicht in `ReceiptStatusEnum`)

### Neue Hilfsmethode

`Receipt::validStatusesForType(string $type): array` — gibt die erlaubten Status fuer
einen Belegtyp zurueck.

---

## Fehlende Felder im Receipt-Schema (unveraendert)

### Waehrung (Currency)

Kein `currency`-Feld im Receipt-Model oder Schema vorhanden. Der Connector legt die
Waehrung aktuell in `custom_data.currency` ab (z.B. "EUR", "CHF"), aber das ist nur
ein Freitextfeld ohne Logik auf der ameax-Seite.

### Adressdaten

Kein Feld fuer direkte Adressdaten (Firma, Strasse, PLZ, Ort etc.) am Beleg. Ein Receipt
referenziert den Kunden ausschliesslich ueber `customer_number` oder `customer_external_id`.
Es gibt keine Moeglichkeit, Adressdaten direkt am Beleg mitzugeben.

# Form Schema

Versioning
- `schema.version`: '1.0'
- Backward-compatible evolution via additive fields.

## Top-level
```
{
  "id": 123,
  "title": "Form Title",
  "description": "",
  "settings": {
    "currency": "USD",
    "layout": {
      "columns": "two",
      "labelPlacement": "top",
      "descriptionPlacement": "below",
      "validationSummary": true,
      "requiredIndicator": "text",
      "cssClass": "",
      "markupVersion": 2
    },
    "spam": {
      "honeypot": { "enabled": true, "action": "spam" },
      "speedCheck": { "enabled": true, "thresholdMs": 2000, "mode": "normal" }
    },
    "restrictions": {
      "limit": { "enabled": false, "count": 0, "period": "" },
      "schedule": { "enabled": false, "startISO": "", "endISO": "", "pendingMessage": "", "expiredMessage": "" },
      "requireLogin": { "enabled": false, "message": "" }
    },
    "saveContinue": { "enabled": true, "buttonText": "Save and Continue" },
    "tax": {
      "mode": "manual",
      "priceMode": "exclusive",
      "addressSource": "billing",
      "nexus": [ { "country": "US" }, { "country": "GB" } ],
      "rules": [
        { "region": { "country": "US", "state": "CA", "zip": "*" }, "rate": 0.0925, "targets": ["products","options"], "shippingTaxable": false }
      ]
    },
    "coupons": { "enabled": true },
    "privacy": {
      "ipLogging": true,
      "retention": { "ipDays": 365, "spamDays": 30, "traceDays": 30, "paymentLogDays": 90 },
      "logRedaction": true
    },
    "a11y": { "focusVisible": true, "targetMinSize": 24, "errorSummary": true }
  },
  "pages": [ Page ],
  "notifications": { "id": Notification },
  "confirmations": { "id": Confirmation }
}
```

## Page
```
{
  "id": "p1",
  "title": "Page 1",
  "progressLabel": "Page 1",
  "navigation": {
    "next": { "text": "Next", "conditionalLogic": ConditionalLogic? },
    "prev": { "text": "Previous", "conditionalLogic": ConditionalLogic? }
  },
  "conditionalLogic": ConditionalLogic?,
  "sections": [ Section ]
}
```

## Section
```
{
  "id": "s1",
  "label": "Contact Info",
  "displayOnly": true,
  "headingLevel": 2,
  "description": "",
  "conditionalLogic": ConditionalLogic?,
  "fields": [ Field ]
}
```

## Field (common)
```
{
  "id": 3,
  "type": "email|text|textarea|select|checkbox|radio|name|address|date|time|website|password|fileupload|list|multiselect|html|hidden|section|page|consent|image_choice|product|quantity|options|shipping|total|price_user",
  "label": "Email",
  "isRequired": true,
  "placeholder": "",
  "helpText": "",
  "size": "medium",
  "cssClass": "",
  "adminOnly": false,
  "allowsPrepopulate": false,
  "noDuplicates": false,
  "inputs": [ { "id": "3.1", "label": "Primary" } ],
  "choices": [ { "text": "Yes", "value": "yes", "isSelected": false, "price": null } ],
  "defaultValue": "",
  "conditionalLogic": ConditionalLogic?,
  "meta": { "validation": { "pattern": "", "min": null, "max": null } }
}
```

## ConditionalLogic
```
{
  "action": "show|hide|disable|readonly|toggle_required",
  "match": "all|any",
  "rules": [
    { "source": { "fieldId": 3 }, "operator": "is|isnot|<|>|contains|starts_with|ends_with", "value": "Yes" }
  ],
  "groups": [ ConditionalLogic? ]
}
```

## Pricing & Coupons
- Product types: single price, user-defined price
- Options: checkbox/select/radio variants with price deltas
- Quantity: numeric with limits
- Shipping: flat or choice-based (optional in v1)
- Coupons: `{code,type:'percent'|'amount',value, validFrom, validTo, usageLimit, scope:'all'|'productIds'}`

## Notifications
```
{
  "id": "admin",
  "to": "{admin_email}",
  "fromName": "BITS",
  "fromEmail": "no-reply@example.com",
  "replyTo": "{Email:3}",
  "subject": "New Entry: {form_title}",
  "message": "<p>Thanks {Name:2}</p>",
  "format": "html",
  "attachUploads": true,
  "conditionalLogic": ConditionalLogic?
}
```

## Confirmations
```
{
  "id": "c1",
  "type": "message|page|redirect",
  "message": "<p>Thanks!</p>",
  "pageId": null,
  "redirectUrl": null,
  "queryString": "entry={entry_id}",
  "conditionalLogic": ConditionalLogic?
}
```

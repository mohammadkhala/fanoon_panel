# Orders List API (Admin)

API لتطبيق فلتر الويب — قائمة الطلبات بصيغة JSON.

## Endpoint

```
GET /admin/orders/list/{status}
```

**مثال:** `GET /admin/orders/list/all`

## التوثيق

- نفس جلسة لوحة الإدارة (Admin session).
- يجب إرسال الهيدر: `Accept: application/json` للحصول على JSON بدلاً من HTML.

## Query Parameters

| Parameter   | Type   | Description                          |
|------------|--------|--------------------------------------|
| `search`   | string | بحث في id, order_status, payment_status |
| `start_date` | date | تاريخ البداية (yyyy-mm-dd)           |
| `end_date` | date   | تاريخ النهاية (yyyy-mm-dd)           |
| `per_page` | int    | عدد النتائج في الصفحة (افتراضي من config) |
| `page`     | int    | رقم الصفحة                           |

## Response (200 OK)

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "order_status": "pending",
      "payment_status": "unpaid",
      "order_amount": 150.50,
      "order_type": "delivery",
      "created_at": "2025-01-15T10:30:00+00:00",
      "customer": {
        "id": 1,
        "name": "أحمد محمد",
        "phone": "0599123456"
      },
      "branch": {
        "id": 1,
        "name": "الفرع الرئيسي"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 25,
    "total": 120
  },
  "filters": {
    "status": "all",
    "search": null,
    "start_date": null,
    "end_date": null
  }
}
```

## قيم status

- `all` — جميع الطلبات
- `pending`, `confirmed`, `processing`, `out_for_delivery`, `delivered`, `returned`, `failed`, `canceled`

## مثال من تطبيق فلتر الويب

```javascript
const response = await fetch('/admin/orders/list/all?search=123&per_page=10', {
  headers: { 'Accept': 'application/json' },
  credentials: 'include'
});
const json = await response.json();
```

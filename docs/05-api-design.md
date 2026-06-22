# 05 - API Design

## หลักการ RESTful API

```
GET    → ดึงข้อมูล (ไม่เปลี่ยนแปลงข้อมูล)
POST   → สร้างข้อมูลใหม่
PUT    → แก้ไขข้อมูลทั้งหมด
PATCH  → แก้ไขข้อมูลบางส่วน
DELETE → ลบข้อมูล
```

---

## Auth API

| Method | URL | Description |
|--------|-----|-------------|
| POST | /api/auth/register | สมัครสมาชิก |
| POST | /api/auth/login | เข้าสู่ระบบ |
| POST | /api/auth/logout | ออกจากระบบ |
| GET  | /api/auth/me | ดูข้อมูลตัวเอง |

### POST /api/auth/register
```json
Request:
{
  "name": "Attawat",
  "email": "att@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response 201:
{
  "message": "Register successful",
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Attawat",
    "email": "att@example.com",
    "role": "customer"
  }
}
```

---

## Product API

| Method | URL | Description |
|--------|-----|-------------|
| GET | /api/products | ดูสินค้าทั้งหมด (+ filter, search, paginate) |
| GET | /api/products/{id} | ดูสินค้าชิ้นนึง |
| POST | /api/admin/products | เพิ่มสินค้า (Admin) |
| PUT | /api/admin/products/{id} | แก้ไขสินค้า (Admin) |
| DELETE | /api/admin/products/{id} | ลบสินค้า (Admin) |

### GET /api/products
```json
Request Query: ?category=skincare&search=vitamin&page=1

Response 200:
{
  "data": [
    {
      "id": 1,
      "name": "Vitamin C Serum",
      "price": 590.00,
      "image_url": "https://...",
      "stock": 50,
      "category": "Skincare"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 15
  }
}
```

---

## Order API

| Method | URL | Description |
|--------|-----|-------------|
| POST | /api/orders | สร้าง Order |
| GET | /api/orders | ดู Order ของตัวเอง |
| GET | /api/orders/{id} | ดู Order รายละเอียด |
| PATCH | /api/orders/{id}/cancel | ยกเลิก Order |
| PATCH | /api/admin/orders/{id}/status | อัปเดตสถานะ (Admin) |

### POST /api/orders
```json
Request:
{
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 3, "quantity": 1 }
  ],
  "shipping_name": "Attawat",
  "shipping_phone": "085-613-2931",
  "shipping_address": "123 KKU Road, Khon Kaen",
  "payment_method": "promptpay",
  "note": ""
}

Response 201:
{
  "order_id": 42,
  "status": "pending_payment",
  "total_amount": 1470.00,
  "payment": {
    "method": "promptpay",
    "qr_code_url": "https://..."
  }
}
```

---

## Payment API

| Method | URL | Description |
|--------|-----|-------------|
| POST | /api/payments/webhook/stripe | Stripe Webhook |
| POST | /api/payments/webhook/promptpay | PromptPay Webhook |

---

## Admin Dashboard API

| Method | URL | Description |
|--------|-----|-------------|
| GET | /api/admin/dashboard | ภาพรวม Stats |
| GET | /api/admin/orders | ดู Order ทั้งหมด |
| GET | /api/admin/products/low-stock | สินค้าใกล้หมด |

### GET /api/admin/dashboard
```json
Response 200:
{
  "total_revenue": 125000.00,
  "total_orders": 342,
  "pending_orders": 12,
  "low_stock_products": 5,
  "revenue_chart": [
    { "date": "2025-01-01", "revenue": 4500.00 },
    { "date": "2025-01-02", "revenue": 6200.00 }
  ],
  "top_products": [
    { "name": "Vitamin C Serum", "sold": 89 }
  ]
}
```

---

## Error Response Format (ทุก API ใช้เหมือนกัน)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

## HTTP Status Codes ที่ใช้

| Code | ความหมาย |
|------|----------|
| 200 | OK — สำเร็จ |
| 201 | Created — สร้างสำเร็จ |
| 400 | Bad Request — ข้อมูลผิด |
| 401 | Unauthorized — ยังไม่ Login |
| 403 | Forbidden — ไม่มีสิทธิ์ |
| 404 | Not Found — ไม่พบข้อมูล |
| 422 | Unprocessable — Validation ไม่ผ่าน |
| 500 | Server Error — พังฝั่ง Server |
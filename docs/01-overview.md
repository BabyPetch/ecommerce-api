# 01 - System Overview

## ระบบทำงานยังไง?

ระบบ E-Commerce นี้มี 2 ฝั่งหลัก

```
ลูกค้า (Customer)     ร้านค้า (Admin)
      │                     │
   ดูสินค้า            จัดการสินค้า
   หยิบใส่ตะกร้า       ดู Order
   จ่ายเงิน            จัดการ Stock
   ติดตาม Order        ดู Dashboard
```

---

## User Flow (ลูกค้า)

```
1. สมัคร/Login
        ↓
2. เลือกสินค้า → ใส่ตะกร้า
        ↓
3. Checkout → กรอกที่อยู่
        ↓
4. จ่ายเงิน (PromptPay / Stripe)
        ↓
5. ระบบตัด Stock อัตโนมัติ
        ↓
6. Order Status: PENDING → PAID → SHIPPING → COMPLETED
        ↓
7. ลูกค้าติดตามพัสดุได้
```

---

## Admin Flow

```
1. Login (Admin only)
        ↓
2. เพิ่ม/แก้ไข สินค้า + หมวดหมู่
        ↓
3. เห็น Order ใหม่เข้ามา
        ↓
4. กด Processing → จัดของ → กด Shipped
        ↓
5. ดู Dashboard: ยอดขาย, สินค้าขายดี, Stock ใกล้หมด
```

---

## Data Flow

```
Frontend (React)
      │  ส่ง HTTP Request
      ▼
Backend (Laravel API)
      │  ตรวจสอบ / ประมวลผล
      ▼
Database (PostgreSQL)
      │  เก็บข้อมูลถาวร
      ▼
Payment Gateway (Stripe/PromptPay)
      │  Webhook แจ้งกลับมาว่าจ่ายสำเร็จ
      ▼
Backend อัปเดต Order Status
```

---

## ตัวอย่างชีวิตจริง: ลูกค้าซื้อครีม 1 กระปุก

```
1. กดสั่งซื้อ
   → Backend สร้าง Order (status: PENDING_PAYMENT)

2. กดจ่าย PromptPay
   → Backend ติดต่อ Payment Gateway
   → แสดง QR Code ให้ลูกค้าสแกน

3. ลูกค้าสแกนจ่ายเงิน
   → Payment Gateway ส่ง Webhook มาบอก Backend
   → Backend อัปเดต Order → PAID
   → ตัด Stock ครีม -1

4. Admin เห็น Order ใหม่
   → จัดของ → กด Shipped
   → ลูกค้าได้รับ Notification

5. ลูกค้าได้ของ → กด Completed
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 + PostgreSQL |
| Frontend | React + Vite + MUI |
| Auth | Laravel Sanctum |
| Payment | Stripe + PromptPay |
| Storage | Cloudinary |
| Deploy | Railway (backend) + Vercel (frontend) |
| Docs | Markdown ใน /docs |
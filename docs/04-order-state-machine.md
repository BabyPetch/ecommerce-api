# 04 - Order State Machine

## ทำไมต้องมี State?

ถ้าไม่มี State Machine จะเกิดปัญหาแบบนี้

```
❌ Admin กด Shipped ทั้งที่ยังไม่ได้รับเงิน
❌ ลูกค้า Cancel Order ที่ส่งของไปแล้ว
❌ ระบบตัด Stock ทั้งที่ยังไม่ได้จ่าย
```

State Machine คือการกำหนดว่า **สถานะไหนไปต่อได้แค่ไหน**

---

## States ทั้งหมด

```
PENDING_PAYMENT → รอชำระเงิน (เพิ่งสร้าง Order)
PAID            → จ่ายเงินแล้ว รอร้านจัดของ
PROCESSING      → ร้านกำลังจัดของ
SHIPPING        → อยู่ระหว่างจัดส่ง
COMPLETED       → ลูกค้าได้รับของแล้ว
CANCELLED       → ยกเลิก
```

---

## Transition ที่อนุญาต

```
PENDING_PAYMENT → PAID         (เมื่อ Payment สำเร็จ)
PENDING_PAYMENT → CANCELLED    (ลูกค้า Cancel หรือหมดเวลา)
PAID            → PROCESSING   (Admin กดเริ่มจัดของ)
PAID            → CANCELLED    (Admin ยกเลิก → ต้อง Refund)
PROCESSING      → SHIPPING     (Admin กดส่งของ)
SHIPPING        → COMPLETED    (ลูกค้ากดได้รับของ)
```

## Transition ที่ไม่อนุญาต

```
PENDING_PAYMENT → SHIPPING     ❌ ยังไม่จ่ายจะส่งไม่ได้
COMPLETED       → CANCELLED    ❌ ได้ของแล้วจะ Cancel ไม่ได้
SHIPPING        → CANCELLED    ❌ ส่งไปแล้ว Cancel ไม่ได้
```

---

## ตัวอย่าง Code (OrderService)

```php
// กฎ: สถานะไหน → ไปต่อได้แค่ไหน
private array $allowedTransitions = [
    'pending_payment' => ['paid', 'cancelled'],
    'paid'            => ['processing', 'cancelled'],
    'processing'      => ['shipping'],
    'shipping'        => ['completed'],
    'completed'       => [],
    'cancelled'       => [],
];

public function updateStatus(Order $order, string $newStatus): Order
{
    // เช็คว่า transition นี้อนุญาตไหม
    $allowed = $this->allowedTransitions[$order->status] ?? [];

    if (!in_array($newStatus, $allowed)) {
        throw new \Exception(
            "Cannot transition from {$order->status} to {$newStatus}"
        );
    }

    $order->update(['status' => $newStatus]);
    return $order;
}
```

---

## Flow เมื่อ Payment สำเร็จ

```
Payment Gateway ส่ง Webhook มา
        ↓
PaymentService รับ Webhook
        ↓
ตรวจสอบว่า Payment จริง
        ↓
OrderService->updateStatus(order, 'paid')
        ↓
InventoryService->deductStock(order)  ← ตัด Stock ตอนนี้
        ↓
Event: OrderPlaced → ส่ง Email ยืนยัน
```
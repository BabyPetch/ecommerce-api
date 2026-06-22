# 06 - Transaction & Inventory

## Transaction คืออะไร?

Transaction คือการรับประกันว่า **หลายขั้นตอนจะสำเร็จพร้อมกัน หรือล้มเหลวพร้อมกัน**

### ตัวอย่างชีวิตจริง: โอนเงิน

```
ธนาคาร A หักเงิน -1000 บาท
        ↓
ธนาคาร B รับเงิน +1000 บาท
```

ถ้า A หักแล้ว แต่ B ยังไม่ได้รับ แล้วระบบพัง → เงินหายไปเลย

Transaction แก้ปัญหานี้โดย: **ถ้าขั้นตอนไหนพัง → ย้อนกลับทุกอย่าง (Rollback)**

---

## ทำไม E-Commerce ต้องใช้?

```
ลูกค้าซื้อสินค้า มี 3 ขั้นตอน:

1. สร้าง Order
2. สร้าง Order Items
3. Reserve Stock (จอง Stock ไว้ก่อน)

ถ้าขั้นตอนที่ 3 Fail:
❌ ไม่มี Transaction → มี Order แต่ไม่มี Stock จอง → ขายเกิน Stock
✅ มี Transaction    → Rollback ทั้งหมด → ไม่มี Order เกิดขึ้น
```

---

## ตัวอย่าง Code

```php
public function createOrder(array $data): Order
{
    return DB::transaction(function () use ($data) {

        // 1. สร้าง Order
        $order = $this->orderRepository->create([
            'user_id'          => auth()->id(),
            'status'           => 'pending_payment',
            'total_amount'     => $this->calculateTotal($data['items']),
            'shipping_name'    => $data['shipping_name'],
            'shipping_address' => $data['shipping_address'],
        ]);

        // 2. สร้าง Order Items
        foreach ($data['items'] as $item) {
            $product = $this->productRepository->findOrFail($item['product_id']);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,   // ← เก็บราคา ณ วันนี้
                'subtotal'   => $product->price * $item['quantity'],
            ]);
        }

        // 3. Reserve Stock
        // ถ้า Stock ไม่พอ → throw Exception → Rollback อัตโนมัติ
        $this->inventoryService->reserveStock($data['items']);

        return $order;

    }); // ถ้ามี Exception ใดๆ → Rollback ทั้งหมดอัตโนมัติ
}
```

---

## Inventory & Race Condition

### ปัญหา: มีสินค้า 1 ชิ้น แต่ 2 คนซื้อพร้อมกัน

```
User A อ่าน Stock → เห็น 1
User B อ่าน Stock → เห็น 1
User A ซื้อ → Stock เหลือ 0
User B ซื้อ → Stock เหลือ -1  ← ปัญหา!
```

### วิธีแก้: Pessimistic Lock (lockForUpdate)

```php
public function reserveStock(array $items): void
{
    DB::transaction(function () use ($items) {
        foreach ($items as $item) {

            // lockForUpdate → ล็อค row นี้ไว้
            // คนอื่นต้องรอจนกว่าเราจะเสร็จ
            $inventory = Inventory::where('product_id', $item['product_id'])
                ->lockForUpdate()
                ->first();

            $available = $inventory->quantity - $inventory->reserved;

            if ($available < $item['quantity']) {
                throw new \Exception("Stock ไม่เพียงพอสำหรับสินค้า ID: {$item['product_id']}");
            }

            // เพิ่ม reserved (จองไว้ก่อน ยังไม่ตัดจริง)
            $inventory->increment('reserved', $item['quantity']);
        }
    });
}

// ตัด Stock จริงๆ เมื่อจ่ายเงินสำเร็จ
public function deductStock(Order $order): void
{
    foreach ($order->items as $item) {
        Inventory::where('product_id', $item->product_id)->update([
            'quantity' => DB::raw("quantity - {$item->quantity}"),
            'reserved' => DB::raw("reserved - {$item->quantity}"),
        ]);
    }
}
```

---

## สรุป Flow Stock

```
ลูกค้าสั่งซื้อ    → reserved +1  (จองไว้)
จ่ายเงินสำเร็จ   → quantity -1, reserved -1  (ตัดจริง)
ยกเลิก Order     → reserved -1  (คืน Stock)
```
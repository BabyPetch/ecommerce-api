# 02 - Architecture

## ที่เลือกใช้: Layered Architecture (Service Repository Pattern)

```
Request → Controller → Service → Repository → Model → Database
```

---

## ทำไมเลือกแบบนี้?

เปรียบเหมือนร้านอาหาร

```
Controller  =  พนักงานเสิร์ฟ  (รับ Order ส่งต่อ ไม่ทำอาหารเอง)
Service     =  หัวหน้าครัว    (คิดสูตร ควบคุมขั้นตอน)
Repository  =  คลังวัตถุดิบ   (ดึงของจาก DB ไม่รู้เรื่องอื่น)
Model       =  วัตถุดิบ       (ข้อมูลดิบจาก Database)
```

---

## ข้อดี

- Controller บาง อ่านง่าย
- Logic อยู่ใน Service ที่เดียว
- เปลี่ยน Database ได้โดยแก้แค่ Repository
- Test ได้ง่าย แยกส่วนกันชัดเจน

## ข้อเสีย

- ไฟล์เยอะกว่า MVC ธรรมดา

## ถ้าโปรเจคโตขึ้น จะเปลี่ยนยังไง?

```
ตอนนี้:   Monolith (ทุกอย่างอยู่ใน Laravel เดียว)
โตขึ้น:   แยก Microservice เช่น Payment Service, Notification Service
```

---

## Folder Structure

```
ecommerce-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/        ← รับ Request ส่งต่อ Service เท่านั้น
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   └── Admin/
│   │   │       └── DashboardController.php
│   │   ├── Requests/           ← Validate ข้อมูลก่อนเข้า Controller
│   │   │   ├── LoginRequest.php
│   │   │   └── CreateOrderRequest.php
│   │   └── Resources/          ← แปลง Model เป็น JSON Response
│   │       ├── ProductResource.php
│   │       └── OrderResource.php
│   ├── Services/               ← Business Logic ทั้งหมดอยู่ที่นี่
│   │   ├── AuthService.php
│   │   ├── ProductService.php
│   │   ├── OrderService.php
│   │   ├── PaymentService.php
│   │   └── InventoryService.php
│   ├── Repositories/           ← Query Database อยู่ที่นี่อย่างเดียว
│   │   ├── ProductRepository.php
│   │   ├── OrderRepository.php
│   │   └── UserRepository.php
│   ├── Models/                 ← Eloquent Model + Relationships
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   └── Inventory.php
│   ├── Events/                 ← เหตุการณ์ที่เกิดในระบบ เช่น OrderPlaced
│   │   ├── OrderPlaced.php
│   │   └── PaymentCompleted.php
│   ├── Listeners/              ← ทำอะไรเมื่อ Event เกิด เช่น ส่ง Email
│   │   ├── SendOrderConfirmation.php
│   │   └── UpdateInventory.php
│   └── Jobs/                   ← งานที่รันใน Background เช่น ส่ง Email
│       └── SendEmailJob.php
├── database/
│   ├── migrations/             ← สร้าง Table
│   └── seeders/                ← ข้อมูลตัวอย่าง
├── routes/
│   ├── api.php                 ← API Routes ทั้งหมด
│   └── web.php
└── docs/                       ← Documentation (ไฟล์นี้อยู่ที่นี่)
```

---

## กฎเหล็ก (Coding Guideline)

### ✅ ควรทำ

```
Controller → เรียก Service อย่างเดียว
Service    → เรียก Repository + Business Logic
Repository → Query Database อย่างเดียว
Request    → Validate ข้อมูลก่อนเข้า Controller เสมอ
Resource   → แปลง Response ก่อนส่งออกเสมอ
```

### ❌ ไม่ควรทำ

```
Controller → เขียน DB::query() เอง
Controller → เขียน Logic ยาวเกิน 20 บรรทัด
Service    → รับ Request Object โดยตรง
Model      → มี Business Logic อยู่ใน Model
```

---

## ตัวอย่าง Bad vs Good

### ❌ Bad — Controller ทำทุกอย่างเอง

```php
public function store(Request $request)
{
    // Validate เอง
    if (!$request->name) return response()->json(['error' => 'name required'], 422);

    // Query เอง
    $product = Product::create([...]);

    // Logic เอง
    Inventory::create(['product_id' => $product->id, 'stock' => 0]);

    return response()->json($product);
}
```

### ✅ Good — Controller บาง แค่รับ-ส่ง

```php
public function store(CreateProductRequest $request)
{
    $product = $this->productService->create($request->validated());
    return new ProductResource($product);
}
```

---

## Design Patterns ที่ใช้ในโปรเจคนี้

| Pattern | ใช้ที่ไหน | ทำไม |
|---|---|---|
| Service Layer | Services/ | แยก Business Logic ออกจาก Controller |
| Repository Pattern | Repositories/ | แยก Database Query ออกจาก Logic |
| DTO (Data Transfer) | Request Classes | ส่งข้อมูลระหว่าง Layer อย่างปลอดภัย |
| Observer/Event | Events + Listeners | ทำงาน async เช่น ส่ง Email หลัง Order |
| Resource (Transformer) | Resources/ | ควบคุม JSON ที่ส่งออก |
| State Machine | OrderService | ควบคุม Order Status ไม่ให้ข้ามขั้น |
# 07 - Development Roadmap

## Phase 1: Project Setup (วันที่ 1-2)

### สิ่งที่ต้องทำ
- [ ] สร้าง Laravel project
- [ ] ตั้งค่า PostgreSQL
- [ ] ติดตั้ง Laravel Sanctum
- [ ] สร้าง Migration ทั้งหมด
- [ ] สร้าง Seeder ข้อมูลตัวอย่าง
- [ ] ตั้งค่า Folder Structure (Services, Repositories)

### Skill ที่ได้
- Laravel project setup
- Database Migration
- Seeder & Factory

---

## Phase 2: Auth + User (วันที่ 3-5)

### สิ่งที่ต้องทำ
- [ ] Register API
- [ ] Login API (Sanctum Token)
- [ ] Logout API
- [ ] Me API
- [ ] Role Middleware (admin/customer)

### Skill ที่ได้
- Laravel Sanctum
- Middleware
- FormRequest Validation
- API Resource

---

## Phase 3: Product + Category (วันที่ 6-9)

### สิ่งที่ต้องทำ
- [ ] CRUD Category (Admin)
- [ ] CRUD Product (Admin)
- [ ] List + Search + Filter + Paginate Product (Public)
- [ ] Cloudinary Image Upload
- [ ] Inventory สร้างอัตโนมัติเมื่อเพิ่มสินค้า

### Skill ที่ได้
- Service Repository Pattern
- File Upload
- Query Filtering & Pagination

---

## Phase 4: Cart + Order (วันที่ 10-15)

### สิ่งที่ต้องทำ
- [ ] Cart (เก็บใน Session หรือ Database)
- [ ] Create Order + Order Items
- [ ] Reserve Stock (Transaction)
- [ ] Order State Machine
- [ ] Admin อัปเดตสถานะ Order

### Skill ที่ได้
- Database Transaction
- State Machine
- Race Condition & Lock

---

## Phase 5: Payment (วันที่ 16-22)

### สิ่งที่ต้องทำ
- [ ] Stripe Integration (sandbox)
- [ ] PromptPay QR (mock หรือ Omise)
- [ ] Webhook Handler
- [ ] Payment สำเร็จ → ตัด Stock → อัปเดต Order
- [ ] Event: OrderPlaced → ส่ง Email

### Skill ที่ได้
- Payment Gateway Integration
- Webhook
- Laravel Event & Listener
- Queue & Job

---

## Phase 6: Admin Dashboard + Deploy (วันที่ 23-30)

### สิ่งที่ต้องทำ
- [ ] Dashboard Stats API
- [ ] Revenue Chart API
- [ ] Low Stock Alert
- [ ] React Frontend (Vite + MUI)
- [ ] Deploy Backend → Railway
- [ ] Deploy Frontend → Vercel
- [ ] เขียน README

### Skill ที่ได้
- Analytics Query
- React + API Integration
- Deployment
- Documentation

---

## Git Commit Convention

```
feat:     เพิ่ม feature ใหม่
fix:      แก้ bug
docs:     แก้ docs
refactor: refactor code
test:     เพิ่ม test
chore:    งานอื่นๆ เช่น config

ตัวอย่าง:
feat: add product CRUD API
fix: fix stock deduction on payment
docs: update API design
```
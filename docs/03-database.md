# 03 - Database Design

## คิดก่อนสร้าง Table

ถามตัวเองว่า "ข้อมูลอะไรเกิดขึ้นในระบบ?"

```
มีคนสมัคร         → ต้องเก็บ users
มีสินค้า           → ต้องเก็บ products
สินค้ามีหมวดหมู่   → ต้องเก็บ categories
ลูกค้าสั่งซื้อ     → ต้องเก็บ orders
Order มีหลายสินค้า → ต้องเก็บ order_items
มีการจ่ายเงิน      → ต้องเก็บ payments
มี Stock           → ต้องเก็บ inventory
```

---

## ERD (Entity Relationship)

```
users
  │
  └── orders (one-to-many: user มีหลาย order)
        │
        ├── order_items (one-to-many: order มีหลาย item)
        │       │
        │       └── products (many-to-one: item อ้างถึง product)
        │
        └── payments (one-to-one: order มี payment เดียว)

products
  │
  ├── categories (many-to-many: product อยู่ได้หลาย category)
  │
  └── inventory (one-to-one: product มี stock เดียว)
```

---

## Table Design

### users
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
email           VARCHAR(255) UNIQUE
password        VARCHAR(255)
role            ENUM('customer', 'admin') DEFAULT 'customer'
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### categories
```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
description     TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### products
```sql
id              BIGINT PRIMARY KEY
category_id     BIGINT FK → categories.id
name            VARCHAR(255)
slug            VARCHAR(255) UNIQUE
description     TEXT NULL
price           DECIMAL(10,2)
image_url       VARCHAR(255) NULL
is_active       BOOLEAN DEFAULT true
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### inventory
```sql
id              BIGINT PRIMARY KEY
product_id      BIGINT FK → products.id UNIQUE
quantity        INT DEFAULT 0
reserved        INT DEFAULT 0  ← Stock ที่ถูกจอง (อยู่ใน cart)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### orders
```sql
id              BIGINT PRIMARY KEY
user_id         BIGINT FK → users.id
status          ENUM('pending_payment','paid','processing','shipping','completed','cancelled')
total_amount    DECIMAL(10,2)
shipping_name   VARCHAR(255)
shipping_phone  VARCHAR(20)
shipping_address TEXT
note            TEXT NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### order_items
```sql
id              BIGINT PRIMARY KEY
order_id        BIGINT FK → orders.id
product_id      BIGINT FK → products.id
quantity        INT
unit_price      DECIMAL(10,2)  ← เก็บราคา ณ วันที่ซื้อ (ราคาอาจเปลี่ยนทีหลัง)
subtotal        DECIMAL(10,2)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### payments
```sql
id              BIGINT PRIMARY KEY
order_id        BIGINT FK → orders.id UNIQUE
method          ENUM('promptpay', 'stripe')
status          ENUM('pending', 'completed', 'failed', 'refunded')
amount          DECIMAL(10,2)
transaction_id  VARCHAR(255) NULL  ← ID จาก Payment Gateway
paid_at         TIMESTAMP NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## Relationships

### One-to-Many
```
users    →  orders      (1 คน มีได้หลาย order)
orders   →  order_items (1 order มีได้หลายสินค้า)
```

### One-to-One
```
orders   →  payments    (1 order มี 1 payment)
products →  inventory   (1 สินค้า มี 1 stock record)
```

### Many-to-Many
```
products ↔ categories   (1 สินค้าอยู่ได้หลาย category)
           ↕
    category_product (pivot table)
      product_id
      category_id
```

---

## สิ่งสำคัญที่ต้องจำ

```
unit_price ใน order_items → เก็บราคา ณ เวลาที่ซื้อ
                             ห้ามอ้าง products.price โดยตรง
                             เพราะราคาอาจเปลี่ยนในอนาคต

reserved ใน inventory    → Stock ที่ถูกจองไว้ใน cart
                           available = quantity - reserved
```
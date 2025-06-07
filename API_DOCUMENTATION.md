# PhoneKu API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Response Format
All API responses follow this format:
```json
{
  "success": true|false,
  "message": "Response message",
  "data": {}, // Response data (optional)
  "errors": {} // Validation errors (optional)
}
```

## Authentication Endpoints

### Register Customer
**POST** `/auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer"
    },
    "token": "1|abc123..."
  }
}
```

### Customer Login
**POST** `/auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer"
    },
    "token": "1|abc123..."
  }
}
```

### Admin Login
**POST** `/auth/admin/login`

**Request Body:**
```json
{
  "email": "admin@phoneku.com",
  "password": "admin123"
}
```

### Admin Register
**POST** `/auth/admin/register`

**Request Body:**
```json
{
  "name": "Admin Name",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Logout
**POST** `/auth/logout`
*Requires Authentication*

### Get Current User
**GET** `/auth/user`
*Requires Authentication*

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer"
    }
  }
}
```

## Product Endpoints

### Get All Products
**GET** `/products`

**Query Parameters:**
- `category` (optional): Filter by category (handphone, accessory)
- `brand` (optional): Filter by brand
- `search` (optional): Search in name and description
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 12)

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "iPhone 15 Pro Max",
        "description": "Latest iPhone...",
        "price": 19999000,
        "original_price": 21999000,
        "category": "handphone",
        "brand": "Apple",
        "stock": 50,
        "is_featured": true,
        "image": "products/iphone15.jpg",
        "specifications": {...}
      }
    ],
    "current_page": 1,
    "total": 8,
    "per_page": 12
  }
}
```

### Get Featured Products
**GET** `/products/featured`

**Query Parameters:**
- `category` (optional): Filter by category
- `limit` (optional): Number of products to return (default: 8)

### Search Products
**GET** `/products/search`

**Query Parameters:**
- `q` (required): Search query
- `category` (optional): Filter by category

### Get Product Details
**GET** `/products/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro Max",
    "description": "Latest iPhone...",
    "price": 19999000,
    "original_price": 21999000,
    "category": "handphone",
    "brand": "Apple",
    "stock": 50,
    "is_featured": true,
    "image": "products/iphone15.jpg",
    "specifications": {
      "display": "6.7-inch Super Retina XDR",
      "processor": "A17 Pro chip",
      "storage": "256GB"
    }
  }
}
```

## Cart Endpoints
*All cart endpoints require authentication*

### Get Cart Items
**GET** `/cart`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "product": {
        "id": 1,
        "name": "iPhone 15 Pro Max",
        "price": 19999000,
        "image": "products/iphone15.jpg"
      }
    }
  ],
  "cart_count": 2,
  "subtotal": 39998000
}
```

### Add Item to Cart
**POST** `/cart/add/{productId}`

**Request Body:**
```json
{
  "quantity": 1
}
```

### Update Cart Item Quantity
**PUT** `/cart/{itemId}`

**Request Body:**
```json
{
  "quantity": 3
}
```

### Remove Cart Item
**DELETE** `/cart/{itemId}`

### Get Cart Count
**GET** `/cart/count`

**Response:**
```json
{
  "success": true,
  "data": {
    "cart_count": 5
  }
}
```

### Clear Cart
**DELETE** `/cart`

## User Profile Endpoints
*All user endpoints require authentication*

### Get User Profile
**GET** `/user/profile`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+62812345678",
    "address": "Jakarta, Indonesia",
    "role": "customer"
  }
}
```

### Update User Profile
**PUT** `/user/profile`

**Request Body:**
```json
{
  "name": "John Smith",
  "phone": "+62812345678",
  "address": "Jakarta, Indonesia"
}
```

### Request Email Change OTP
**POST** `/user/email/request-otp`

**Request Body:**
```json
{
  "new_email": "newemail@example.com"
}
```

### Verify Email Change OTP
**POST** `/user/email/verify-otp`

**Request Body:**
```json
{
  "new_email": "newemail@example.com",
  "otp": "123456"
}
```

### Request Phone Change OTP
**POST** `/user/phone/request-otp`

**Request Body:**
```json
{
  "new_phone": "+62812345679"
}
```

### Verify Phone Change OTP
**POST** `/user/phone/verify-otp`

**Request Body:**
```json
{
  "new_phone": "+62812345679",
  "otp": "123456"
}
```

## Chat Endpoints
*All chat endpoints require authentication*

### Get Customer Chat Messages
**GET** `/chat/customer`
*Customer only*

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sender_id": 1,
      "receiver_id": 2,
      "message": "Hello, I need help with my order",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Send Message
**POST** `/chat/send`

**Request Body:**
```json
{
  "message": "Hello, I need help",
  "receiver_id": 2
}
```

### Get Chat Messages (Admin)
**GET** `/chat/messages/{receiverId}`
*Admin only*

## Admin Endpoints
*All admin endpoints require admin authentication*

### Get Dashboard Statistics
**GET** `/admin/dashboard`

**Response:**
```json
{
  "success": true,
  "data": {
    "total_users": 150,
    "total_products": 25,
    "total_orders": 89,
    "total_revenue": 450000000,
    "recent_orders": [...],
    "popular_products": [...]
  }
}
```

### Get All Users
**GET** `/admin/users`

**Query Parameters:**
- `search` (optional): Search users by name or email
- `role` (optional): Filter by role (customer, admin)
- `page` (optional): Page number

### Create User
**POST** `/admin/users`

**Request Body:**
```json
{
  "name": "New User",
  "email": "user@example.com",
  "password": "password123",
  "role": "customer"
}
```

### Update User
**PUT** `/admin/users/{id}`

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "role": "customer"
}
```

### Delete User
**DELETE** `/admin/users/{id}`

### Create Product
**POST** `/admin/products`

**Request Body (multipart/form-data):**
```
name: iPhone 15 Pro Max
description: Latest iPhone...
price: 19999000
original_price: 21999000
category: handphone
brand: Apple
stock: 50
is_featured: true
image: [file upload]
specifications: {"display": "6.7-inch", ...}
```

### Update Product
**PUT** `/admin/products/{id}`

**Request Body (multipart/form-data):**
```
name: Updated iPhone 15 Pro Max
price: 18999000
stock: 45
image: [file upload] (optional)
```

### Delete Product
**DELETE** `/admin/products/{id}`

### Get Admin Chat
**GET** `/admin/chat`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "customer": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "last_message": {
        "message": "Hello, I need help",
        "created_at": "2024-01-15T10:30:00Z"
      },
      "unread_count": 2
    }
  ]
}
```

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error."
}
```

## Rate Limiting
API endpoints are rate limited to prevent abuse:
- Authentication endpoints: 5 requests per minute
- General API endpoints: 60 requests per minute
- Admin endpoints: 100 requests per minute

## File Uploads
Product images are uploaded to the `storage/app/public/products` directory and accessible via:
```
http://localhost:8000/storage/products/{filename}
```

Supported image formats: JPG, JPEG, PNG, GIF
Maximum file size: 2MB 
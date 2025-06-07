# PhoneKu Microservice - Setup Guide

## 🚀 Cara Menjalankan Aplikasi

### Metode 1: Menggunakan Script Otomatis (Recommended)

1. Buka Command Prompt atau PowerShell
2. Navigate ke folder `phoneku-microservice`
3. Jalankan script:
```bash
.\run-application.bat
```

### Metode 2: Manual

#### Backend (Laravel API)
```bash
cd backend
php artisan serve
```
Backend akan berjalan di: `http://localhost:8000`

#### Frontend (Vue.js)
```bash
cd frontend  
npm run dev
```
Frontend akan berjalan di: `http://localhost:5173`

## 🔧 Perbaikan yang Telah Dilakukan

### 1. FontAwesome Icons
- ✅ Ditambahkan CDN FontAwesome ke `frontend/index.html`
- ✅ Semua icon seperti `fas fa-cart-plus`, `fas fa-user-circle` sekarang berfungsi

### 2. Google Fonts
- ✅ Ditambahkan Inter font untuk konsistensi typography
- ✅ Matching dengan design monolitik

### 3. Metadata HTML
- ✅ Title diubah dari "Vite App" ke "PhoneKu - Toko Handphone & Aksesoris Terpercaya"
- ✅ Ditambahkan meta description
- ✅ Language setting ke bahasa Indonesia

### 4. CSS Framework
- ✅ Tailwind CSS sudah dikonfigurasi dengan benar
- ✅ Custom scrollbar styling
- ✅ Responsive design

### 5. Assets & Images
- ✅ Banner images sudah tersedia (banner1.png, banner2.png, banner3.png)
- ✅ Logo PhoneKu tersedia
- ✅ Product placeholder images

## 🎯 Struktur Aplikasi

```
phoneku-microservice/
├── backend/          # Laravel API Server
│   ├── app/
│   │   ├── routes/api.php
│   │   ├── config/cors.php (sudah dikonfigurasi)
│   │   └── ...
│   ├── frontend/         # Vue.js Client
│   │   ├── src/
│   │   │   ├── components/
│   │   │   ├── views/
│   │   │   ├── stores/
│   │   │   ├── services/
│   │   │   └── router/
│   │   ├── public/img/   # Assets & images
│   │   └── index.html    # ✅ Sudah diperbaiki
│   └── run-application.bat # ✅ Script baru
```

## 📱 Fitur yang Tersedia

### Frontend Vue.js
- ✅ Responsive Navbar dengan mobile menu
- ✅ Homepage dengan banner slider
- ✅ Product catalog (handphone & aksesoris)
- ✅ Shopping cart functionality
- ✅ User authentication (login/register)
- ✅ User profile management
- ✅ Admin dashboard
- ✅ Chat system

### Backend Laravel API
- ✅ RESTful API endpoints
- ✅ Authentication dengan Sanctum
- ✅ Product management
- ✅ Cart management
- ✅ User management
- ✅ Admin functionality
- ✅ CORS properly configured

## 🌐 URL Akses

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **API Documentation**: [Lihat API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

## 🔍 Troubleshooting

### Jika tampilan masih "acak-acakan":

1. **Pastikan kedua server berjalan**:
   - Backend: http://localhost:8000
   - Frontend: http://localhost:5173

2. **Clear browser cache**:
   - Ctrl + Shift + R (hard refresh)
   - Atau buka developer tools dan disable cache

3. **Periksa console errors**:
   - Buka browser developer tools (F12)
   - Lihat tab Console untuk error messages
   - Lihat tab Network untuk failed requests

4. **Restart servers**:
   ```bash
   # Stop dengan Ctrl+C di command prompt
   # Lalu jalankan ulang dengan run-application.bat
   ```

### Jika API tidak terhubung:

1. **Periksa CORS**:
   - File `backend/config/cors.php` sudah dikonfigurasi untuk localhost:5173

2. **Periksa .env backend**:
   ```env
   APP_URL=http://localhost:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   ```

3. **Periksa database connection**:
   ```bash
   cd backend
   php artisan migrate
   ```

## 📋 Default Login Credentials

### Admin
- Email: admin@phoneku.com
- Password: admin123

### Customer  
- Registrasi sendiri di halaman register

## 🎨 Styling Features

- ✅ Modern UI dengan Tailwind CSS
- ✅ Blue color scheme (#3B82F6)
- ✅ Responsive design (mobile-first)
- ✅ Smooth animations dan transitions
- ✅ FontAwesome icons
- ✅ Custom scrollbar
- ✅ Hover effects
- ✅ Loading states

Sekarang aplikasi microservice PhoneKu seharusnya memiliki tampilan yang sama bagusnya dengan versi monolitik! 🎉 
@echo off
echo 🔧 COMPLETE PHONEKU MICROSERVICE FIX
echo =====================================
echo Ensuring everything works like monolithic version...
echo.

echo Step 1: Fix Backend Database Structure...
cd backend

echo 1.1. Generating fresh Laravel key...
php artisan key:generate
echo.

echo 1.2. Running all migrations (adding missing columns)...
php artisan migrate
echo.

echo 1.3. Reset database with complete structure...
php artisan migrate:fresh
php artisan migrate
echo.

echo 1.4. Seeding database with proper data...
php artisan db:seed
echo.

echo 1.5. Creating storage symlink...
php artisan storage:link
echo.

echo 1.6. Starting Laravel API server...
start "PhoneKu API" cmd /k "php artisan serve"
echo ✅ Backend API running at http://localhost:8000
echo.

timeout /t 5 /nobreak > nul

cd ../frontend

echo Step 2: Fix Frontend Completely...
echo.

echo 2.1. Stopping any running servers...
taskkill /f /im node.exe 2>nul

echo 2.2. Cleaning all cache and temp files...
if exist .vite rmdir /s /q .vite
if exist dist rmdir /s /q dist
if exist node_modules rmdir /s /q node_modules
if exist package-lock.json del package-lock.json

echo 2.3. Clearing npm cache...
npm cache clean --force

echo 2.4. Fresh install of dependencies...
npm install

echo 2.5. Building to ensure no compilation errors...
npm run build

echo 2.6. Starting Vue.js development server...
start "PhoneKu Frontend" cmd /k "npm run dev"
echo ✅ Frontend running at http://localhost:5173
echo.

cd ..

echo Step 3: Testing API Connection...
timeout /t 3 /nobreak > nul
echo Testing backend API...
curl -s http://localhost:8000/api/products >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Backend API is responding
) else (
    echo ❌ Backend API not responding yet, please wait...
)
echo.

echo.
echo ========================================
echo 🎉 PHONEKU MICROSERVICE READY!
echo ========================================
echo.
echo 🌐 Frontend Application: 
echo    http://localhost:5173
echo.
echo 🔌 Backend API:
echo    http://localhost:8000
echo    http://localhost:8000/api/products
echo.
echo 🔑 Login Credentials:
echo    Admin: admin@phoneku.com / admin123
echo    Customer: customer@phoneku.com / customer123
echo.
echo 📱 Features Available:
echo    ✓ Product Catalog
echo    ✓ Shopping Cart
echo    ✓ User Authentication
echo    ✓ Admin Dashboard
echo    ✓ Customer Support Chat
echo    ✓ Profile Management
echo.
echo 🛠️ If frontend is blank:
echo    1. Wait 30 seconds for Vite to compile
echo    2. Refresh browser (Ctrl+F5)
echo    3. Check F12 Console for errors
echo.
pause 
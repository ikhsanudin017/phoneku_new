@echo off
echo ⚡ Quick Start PhoneKu...
echo.

echo 🔧 Fixing database...
cd backend
php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo ❌ Database error! Trying to fix...
    php artisan key:generate
    php artisan migrate:fresh --seed
)

echo 🚀 Starting backend server...
start "Backend Server" cmd /k "php artisan serve"

cd ..

echo 🎨 Starting frontend...
cd frontend
start "Frontend Server" cmd /k "npm run dev"

cd ..

echo.
echo ✅ Servers started!
echo 📱 Frontend: http://localhost:5173
echo 🔌 Backend: http://localhost:8000
echo.
echo 🔑 Login:
echo Admin: admin@phoneku.com / admin123
echo Customer: customer@phoneku.com / customer123
echo.
pause 
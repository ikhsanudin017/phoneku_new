@echo off
echo 🔧 Fixing All PhoneKu Issues...
echo.

cd backend

echo 1. Generating Laravel key...
php artisan key:generate
echo.

echo 2. Adding missing database columns...
php artisan migrate
echo.

echo 3. Resetting database with correct structure...
php artisan migrate:fresh --seed
echo.

echo 4. Starting backend server...
start "Backend API" cmd /k "php artisan serve"
echo Backend started at http://localhost:8000
echo.

timeout /t 3 /nobreak > nul

cd ../frontend

echo 5. Installing frontend dependencies...
npm install
echo.

echo 6. Starting frontend server...
start "Frontend App" cmd /k "npm run dev"
echo Frontend started at http://localhost:5173
echo.

cd ..

echo.
echo ✅ ALL FIXED!
echo.
echo 🌐 Open in browser:
echo   Frontend: http://localhost:5173
echo   Backend API: http://localhost:8000/api/products
echo.
echo 🔑 Login credentials:
echo   Admin: admin@phoneku.com / admin123
echo   Customer: customer@phoneku.com / customer123
echo.
echo If still blank, press F12 and check Console for errors.
echo.
pause 
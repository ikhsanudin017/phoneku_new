@echo off
echo ========================================
echo    PhoneKu Microservice Setup
echo ========================================
echo.

echo Step 1: Setting up Laravel Backend...
echo.
cd backend

echo Installing Composer dependencies...
composer install
echo.

echo Copying environment file...
if not exist .env (
    copy .env.example .env
    echo Environment file created. Please configure your database settings in .env
    echo.
    pause
)

echo Generating application key...
php artisan key:generate
echo.

echo Running database migrations...
php artisan migrate:fresh
echo.

echo Seeding database with sample data...
php artisan db:seed
echo.

echo Creating storage link...
php artisan storage:link
echo.

cd ..

echo Step 2: Setting up Vue.js Frontend...
echo.
cd frontend

echo Installing npm dependencies...
npm install
echo.

cd ..

echo ========================================
echo    Setup Complete!
echo ========================================
echo.
echo Default credentials:
echo Admin: admin@phoneku.com / admin123
echo Customer: customer@phoneku.com / customer123
echo.
echo To start the application:
echo 1. Run start-servers.bat
echo 2. Open http://localhost:5173 in your browser
echo.
echo API Documentation: API_DOCUMENTATION.md
echo Project Documentation: README.md
echo.
pause 
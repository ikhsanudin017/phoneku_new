@echo off
echo Setting up PhoneKu Database...
echo.

cd backend

echo Running database migrations...
php artisan migrate:fresh
echo.

echo Seeding database with sample data...
php artisan db:seed
echo.

echo Creating storage link...
php artisan storage:link
echo.

echo Database setup complete!
echo.
echo Default credentials:
echo Admin: admin@phoneku.com / admin123
echo Customer: customer@phoneku.com / customer123
echo.
pause 
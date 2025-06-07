@echo off
echo Running PhoneKu Application Tests...
echo.

echo ========================================
echo    Backend Tests (Laravel)
echo ========================================
echo.
cd backend

echo Running PHP Unit Tests...
php artisan test
echo.

echo Running API Endpoint Tests...
cd ..
call test-api.bat
echo.

echo ========================================
echo    Frontend Tests (Vue.js)
echo ========================================
echo.
cd frontend

echo Running ESLint...
npm run lint
echo.

echo Running E2E Tests...
npm run test:e2e
echo.

cd ..

echo ========================================
echo    All Tests Complete!
echo ========================================
echo.
pause 
@echo off
echo Starting PhoneKu Microservice Application...
echo.

echo Starting Laravel Backend API Server...
start "Laravel Backend" cmd /k "cd backend && php artisan serve"

echo Waiting for backend to start...
timeout /t 3 /nobreak > nul

echo Starting Vue.js Frontend Development Server...
start "Vue.js Frontend" cmd /k "cd frontend && npm run dev"

echo.
echo Both servers are starting...
echo Backend API: http://localhost:8000
echo Frontend App: http://localhost:5173
echo.
echo Press any key to close this window...
pause > nul 
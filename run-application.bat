@echo off
cls
echo ============================================
echo     PhoneKu Microservice Application
echo ============================================
echo.

echo [1/3] Checking directories...
if not exist "backend" (
    echo ERROR: Backend directory not found!
    echo Please run this script from the phoneku-microservice root directory.
    pause
    exit /b 1
)

if not exist "frontend" (
    echo ERROR: Frontend directory not found!
    echo Please run this script from the phoneku-microservice root directory.
    pause
    exit /b 1
)

echo [2/3] Starting Laravel Backend Server...
echo Backend will run on: http://localhost:8000
start "PhoneKu Backend API" cmd /k "cd backend && echo Starting Laravel Backend... && php artisan serve"

echo [3/3] Waiting 3 seconds then starting Frontend...
timeout /t 3 /nobreak > nul

echo Starting Vue.js Frontend Server...
echo Frontend will run on: http://localhost:5173
start "PhoneKu Frontend" cmd /k "cd frontend && echo Starting Vue.js Frontend... && npm run dev"

echo.
echo ============================================
echo           Application Started!
echo ============================================
echo.
echo Backend API:  http://localhost:8000
echo Frontend App: http://localhost:5173
echo.
echo Both servers are starting in separate windows.
echo Wait a moment for them to fully load.
echo.
echo Access your application at: http://localhost:5173
echo.
echo Press any key to close this window...
pause > nul 
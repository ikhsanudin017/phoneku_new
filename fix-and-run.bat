@echo off
cls
echo ============================================
echo        PhoneKu Microservice - Fix & Run
echo ============================================
echo.

echo [1/6] Checking project structure...
if not exist "backend" (
    echo ERROR: Backend directory not found!
    pause
    exit /b 1
)

if not exist "frontend" (
    echo ERROR: Frontend directory not found!
    pause
    exit /b 1
)

echo ✅ Project structure OK

echo [2/6] Checking backend dependencies...
cd backend
if not exist "vendor" (
    echo Installing Laravel dependencies...
    composer install
) else (
    echo ✅ Laravel dependencies OK
)

echo [3/6] Checking frontend dependencies...
cd ..\frontend
if not exist "node_modules" (
    echo Installing Vue.js dependencies...
    npm install
) else (
    echo ✅ Vue.js dependencies OK
)

echo [4/6] Building frontend for production...
npm run build

echo [5/6] Starting Laravel Backend Server...
cd ..\backend
echo Backend will run on: http://localhost:8000
start "PhoneKu Backend API" cmd /k "echo ================================ && echo   PhoneKu Backend API Server && echo ================================ && echo Starting Laravel... && php artisan serve"

echo [6/6] Waiting 3 seconds then starting Frontend...
timeout /t 3 /nobreak > nul

echo Starting Vue.js Frontend Server...
cd ..\frontend
echo Frontend will run on: http://localhost:5173
start "PhoneKu Frontend" cmd /k "echo ================================ && echo   PhoneKu Frontend Vue.js && echo ================================ && echo Starting Vue.js development server... && npm run dev"

cd ..

echo.
echo ============================================
echo           🚀 Application Ready!
echo ============================================
echo.
echo ✅ Backend API:  http://localhost:8000
echo ✅ Frontend App: http://localhost:5173
echo.
echo 📱 Main Application: http://localhost:5173
echo 📚 API Documentation: http://localhost:8000/api
echo.
echo 🎯 Features Fixed:
echo   - FontAwesome icons
echo   - Google Fonts (Inter)
echo   - Tailwind CSS styling
echo   - CORS configuration
echo   - Responsive design
echo   - API integration
echo.
echo Both servers are running in separate windows.
echo Wait a moment for them to fully load.
echo.
echo 🌟 Enjoy your PhoneKu Microservice Application!
echo.
pause 
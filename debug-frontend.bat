@echo off
echo 🔍 DEBUGGING FRONTEND ISSUES
echo =============================
echo.

cd frontend

echo 1. Checking if backend API is accessible...
curl -s http://localhost:8000/api/products | findstr "success" >nul
if %errorlevel% == 0 (
    echo ✅ Backend API is working
) else (
    echo ❌ Backend API not accessible - check backend server
    pause
    exit /b 1
)
echo.

echo 2. Stopping all Node.js processes...
taskkill /f /im node.exe 2>nul
timeout /t 2 /nobreak > nul

echo 3. Cleaning all caches and builds...
if exist .vite rmdir /s /q .vite
if exist dist rmdir /s /q dist
if exist node_modules\.cache rmdir /s /q node_modules\.cache
echo.

echo 4. Checking package.json scripts...
type package.json | findstr "dev"
echo.

echo 5. Installing/updating dependencies...
npm install --force
echo.

echo 6. Running linter to fix syntax errors...
npm run lint -- --fix 2>nul
echo.

echo 7. Testing build (to catch compilation errors)...
npm run build
if %errorlevel% neq 0 (
    echo ❌ Build failed - there are compilation errors
    echo Check the errors above and fix them
    pause
    exit /b 1
)
echo ✅ Build successful
echo.

echo 8. Starting development server with verbose output...
echo.
echo 🚀 Starting Vite dev server...
echo If blank page appears:
echo   1. Wait 30-60 seconds for compilation
echo   2. Open http://localhost:5173
echo   3. Press F12 and check Console tab
echo   4. Press Ctrl+Shift+R to hard refresh
echo.

start "PhoneKu Frontend Debug" cmd /k "npm run dev -- --host 0.0.0.0 --debug"

echo.
echo 📱 Frontend should be starting at: http://localhost:5173
echo 🔌 Backend API is at: http://localhost:8000/api/products
echo.
echo If still blank:
echo 1. Open F12 Developer Tools
echo 2. Check Console for JavaScript errors
echo 3. Check Network tab for failed requests
echo 4. Try accessing http://127.0.0.1:5173 instead
echo.
pause 
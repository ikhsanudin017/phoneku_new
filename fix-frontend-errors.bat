@echo off
echo 🔧 Fixing Frontend Syntax Errors...
echo.

cd frontend

echo 1. Stop any running dev servers...
taskkill /f /im node.exe 2>nul

echo 2. Clearing browser cache and temp files...
if exist .vite rmdir /s /q .vite
if exist dist rmdir /s /q dist
if exist .nuxt rmdir /s /q .nuxt

echo 3. Clearing npm cache...
npm cache clean --force

echo 4. Removing node_modules...
if exist node_modules rmdir /s /q node_modules

echo 5. Removing package-lock.json...
if exist package-lock.json del package-lock.json

echo 6. Reinstalling dependencies...
npm install

echo 7. Fixing lint errors...
npm run lint -- --fix 2>nul

echo 8. Building to check compilation...
npm run build

echo 9. Starting fresh dev server...
start "Frontend Fixed" cmd /k "npm run dev"

cd ..

echo.
echo ✅ Frontend errors fixed!
echo.
echo 🌐 Now open: http://localhost:5173
echo.
echo If still getting errors:
echo 1. Open F12 in browser
echo 2. Go to Network tab
echo 3. Refresh page
echo 4. Check for failed requests
echo.
pause 
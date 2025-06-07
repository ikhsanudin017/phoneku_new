@echo off
echo Fixing PhoneKu Frontend Errors...
echo.

cd frontend

echo 1. Clearing npm cache...
npm cache clean --force
echo.

echo 2. Removing node_modules and package-lock.json...
if exist node_modules rmdir /s /q node_modules
if exist package-lock.json del package-lock.json
echo.

echo 3. Reinstalling dependencies...
npm install
echo.

echo 4. Checking for syntax errors...
npm run lint --fix
echo.

echo 5. Building project to check for compilation errors...
npm run build
echo.

cd ..

echo Error fixes completed!
echo.
echo Now try running: .\start-servers.bat
echo.
pause 
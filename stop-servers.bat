@echo off
echo Stopping PhoneKu Microservice Servers...
echo.

echo Stopping Laravel Backend Server...
taskkill /f /im php.exe 2>nul
if %errorlevel% == 0 (
    echo Laravel server stopped.
) else (
    echo Laravel server was not running.
)

echo.
echo Stopping Vue.js Frontend Server...
taskkill /f /im node.exe 2>nul
if %errorlevel% == 0 (
    echo Vue.js server stopped.
) else (
    echo Vue.js server was not running.
)

echo.
echo All servers stopped.
echo.
pause 
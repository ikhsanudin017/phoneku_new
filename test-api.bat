@echo off
echo Testing PhoneKu API Endpoints...
echo.

echo Testing Backend Health Check...
curl -X GET http://localhost:8000/api/products -H "Accept: application/json"
echo.
echo.

echo Testing Product Endpoints...
echo 1. Get all products:
curl -X GET "http://localhost:8000/api/products" -H "Accept: application/json"
echo.
echo.

echo 2. Get featured products:
curl -X GET "http://localhost:8000/api/products/featured" -H "Accept: application/json"
echo.
echo.

echo Testing Authentication Endpoints...
echo 3. Register new user:
curl -X POST "http://localhost:8000/api/auth/register" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}"
echo.
echo.

echo 4. Login user:
curl -X POST "http://localhost:8000/api/auth/login" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"test@example.com\",\"password\":\"password123\"}"
echo.
echo.

echo API Testing Complete!
echo.
echo Note: Make sure Laravel backend is running on http://localhost:8000
echo.
pause 
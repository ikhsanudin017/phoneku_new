Set-Location -Path (Join-Path $PSScriptRoot 'backend')
Write-Host "Setting up PhoneKu Microservice Backend..."

# Clear existing compiled files and caches
Write-Host "`nClearing caches..."
if (Test-Path "bootstrap/cache/config.php") { Remove-Item "bootstrap/cache/config.php" }
if (Test-Path "bootstrap/cache/services.php") { Remove-Item "bootstrap/cache/services.php" }
if (Test-Path "storage/framework/views/*") { Remove-Item "storage/framework/views/*" }

# Run Laravel commands
Write-Host "`nOptimizing Laravel..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Run database migrations
Write-Host "`nSetting up database..."
php artisan config:cache
php artisan migrate:fresh --force --seed

Write-Host "`nDone! You can now start the backend server by running:"
Write-Host "cd backend; php artisan serve"

@echo off
echo ===================================================
echo   EduLink - Production Optimization & Deployment Script
echo ===================================================
echo.

echo [1/6] Optimizing Composer Autoloader...
call composer dump-autoload --optimize
if %ERRORLEVEL% neq 0 (
    echo Error optimizing autoloader.
    exit /b %ERRORLEVEL%
)
echo.

echo [2/6] Caching Laravel Configuration...
call php artisan config:cache
if %ERRORLEVEL% neq 0 (
    echo Error caching configuration.
    exit /b %ERRORLEVEL%
)
echo.

echo [3/6] Caching Laravel Routes...
call php artisan route:cache
if %ERRORLEVEL% neq 0 (
    echo Error caching routes.
    exit /b %ERRORLEVEL%
)
echo.

echo [4/6] Pre-compiling Blade Views...
call php artisan view:cache
if %ERRORLEVEL% neq 0 (
    echo Error caching views.
    exit /b %ERRORLEVEL%
)
echo.

echo [5/6] Caching Event Listeners...
call php artisan event:cache
if %ERRORLEVEL% neq 0 (
    echo Error caching events.
    exit /b %ERRORLEVEL%
)
echo.

echo [6/6] Building Frontend Assets for Production...
call npm run build
if %ERRORLEVEL% neq 0 (
    echo Error building frontend assets.
    exit /b %ERRORLEVEL%
)
echo.

echo ===================================================
echo   EduLink has been successfully optimized!
echo ===================================================
pause

@echo off
setlocal enabledelayedexpansion

:: Laravel Claude Deployment Script (Batch Version)
:: This script handles the complete deployment process

echo.
echo ========================
echo Laravel Claude Deploy
echo ========================
echo.

:: Check if we're in the right directory
if not exist "artisan" (
    echo Error: Laravel artisan file not found. Are you in the correct directory?
    pause
    exit /b 1
)

:: Check for PHP
php --version >nul 2>&1
if errorlevel 1 (
    echo Error: PHP not found in PATH. Please install PHP or add it to your PATH.
    pause
    exit /b 1
)

echo Prerequisites check passed.
echo.

:: Step 1: Database Migrations
echo ==================
echo Running Migrations
echo ==================
echo.

set /p fresh="Run fresh migrations? This will drop all tables! (y/N): "
if /i "%fresh%"=="y" (
    echo Running fresh migrations...
    php artisan migrate:fresh --force
    if errorlevel 1 (
        echo Error: Fresh migration failed
        pause
        exit /b 1
    )
    echo Fresh migrations completed successfully.
) else (
    echo Running database migrations...
    php artisan migrate --force
    if errorlevel 1 (
        echo Error: Migration failed
        pause
        exit /b 1
    )
    echo Migrations completed successfully.
)

echo.

:: Step 2: Core Data Seeding  
echo ====================
echo Core Database Seeding
echo ====================
echo.

set /p seed="Run database seeders? (y/N): "
if /i "%seed%"=="y" (
    echo Seeding companies and roles...
    php artisan db:seed --force
    if errorlevel 1 (
        echo Error: Core seeding failed
        pause
        exit /b 1
    )
    echo Core seeding completed successfully.
)

echo.

:: Step 3: Language Synchronization
echo ========================
echo Language Synchronization
echo ========================
echo.

set /p force_lang="Force language sync? (y/N): "
if /i "%force_lang%"=="y" (
    echo Running forced language synchronization...
    php artisan languages:sync --force
) else (
    echo Running language synchronization...
    php artisan languages:sync
)

if errorlevel 1 (
    echo Error: Language synchronization failed
    pause
    exit /b 1
)

echo Language synchronization completed successfully.
echo.

:: Step 4: RBAC Synchronization
echo ==================
echo RBAC Synchronization
echo ==================
echo.

echo Synchronizing access groups from codebase...
php artisan rbac:sync

if errorlevel 1 (
    echo Error: RBAC synchronization failed
    pause
    exit /b 1
)

echo RBAC synchronization completed successfully.
echo.

:: Step 5: User Data Seeding
if /i "%seed%"=="y" (
    echo ================
    echo User Data Seeding
    echo ================
    echo.
    
    echo Seeding users...
    php artisan db:seed --class=UsersSeeder --force
    if errorlevel 1 (
        echo Error: User seeding failed
        pause
        exit /b 1
    )
    echo User seeding completed successfully.
    echo.
)

:: Step 6: Cache and Optimization
echo =======================
echo Application Optimization
echo =======================
echo.

echo Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo Application optimization completed.
echo.

:: Final summary
echo ===============================
echo Deployment Completed Successfully!
echo ===============================
echo.
echo Your Laravel Claude application is ready!
echo.
echo Press any key to exit...
pause >nul
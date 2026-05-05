# Laravel Claude Deployment Script
# This script handles the complete deployment process including:
# - Database migrations
# - User seeding
# - Language synchronization

param(
    [switch]$Fresh,
    [switch]$Seed,
    [switch]$Force,
    [switch]$Help
)

function Show-Help {
    Write-Host ""
    Write-Host "Laravel Claude Deployment Script" -ForegroundColor Green
    Write-Host "===================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Usage:" -ForegroundColor Yellow
    Write-Host "  .\deploy.ps1 [options]"
    Write-Host ""
    Write-Host "Options:" -ForegroundColor Yellow
    Write-Host "  -Fresh    Run fresh migrations (drops all tables first)"
    Write-Host "  -Seed     Run seeders after migrations"
    Write-Host "  -Force    Force language sync even if languages exist"
    Write-Host "  -Help     Show this help message"
    Write-Host ""
    Write-Host "Examples:" -ForegroundColor Yellow
    Write-Host "  .\deploy.ps1                    # Standard deployment (migrate + language sync)"
    Write-Host "  .\deploy.ps1 -Seed              # Deploy with seeding"
    Write-Host "  .\deploy.ps1 -Fresh -Seed       # Fresh install with seeding"
    Write-Host "  .\deploy.ps1 -Force             # Deploy with forced language sync"
    Write-Host ""
}

function Write-Step {
    param($Message)
    Write-Host ""
    Write-Host "🚀 $Message" -ForegroundColor Cyan
    Write-Host "=" * ($Message.Length + 3) -ForegroundColor Cyan
}

function Write-Success {
    param($Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Error {
    param($Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

function Test-Command {
    param($Command)
    try {
        & $Command --version 2>$null | Out-Null
        return $true
    }
    catch {
        return $false
    }
}

# Show help if requested
if ($Help) {
    Show-Help
    exit 0
}

Write-Host ""
Write-Host "🌟 Laravel Claude Deployment" -ForegroundColor Magenta
Write-Host "===============================" -ForegroundColor Magenta
Write-Host ""

# Check prerequisites
Write-Step "Checking Prerequisites"

if (-not (Test-Path "artisan")) {
    Write-Error "Laravel artisan file not found. Are you in the correct directory?"
    exit 1
}

if (-not (Test-Command "php")) {
    Write-Error "PHP not found in PATH. Please install PHP or add it to your PATH."
    exit 1
}

Write-Success "Prerequisites check passed"

# Step 1: Database Migrations
Write-Step "Running Database Migrations"

try {
    if ($Fresh) {
        Write-Host "Running fresh migrations (this will drop all tables)..." -ForegroundColor Yellow
        php artisan migrate:fresh --force
        if ($LASTEXITCODE -ne 0) { throw "Fresh migration failed" }
        Write-Success "Fresh migrations completed successfully"
    } else {
        Write-Host "Running database migrations..." -ForegroundColor Yellow
        php artisan migrate --force
        if ($LASTEXITCODE -ne 0) { throw "Migration failed" }
        Write-Success "Migrations completed successfully"
    }
} catch {
    Write-Error "Migration failed: $_"
    exit 1
}

# Step 2: Database Seeding - Core Data (optional)
if ($Seed) {
    Write-Step "Running Core Database Seeders"
    
    try {
        Write-Host "Seeding initial data..." -ForegroundColor Yellow
        php artisan db:seed --force
        if ($LASTEXITCODE -ne 0) { throw "Core seeding failed" }
        Write-Success "Core seeding completed successfully"
    } catch {
        Write-Error "Core seeding failed: $_"
        exit 1
    }
}

# Step 3: Language Synchronization
Write-Step "Synchronizing Languages"

try {
    if ($Force) {
        Write-Host "Running forced language synchronization..." -ForegroundColor Yellow
        php artisan languages:sync --force
    } else {
        Write-Host "Running language synchronization..." -ForegroundColor Yellow
        php artisan languages:sync
    }
    
    if ($LASTEXITCODE -ne 0) { throw "Language sync failed" }
    Write-Success "Language synchronization completed successfully"
} catch {
    Write-Error "Language synchronization failed: $_"
    exit 1
}

# Step 4: RBAC Synchronization
Write-Step "Synchronizing RBAC Access Groups"

try {
    Write-Host "Synchronizing access groups from codebase..." -ForegroundColor Yellow
    php artisan access:sync-groups
    
    if ($LASTEXITCODE -ne 0) { throw "RBAC sync failed" }
    Write-Success "RBAC synchronization completed successfully"
} catch {
    Write-Error "RBAC synchronization failed: $_"
    exit 1
}

# Step 5: User Data Seeding (optional)
if ($Seed) {
    Write-Step "Seeding User Data"
    
    try {
        Write-Host "Seeding users..." -ForegroundColor Yellow
        php artisan db:seed --class=UsersSeeder --force
        if ($LASTEXITCODE -ne 0) { throw "User seeding failed" }
        Write-Success "User seeding completed successfully"
    } catch {
        Write-Error "User seeding failed: $_"
        exit 1
    }
}

# Step 6: Cache and Optimization (optional)
Write-Step "Optimizing Application"

try {
    Write-Host "Clearing application cache..." -ForegroundColor Yellow
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    Write-Host "Optimizing for production..." -ForegroundColor Yellow
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    Write-Success "Application optimization completed"
} catch {
    Write-Warning "Some optimization steps failed, but deployment can continue"
}

# Final summary
Write-Host ""
Write-Host "🎉 Deployment Completed Successfully!" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""

$steps = @()
if ($Fresh) { $steps += "Fresh migrations" } else { $steps += "Migrations" }
if ($Seed) { $steps += "Core data seeding (companies, roles)" }
$steps += "Language synchronization"
$steps += "RBAC synchronization"
if ($Seed) { $steps += "User data seeding" }
$steps += "Application optimization"

Write-Host "Completed steps:" -ForegroundColor Yellow
foreach ($step in $steps) {
    Write-Host "  ✅ $step" -ForegroundColor Green
}

Write-Host ""
Write-Host "Your Laravel Claude application is ready! 🚀" -ForegroundColor Magenta
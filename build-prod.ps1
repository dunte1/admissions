# ============================================================
# ADMISSION PORTAL - Production Build Script
# ============================================================

$source = "C:\Users\dunth\Desktop\projects\admission-portal"
$dest   = "C:\Users\dunth\Desktop\admission-portal-production"

# Create destination folder
New-Item -ItemType Directory -Force -Path $dest | Out-Null
Write-Host "Created: $dest" -ForegroundColor Green

# ============================================================
# STEP 1 - Copy root files
# ============================================================
Write-Host "`nCopying root files..." -ForegroundColor Cyan

$rootFiles = @("artisan", "composer.json", "composer.lock", ".env", ".env.example")
foreach ($file in $rootFiles) {
    $srcFile = Join-Path $source $file
    if (Test-Path $srcFile) {
        Copy-Item $srcFile -Destination $dest -Force
        Write-Host "  + $file" -ForegroundColor Gray
    } else {
        Write-Host "  ! MISSING: $file" -ForegroundColor Yellow
    }
}

# ============================================================
# STEP 2 - Copy main folders (simple full copy)
# ============================================================
Write-Host "`nCopying main folders..." -ForegroundColor Cyan

$simpleFolders = @("app", "bootstrap", "config", "lang", "resources", "routes", "vendor")
foreach ($folder in $simpleFolders) {
    $srcFolder  = Join-Path $source $folder
    $destFolder = Join-Path $dest $folder
    if (Test-Path $srcFolder) {
        Copy-Item $srcFolder -Destination $destFolder -Recurse -Force
        Write-Host "  + $folder\" -ForegroundColor Gray
    } else {
        Write-Host "  ! MISSING: $folder" -ForegroundColor Yellow
    }
}

# ============================================================
# STEP 3 - Copy database (exclude sqlite)
# ============================================================
Write-Host "`nCopying database (excluding sqlite)..." -ForegroundColor Cyan

$dbFolders = @("migrations", "seeders", "factories")
foreach ($folder in $dbFolders) {
    $srcFolder  = Join-Path $source "database\$folder"
    $destFolder = Join-Path $dest   "database\$folder"
    if (Test-Path $srcFolder) {
        Copy-Item $srcFolder -Destination $destFolder -Recurse -Force
        Write-Host "  + database\$folder\" -ForegroundColor Gray
    }
}

# ============================================================
# STEP 4 - Copy public (exclude hot and build.zip)
# ============================================================
Write-Host "`nCopying public folder (excluding dev files)..." -ForegroundColor Cyan

$publicExclude = @("hot", "build.zip")
Get-ChildItem -Path "$source\public" | Where-Object {
    $_.Name -notin $publicExclude
} | ForEach-Object {
    $destPath = Join-Path $dest "public"
    Copy-Item $_.FullName -Destination $destPath -Recurse -Force
    Write-Host "  + public\$($_.Name)" -ForegroundColor Gray
}

# ============================================================
# STEP 5 - Copy storage (user files only, empty runtime dirs)
# ============================================================
Write-Host "`nSetting up storage folder..." -ForegroundColor Cyan

# User uploaded content - copy with files
$storageWithFiles = @(
    "app\public\ai-avatars",
    "app\public\profile",
    "app\public\schools",
    "app\public\system",
    "app\public\admission-letters",
    "app\backups"
)
foreach ($folder in $storageWithFiles) {
    $srcFolder  = Join-Path $source "storage\$folder"
    $destFolder = Join-Path $dest   "storage\$folder"
    if (Test-Path $srcFolder) {
        Copy-Item $srcFolder -Destination $destFolder -Recurse -Force
        Write-Host "  + storage\$folder\" -ForegroundColor Gray
    } else {
        # Create empty folder anyway
        New-Item -ItemType Directory -Force -Path $destFolder | Out-Null
        Write-Host "  + storage\$folder\ (created empty)" -ForegroundColor DarkGray
    }
}

# Runtime folders - create empty (server will fill these)
$storageEmptyFolders = @(
    "framework\cache\data",
    "framework\sessions",
    "framework\views",
    "logs"
)
foreach ($folder in $storageEmptyFolders) {
    $destFolder = Join-Path $dest "storage\$folder"
    New-Item -ItemType Directory -Force -Path $destFolder | Out-Null
    # Add .gitkeep so folder isn't empty in zip
    New-Item -ItemType File -Force -Path "$destFolder\.gitkeep" | Out-Null
    Write-Host "  + storage\$folder\ (empty)" -ForegroundColor DarkGray
}

# ============================================================
# STEP 6 - Clean up junk files from copied folders
# ============================================================
Write-Host "`nCleaning up unwanted files..." -ForegroundColor Cyan

$junkFiles = @(
    "$dest\resources\views.zip",
    "$dest\resources\views (2).zip",
    "$dest\resources\lang.zip",
    "$dest\config\app-config.json"
)
foreach ($file in $junkFiles) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "  - Removed: $(Split-Path $file -Leaf)" -ForegroundColor DarkGray
    }
}

# ============================================================
# DONE
# ============================================================
Write-Host "`n============================================================" -ForegroundColor Green
Write-Host " Production folder ready!" -ForegroundColor Green
Write-Host " Location: $dest" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "  1. Edit $dest\.env with production values" -ForegroundColor White
Write-Host "  2. Zip the folder and upload to your server" -ForegroundColor White
Write-Host "  3. On server run: php artisan storage:link" -ForegroundColor White
Write-Host "  4. On server run: php artisan migrate --force" -ForegroundColor White
Write-Host "  5. On server run: php artisan optimize" -ForegroundColor White
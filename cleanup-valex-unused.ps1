# Valex Cleanup Script - Removes unused assets from public/
# Run from project root. Keeps only assets referenced in resources/views and style.css.

$ErrorActionPreference = "Stop"
$root = $PSScriptRoot
$public = Join-Path $root "public"

if (-not (Test-Path $public)) {
    Write-Host "public/ not found. Aborting." -ForegroundColor Red
    exit 1
}

Write-Host "Valex cleanup: removing unused files from public/ ..." -ForegroundColor Cyan

# 1. Delete entire public/html (Valex demo pages)
$html = Join-Path $public "html"
if (Test-Path $html) {
    Remove-Item -Path $html -Recurse -Force
    Write-Host "  Deleted: public/html/" -ForegroundColor Green
}

# 2. Delete entire public/assets/scss (source; style.css is compiled)
$scss = Join-Path $public "assets\scss"
if (Test-Path $scss) {
    Remove-Item -Path $scss -Recurse -Force
    Write-Host "  Deleted: public/assets/scss/" -ForegroundColor Green
}

# 3. Delete unused JS files (keep only 7)
$keepJs = @('main.js','defaultmenu.js','switch.js','sticky.js','custom-switcher.js','custom.js','us-merc-en.js')
$jsDir = Join-Path $public "assets\js"
if (Test-Path $jsDir) {
    $removed = 0
    Get-ChildItem $jsDir -File | Where-Object { $_.Name -notin $keepJs } | ForEach-Object {
        Remove-Item $_.FullName -Force
        $removed++
    }
    Write-Host "  Deleted $removed unused files from public/assets/js/" -ForegroundColor Green
}

# 4. Delete unused libs folders (keep only 6)
$keepLibs = @('@popperjs','@simonwep','apexcharts','jsvectormap','preline','simplebar')
$libsDir = Join-Path $public "assets\libs"
if (Test-Path $libsDir) {
    Get-ChildItem $libsDir -Directory | Where-Object { $_.Name -notin $keepLibs } | ForEach-Object {
        Remove-Item $_.FullName -Recurse -Force
        Write-Host "  Deleted: public/assets/libs/$($_.Name)/" -ForegroundColor Green
    }
}

# 5. Delete unused images folder (crypto-currencies not referenced)
$crypto = Join-Path $public "assets\images\crypto-currencies"
if (Test-Path $crypto) {
    Remove-Item -Path $crypto -Recurse -Force
    Write-Host "  Deleted: public/assets/images/crypto-currencies/" -ForegroundColor Green
}

Write-Host "Cleanup finished." -ForegroundColor Cyan

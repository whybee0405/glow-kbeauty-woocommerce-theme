<#
.SYNOPSIS
  Deploys kbeauty-theme to the live server via FTP.

  First-time setup
  ----------------
  1. Install WinSCP (free): https://winscp.net/eng/download.php
  2. Copy .deploy-config.example.ps1  →  .deploy-config.ps1
  3. Fill in your FTP credentials in .deploy-config.ps1

  Usage
  -----
  .\deploy.ps1
#>

$ErrorActionPreference = "Stop"

# ── Load credentials ────────────────────────────────────────────────────────
$configFile = Join-Path $PSScriptRoot ".deploy-config.ps1"
if (-not (Test-Path $configFile)) {
    Write-Host ""
    Write-Host "  Setup needed:" -ForegroundColor Yellow
    Write-Host "  Copy .deploy-config.example.ps1  to  .deploy-config.ps1" -ForegroundColor Yellow
    Write-Host "  then fill in your FTP host, username and password." -ForegroundColor Yellow
    Write-Host ""
    exit 1
}
. $configFile

# ── Find WinSCP ─────────────────────────────────────────────────────────────
$candidates = @(
    "C:\Program Files (x86)\WinSCP\WinSCP.com",
    "C:\Program Files\WinSCP\WinSCP.com",
    "$env:LOCALAPPDATA\Programs\WinSCP\WinSCP.com"
)
$winscp = $candidates | Where-Object { Test-Path $_ } | Select-Object -First 1

if (-not $winscp) {
    Write-Host ""
    Write-Host "  WinSCP not found." -ForegroundColor Yellow
    Write-Host "  Download from: https://winscp.net/eng/download.php" -ForegroundColor Yellow
    Write-Host ""
    exit 1
}

# ── Paths ────────────────────────────────────────────────────────────────────
$localTheme  = "D:\Dev Projects\cosmetics-woocommerce-theme\kbeauty-theme"
$remoteTheme = $RemotePath   # defined in .deploy-config.ps1

# ── Run sync ─────────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "  Deploying kbeauty-theme  →  $FtpHost" -ForegroundColor Cyan

$ftpUrl = "ftp://${FtpUser}:${FtpPass}@${FtpHost}/"

$script = @"
open $ftpUrl
synchronize remote -delete "$localTheme" "$remoteTheme"
exit
"@

$tmp = [System.IO.Path]::GetTempFileName() + ".winscp"
$script | Out-File -FilePath $tmp -Encoding utf8

try {
    & $winscp /script=$tmp /log="$PSScriptRoot\deploy.log"
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  Done." -ForegroundColor Green
        Write-Host ""
    } else {
        Write-Host "  Failed — check deploy.log for details." -ForegroundColor Red
        Write-Host ""
        exit $LASTEXITCODE
    }
} finally {
    Remove-Item $tmp -ErrorAction SilentlyContinue
}

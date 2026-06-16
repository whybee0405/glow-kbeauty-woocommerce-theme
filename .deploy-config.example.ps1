# Copy this file to .deploy-config.ps1 and fill in your details.
# .deploy-config.ps1 is listed in .gitignore — never commit it.

$FtpHost    = "ftp.whybee.co.za"          # FTP hostname — try your domain if this fails
$FtpUser    = "kbapfood"                  # cPanel / FTP username
$FtpPass    = "your-ftp-password-here"    # FTP password (same as cPanel login)
$RemotePath = "/home/kbapfood/public_html/whybee.co.za/k-beauty-ecommerce/wp-content/themes/kbeauty-theme"

<?php
/**
 * Run this script once to generate your admin password hash.
 * Usage: php generate-hash.php
 * Paste the output into config/config.php as ADMIN_PASSWORD_HASH.
 * Delete this file before deploying to production.
 */
echo "Enter your admin password: ";
$password = trim(fgets(STDIN));

if (strlen($password) < 8) {
    echo "Error: password must be at least 8 characters.\n";
    exit(1);
}

$sha256 = hash('sha256', $password);
$bcrypt = password_hash($sha256, PASSWORD_BCRYPT, ['cost' => 12]);

echo "\nYour bcrypt hash (paste into config/config.php):\n";
echo $bcrypt . "\n\n";
echo "Define: define('ADMIN_PASSWORD_HASH', '" . $bcrypt . "');\n";

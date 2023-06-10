<?php
require './functions.php';

if (empty($_GET['username']) || empty($_GET['password'])) reject('Missing "username" or "password" param');

require_once('./vendor/autoload.php');

$google2fa = new \PragmaRX\Google2FA\Google2FA();

$hash = password_hash($_GET['password'], PASSWORD_ARGON2ID);
$code = $google2fa->generateSecretKey(64);
$totp = $google2fa->getQRCodeUrl(
  'ZSM TimeTable API',
  $_GET['username'],
  $code
);

echo 'Password hash:<br />' . $hash . '<br /><br />TOTP Secret:<br />' . $code . '<br />';
echo '<img src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . $totp . '" />';

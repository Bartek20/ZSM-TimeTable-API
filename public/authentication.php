<?php

declare(strict_types=1);

use Firebase\JWT\JWT;

require_once('./vendor/autoload.php');

if ($_SERVER['SCRIPT_FILENAME'] === __FILE__) {
	header('HTTP/1.1 404 Not Found');
	exit;
}

$SERVER_SALT = 'X$qZlhHzo7)zwXkVXQB1HS%h57g%Er$qFuJi$zx()J3HBxEN(j)$)HtqX7bP8X&A';

function createSession($username) {
	global $SERVER_SALT;
	$tokenID = base64_encode(random_bytes(16));
	$now = new DateTimeImmutable();
	$current = $now->getTimestamp();
	$expire = $now->modify('+10 minutes')->getTimestamp();

	$data = [
		'iat'  => $current,
		'jti'  => $tokenID,
		'iss'  => $_SERVER['HTTP_HOST'],
		'nbf'  => $current,
		'exp'  => $expire,
		'data' => [
			'user' => $username,
		]
	];
	return array(
		'token' => JWT::encode($data, $SERVER_SALT, 'HS512'),
		'expire' => $expire
	);
}

function validateSession() {
	global $SERVER_SALT;
	if (!isset($_SERVER['HTTP_AUTHORIZATION']) or !preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
		return false;
	}
	$jwt = $matches[1];
	if (!$jwt) {
		return false;
	}
	JWT::$leeway += 60;
	try {
		$token = JWT::decode($jwt, $SERVER_SALT, ['HS512']);
	} catch (Exception $e) {
		return false;
	}
	if ($token->iss !== $_SERVER['HTTP_HOST']) {
		return false;
	}
	return $token->data;
}

<?php
require './authentication.php';
require './functions.php';
require './users.php';

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
	reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}

if (empty($_POST['username']) || empty($_POST['password'])) {
	reject('Odmowa dostępu', 401, 'Unauthorized');
}

function authenticateUser($username, $password) {
	global $users;
	$userData = array_key_exists($username, $users) ? $users[$username] : null;
	if ($userData !== null && password_verify($password, $userData['password'])) {
		return true;
	}
	return false;
}

$username = $_POST['username'];
$password = $_POST['password'];

$session = validateSession();
if ($session) {
	echo 'Uwierzytelnienie z użyciem sesji. Witaj, ' . $session->user . '!';
	exit;
}
if (authenticateUser($username, $password)) {
	$jwt = createSession($username);
	header('X-Token-JWT: ' . $jwt['token']);
	header('X-Token-EXP: ' . $jwt['expire']);
	echo 'Uwierzytelnienie powiodło się. Witaj, ' . $username . '!';
	exit;
}
reject('Odmowa dostępu', 401, 'Unauthorized');

<?php
require './authentication.php';
require './functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
  reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

if (validateSession()) {
  isset($_FILES['file']) ? $file = $_FILES['file'] : reject('Żaden plik nie został wybrany');
  isset($file['name']) ? $filename = $file['name'] : reject('Plik nie posiada prawidłowej nazwy');
  if (in_array($filename, array('.htaccess', 'upload.php', 'login.php', 'authentication.php'))) reject('Ten plik nie może zostać edytowany... To dla twojego bezpieczeństwa :)');
  if (in_array($filename, array('index.html', 'lista.html'))) $path = '';
  elseif (getExt($filename) == '.js') $path = 'scripts';
  elseif (getExt($filename) == '.css') $path = 'css';
  elseif (getExt($filename) == '.html') $path = 'plany';
  elseif (getExt($filename) == '.gif' || getExt($filename) == '.jpg') $path = 'images';
  elseif (isset($_POST['admin']) && $_POST['admin'] == 'kuzniar.bartlomiej20@gmail.com') $path = 'ADMIN';
  else reject('Podany plik nie jest dozwolony');
  $path == 'ADMIN' ? $path = isset($_POST['path']) ? $_POST['path'] : './' : $path = '/timetable/' . $path;
  if (mb_strpos($path, 'vendor') !== false) reject('Ta ścieżka nie może być edytowana... To dla twojego bezpieczeństwa :)');
  $path = getPath($path);
  if (!file_exists($path)) mkdir($path, 0777, true);
  $path = getPath(str_replace('..', '', $path), $filename);
  if (move_uploaded_file($file['tmp_name'], $path)) {
    header('HTTP/1.1 201 Success');
    echo 'Sukces, Plik został wgrany do ' . str_replace('..', '', $path);
  } else reject('Wystąpił problem przy wgrywaniu pliku');
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

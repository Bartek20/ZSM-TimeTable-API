<?php
require './authentication.php';
require './functions.php';

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
  reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}

if (validateSession()) {
  $file = getPath($_POST['path']);
  $dir = str_replace('../', '', $file);
  if (substr($dir, 0, 2) != './' && !in_array(substr($dir, 0, strpos($dir, '/')), array('plan_lekcji', 'plan_api'))) reject('Odmowa wykonania żądania');
  echo 'OK';
  exit;
  if (is_dir($file)) {
    rrmdir($file);
    echo 'Folder został usunięty';
    exit;
  }
  if (!file_exists($file)) {
    reject('Plik nie istnieje.');
  }
  if (unlink($file)) {
    echo 'Plik został usunięty.';
    exit;
  }
  reject('Nie udało się usunąć pliku.');
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

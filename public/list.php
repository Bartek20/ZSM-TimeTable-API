<?php
require './authentication.php';
require './functions.php';

// Validate Method
if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
  reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}

if (empty($_POST['path'])) {
  reject('Brak wymaganych danych');
}

// Process request
if (validateSession()) {
  $path = getPath($_POST['path']);
  if (!is_dir($path)) reject('Podana ścieżka nie jest prawidłową ścieżką folderu');
  $files = scandir($path);
  print_r($files);
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

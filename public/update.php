<?php
require './authentication.php';

// Validate Method
if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
  reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}

// Validate required data
if (empty($_POST['data'])) {
  reject('Brak wymaganych danych');
}

// Process request
if (validateSession()) {
  file_put_contents('../schoolData.json', $_POST['data']);
  echo 'Zaktualizowano ./schoolData.json<br />';
  header('HTTP/1.1 201 Success');
  echo 'OK';
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

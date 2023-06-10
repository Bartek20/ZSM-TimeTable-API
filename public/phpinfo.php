<?php
require './authentication.php';

// Validate Method
if (!($_SERVER['REQUEST_METHOD'] === 'GET')) {
  reject('Metoda nie jest dozwolona', 405, 'Method Not Allowed');
}


// Process request
if (validateSession()) {
  echo phpinfo();
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

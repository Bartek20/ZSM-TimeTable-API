<?php
require './authentication.php';
require './functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}

if (empty($_POST['command'])) reject('Command not selected...');

if (validateSession()) {
  switch ($_POST['command']) {
    case 'update':
      rcopy('../plan_lekcji', '../plan_lekcji.old');
      rrmdir('../plan_lekcji');
      break;
    case 'upload':
      if (!file_exists('../plan_lekcji.old')) {
        rcopy('../plan_lekcji', '../plan_lekcji.old');
        rrmdir('../plan_lekcji');
      }
      $file = $_FILES['file'];
      $filename = $file['name'];
      if (!$filename) reject('Nie wybrano żadnego pliku');
      if (!in_array(getExt($filename), array('.htaccess', '.xml', '.ico', '.html', '.webmanifest', '.txt', '.json', '.js', '.css', '.ttf', '.woff', '.woff2', '.png', '.svg', '.jpg'))) reject('Wybrany format pliku nie jest dozwolony');
      $path = '../plan_lekcji/' . str_replace($filename, '', $_POST['fullPath']);
      if (!file_exists($path)) mkdir($path, 0777, true);
      move_uploaded_file($file['tmp_name'], $path . $filename);
      break;
    case 'approve':
      rrmdir('../plan_lekcji.old');
      break;
    case 'decline':
      if (is_dir('../plan_lekcji.old')) rcopy('../plan_lekcji.old', '../plan_lekcji');
      rrmdir('../plan_lekcji.old');
      break;
    case 'backup':
      rcopy('../plan_lekcji', '../plan_lekcji.old');
      break;
    case 'rm_backup':
      rrmdir('../plan_lekcji.old');
      break;
    default:
      reject('Unknown command "' . $_POST['command'] . '"');
  }
} else {
  reject('Odmowa dostępu', 401, 'Unauthorized');
}

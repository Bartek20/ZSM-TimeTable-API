<?php
if ($_SERVER['SCRIPT_FILENAME'] === __FILE__) {
  header('HTTP/1.1 404 Not Found');
  exit;
}

// Return response rejection
function reject($reason, $code = 400, $msg = 'Bad Request') {
  header("HTTP/1.1 $code $msg");
  echo $reason;
  exit;
}

// Returns given file extension
function getExt($filename) {
  return '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Returns concatenated path to file
function getPath($folder, $file = '') {
  $folder = rtrim($folder, '/');
  if (mb_strpos($folder, '..') !== false) reject('Wykorzystanie ścieżki rodzica zostało zablokowane');
  if ($folder == '') $folder = '..';
  if ($folder[0] == '/') $folder = '..' . $folder;
  if ($folder[0] != '.' && $folder[0] != '/') $folder = './' . $folder;
  $file = ltrim($file, '/');
  return $folder . '/' . $file;
}

// Remove files / directories recursively
function rrmdir($dir) {
  if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $file)
      if ($file != "." && $file != "..") rrmdir("$dir/$file");
    rmdir($dir);
  } else if (file_exists($dir)) unlink($dir);
}

// Copy files / directories recursively
function rcopy($src, $dst) {
  if (file_exists($dst)) rrmdir($dst);
  if (is_dir($src)) {
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file)
      if ($file != "." && $file != "..") rcopy("$src/$file", "$dst/$file");
  } else if (file_exists($src)) copy($src, $dst);
}

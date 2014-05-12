<?php
require_once('includes/database.php');

$file_id = $_GET['id'];

$results = $database->query(sprintf("SELECT name, author, published_date, location, type FROM books LEFT JOIN book_metas ON books.book_meta_id = book_metas.id WHERE books.id = %s", $file_id));

if($results->num_rows == 1) {
} else {
  // Display file not found
  echo "File not found";
  exit();
}
$results->data_seek(0);
$row = $results->fetch_assoc();

$file = $row['location'];

$year = '';
if($row['published_date'] !== '') {
  $date = date_create($row['published_date']);
  $year = ' (' . $date->format('Y') . ')';
}

$converted = false;
if (isset($_GET['convert']) &&  $_GET['convert'] == true) {
  preg_match("/^(\/.*\/)(.*)\.[epub|mobi]/", $row['location'], $file_name);
  if($row['type'] == 'mobi')
    $new_format = 'epub';
  else if($row['type'] == 'epub')
    $new_format = 'mobi';
  else {
    echo "Error";
    exit();
  }
  $file_name = $file_name[2] . '.' . $new_format;
  shell_exec('ebook-convert "'. $row['location'] . '" "tmp/' . $file_name .'"');
  $file = 'tmp/' . $file_name;
  $row['type'] = $new_format;
  $converted = true;
}
$filename = $row['name'] . ' - ' . $row['author'] . $year . '.' . $row['type'];
$filename = str_replace(',', '', $filename);

if (file_exists($file)) {
 if (filesize($file) != 0)
 {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    if ($converted)
      unlink($file);
    exit;
 }
 else
  echo "File is corrupt!";
}
else
  echo "File doesn't exist";

?>

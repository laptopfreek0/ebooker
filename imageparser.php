<?php
  require_once('includes/database.php');

  $file_id = urldecode($_GET['image_id']);
  $results = $database->query(sprintf("SELECT image_location FROM book_metas WHERE id = %s", $file_id));

  if($results->num_rows == 1) {
  } else {
    // Display image not found
    $file = 'img/generic_cover.jpg';
  }
  $results->data_seek(0);
  $row = $results->fetch_assoc();

  $file = $row['image_location'];

  if (!file_exists($file)) {
    // Display generic image
    $file = 'img/generic_cover.jpg';
  }

  $contents = file_get_contents($file);
  header('Content-type: image/jpeg');
  echo $contents;
?>

<?php
require_once('includes/database.php');
require_once('includes/phpmailer/class.phpmailer.php');

$file_id = $_POST['book_id'];

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
if($row['type'] != 'mobi' && $row['type'] == 'epub') {
  preg_match("/^(\/.*\/)(.*)\.epub/", $row['location'], $file_name);
  $file_name = $file_name[2] . ".mobi";
  echo $file_name;
  shell_exec('ebook-convert "'. $row['location'] . '" "tmp/' . $file_name .'"');
  $file = 'tmp/' . $file_name;
  $row['type'] = 'mobi';
  $converted = true;
}

$filename = $row['name'] . ' - ' . $row['author'] . $year . '.' . $row['type'];

// Mailer
$mail = new PHPMailer();

// Santitize me
$to = $_POST['email'];
setcookie("amazon-email", $to, time()+3600*24*120);

if($config['Email']['email_smtp'])
  $mail->IsSMTP();
if($config['Email']['email_ssl'])
  $mail->SMTPSecure = "tls";
$mail->Host = $config['Email']['email_host'];
$mail->Port = $config['Email']['email_port'];
if($config['Email']['email_auth_required']) {
  $mail->SMTPAuth = true;
  $mail->Username = $config['Email']['email_username'];
  $mail->Password = $config['Email']['email_password'];
}
$mail->SetFrom($config['Email']['email_address']);
$mail->Subject = '';
$mail->Body = 'Ebook';
$mail->Addaddress($to);
$mail->AddAttachment($file, $filename);
if(!$mail->Send())
  echo "Mailer Error:" . $mail->ErrorInfo;
else
  echo "Book sent.";

if ($converted)
  unlink($file);
?>

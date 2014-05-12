
<?php
  require_once('includes/database.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <?php
     require_once("includes/head.php");
   ?>
  </head>

  <body>
    <?php
      require_once("includes/navbar.php");
    ?>

    <?php
      if(!isset($_GET['id'])) {
       echo "Unknown Book";
       exit();
      }
      $results = $database->query(sprintf("SELECT * FROM book_metas WHERE id = %s", $_GET['id']));
      $results->data_seek(0);
      $result = $results->fetch_assoc();

    ?>
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <br /><br />
    <div class="container">
      <div class="row">
        <div class="col-md-4">
         <?php echo "<a class='item' href='bookdetails.php?id=" . $result['id'] ."'><img class='rounded-image' height='450px' width='292' src='imageparser.php?image_id=" . $result['id'] .  "' /></a>"; ?>
        </div>
        <div class="col-md-4">
         <div class="book-title"><strong><?php echo $result['name']; ?></strong></div>
         <div class="book-author"><?php echo $result['author']; ?></div><br />
         <div class="book-info"><strong>Publisher:</strong> <?php echo $result['publisher']; ?></div>
         <div class="book-info"><strong>Published Date: </strong><?php echo $result['published_date']; ?></div>
         <div class="book-info"><strong>Description: </strong><?php echo $result['description']; ?></div>
       </div>
       <div class="col-md-4">
         <?php
           $results = $database->query(sprintf("SELECT type, id FROM books where book_meta_id = %s ORDER BY type ASC", $_GET['id']));
           $results->data_seek(0);
           $books = array();
           $formats = array('epub', 'mobi');
           while ($row = $results->fetch_assoc()) {
             $books[$row['type']] = $row['id'];
           }
           foreach ($formats as $format) {
             if(isset($books[$format])) {
               echo "<a class='btn btn-primary btn-lg' role='button' href='downloader.php?id=". $books[$format] ."&convert=false'>Download " . $format . "</a><br /><br />";
             } else {
               if ($format == 'mobi')
                 $otherformat = 'epub';
               else if ($format == 'epub')
                 $otherformat = 'mobi';
               else
                 $otherformat = 'err';
               echo "<a class='btn btn-warning btn-lg' role='button' href='downloader.php?id=". $books[$otherformat] ."&convert=true'>Convert " . $format . "</a><br /><br />";
             }
           }
         ?>
         <form name="amazon" action="emailer.php" method="POST" onsubmit="return amazon_email();">
           <div class="input-group form-group amazon-email">
             <input type="hidden" name="book_id" id="book_id" value="<?php
             if (isset($books['mobi'])) {
               echo $books['mobi'];
             } else {
               // For PDF only we need to disable the email
               echo $books['epub'];
             }
              ?>">
             <input type="text" placeholder="Amazon Email" class="form-control" name="email" id="email" <?php if(isset($_COOKIE['amazon-email'])) echo "value='" . $_COOKIE['amazon-email'] ."'"; ?>>
               <div class="input-group-btn">
                 <button type="submit" class="btn btn-default" id="email-btn"><span class="glyphicon glyphicon-envelope"></span></button>
               </div>
           </div>
         </form>
       <p>* Note you need to <a href="https://www.amazon.com/gp/digital/fiona/manage?ie=UTF8&ref_=ya_manage_kindle#pdocSettings">authorize</a> <?php echo $config['Email']['email_address']; ?> on amazon.com</p>
       </div>
      </div>
<?php
  require_once("includes/footer.php");
?>
  <script>
    function amazon_email() {
      var email = $('#email').val();
      if(email !== '') {
        $('#email-btn').attr('disabled', 'disabled');
        $('#email-btn').html("<img width='20px' src='img/spinner.gif'>");
        var email = $('#email').val();
        var book_id = $('#book_id').val();
        var map = {};
        map["book_id"] = book_id;
        map["email"] = email;
        $.post(
		      "mailer.php",
		      map, 
		      function (result) {
			      if (result.indexOf('Book sent.') != -1) {
              $('#email-btn').html("<span class='glyphicon glyphicon-ok text-success'></span>");
			      } else {
				      alert(result);
				      alert("An Error while emailing your Book.");
			      }
		      }
	      );
      } else {
        $('#email').parent().addClass('has-error');
      }
      return false;
    }
  </script>
  </body>
</html>


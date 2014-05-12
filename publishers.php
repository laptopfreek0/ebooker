
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
    <div class="container">
      <div class="row">
       <div class="col-md-8">
        <h2>Publishers</h2>
        <br /><br />
        <table class="table table-hover">
         <?php
           $results = $database->query("SELECT DISTINCT publisher as publisher FROM book_metas ORDER BY publisher ASC");
           $results->data_seek(0);
           while ($row = $results->fetch_assoc()) {
             echo "<tr><td><a href='books.php?publisher=" . $row['publisher'] . "'>" . $row['publisher'] . "</a></td></tr>";
           }
         ?>
        </table>
      </div>
    </div>
<?php
  require_once("includes/footer.php");
?>
  </body>
</html>


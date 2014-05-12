
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

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="carousel-body">
      <div class="container">
        <div id="scroller">
          <div class="nav">
            <a class="prev">&laquo;</a>
            <a class="next">&raquo;</a>
          </div>
        <?php
          $results = $database->query("SELECT * FROM book_metas ORDER BY date_added desc limit 0,20");
          $results->data_seek(0);
          while ($row = $results->fetch_assoc()) {
            echo "<a class='item' href='bookdetails.php?id=" . $row['id'] ."'><img height='250px' width='162px' src='imageparser.php?image_id=" . $row['id'] .  "' /></a>";
          }
        ?>
        </div>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <?php
            $results = $database->query("SELECT COUNT(sub.author) as author_count FROM (SELECT DISTINCT author FROM book_metas GROUP BY author) as sub");
            $results->data_seek(0);
            $row = $results->fetch_assoc();
          ?>
          <h2><a href="authors.php">Authors<span class="pull-right"><span class="badge vtop"><?php echo $row['author_count']; ?></span><span class="glyphicon glyphicon-user"></span></span></a></h2>
          <p>View an alphabetical list of the Authors in the serve database.</p>
        </div>
        <?php
          $results = $database->query("SELECT COUNT(id) as book_count FROM book_metas");
          $results->data_seek(0);
          $row = $results->fetch_assoc();
        ?>
        <div class="col-md-4">
            <h2><a href="books.php">Books<span class="pull-right"><span class="badge vtop"><?php echo $row['book_count']; ?></span><span class="glyphicon glyphicon-book"></span></span></a></h2>
          <p>View an alphabetical list of the Books in the serve database.</p>
       </div>
       <?php
         $results = $database->query("SELECT COUNT(sub.publisher) as publisher_count FROM (SELECT DISTINCT publisher FROM book_metas GROUP BY publisher) as sub");
         $results->data_seek(0);
         $row = $results->fetch_assoc();
       ?>
       <div class="col-md-4">
          <h2><a href="publishers.php">Publishers<span class="pull-right"><span class="badge vtop"><?php echo $row['publisher_count']; ?></span><span class="glyphicon glyphicon-print"></span></span></a></h2>
          <p>View a list of the Publishers.</p>
        </div>
      </div>
<?php
  require_once("includes/footer.php");
?>
  </body>
</html>


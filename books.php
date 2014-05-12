
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
    <br />
    <div class="container">
    <div class="row">
      <div class="col-md-2">
      </div>
      <div class="col-md-6">
      <h2>Books</h2>
      <br />
      <button type="button" class="btn btn-default sort-by-button" disabled>Sort By:</button>
      <?php
        $sorts = array('date_added' => 'Date Added', 'name' => 'Title', 'author' => 'Author');
        foreach ($sorts as $key => $sort) {
          $btn_color = 'btn-default';
          $btn_icon = '';
          $btn_direction='asc';

          if(isset($_GET['sortby']) && $_GET['sortby'] == $key) {
            $btn_color = 'btn-primary';
            if ($_GET['sortdirection'] == 'asc') {
              $btn_icon='<span class="glyphicon glyphicon-chevron-up"></span> ';
              $btn_direction='desc';
            } else
              $btn_icon='<span class="glyphicon glyphicon-chevron-down"></span> ';
          }
          $link = '?sortby=' . $key . '&sortdirection=' . $btn_direction;
          if(!empty($_GET)) {
            foreach ($_GET as $key => $value) {
              if($key != 'sortdirection' && $key != 'sortby')
                $link .= "&" . $key . "=" . urlencode($value);
            }
          }

          echo '<a class="sort-button" href="books.php'. $link .'"><button type="button" class="btn ' . $btn_color . '">' . $btn_icon . $sort . '</button></a>';
        }
      ?>
        <div class="pull-right btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">10 <span class="caret"></span></button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">5</a></li>
            <li><a href="#">10</a></li>
            <li><a href="#">25</a></li>
            <li><a href="#">50</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-2">
      </div>
    </div>
    <br /><br />
      <?php
         $orderby = 'date_added DESC';
         $book_count = 10;
         $page = '0,' . $book_count;
         if(isset($_GET['sortby']) && isset($_GET['sortdirection']))
           $orderby = $_GET['sortby'] . ' ' . $_GET['sortdirection'];
         if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
           $lower_limit = ($_GET['page'] - 1) * $book_count;
           $page = $lower_limit . ','  . $book_count;
         }
         $search = '';
         if(isset($_GET['search']))
           $search = sprintf("WHERE name LIKE '%%%s%%' OR author LIKE '%%%s%%' OR publisher LIKE '%%%s%%'", $_GET['search'], $_GET['search'], $_GET['search']);
         if(isset($_GET['publisher'])) {
           if($search == '')
             $search = "WHERE publisher = '" . $_GET['publisher'] . "'";
           else
             $search = " AND publisher = '" . $_GET['publisher'] . "'";
         }
         if(isset($_GET['author'])) {
           if($search == '')
             $search = "WHERE author = '" . $_GET['author'] . "'";
           else
             $search = " AND author = '" . $_GET['author'] . "'";
         }
         $results = $database->query(sprintf("SELECT * FROM book_metas %s ORDER BY %s LIMIT %s", $search, $orderby, $page));
         $results->data_seek(0);
         if($results->num_rows == 0){
         echo '
           <div class="row">
             <div class="col-md-2">
             </div>
             <div class="col-md-6">
               <h3>No books found :(</h3>
             </div>
             <div class="col-md-2">
             </div>
           </div>';
         } else {
         while ($row = $results->fetch_assoc()) {
         echo '<div class="row">
                 <div class="col-md-2">
                 </div>
                 <div class="col-md-6">
                   <div class="book">
                     <div class="col-md-4 book-image">
                       <a href="bookdetails.php?id=' . $row['id'] .'"><img height="250px" width="162px" src="imageparser.php?image_id=' . $row['id'] .  '" /></a>
                     </div>
                     <div class="book-title-smaller">' . $row['name'] . '</div>
                     <div class="book-author-smaller">' . $row['author'] . '</div>
                   </div>
                 </div>
               </div>';
         }
      ?>
      <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
          <ul class="pagination">
            <?php
              $link = '';
              if(!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                  if($key != 'page')
                    $link .= "&" . $key . "=" . urlencode($value);
                }
              }
            ?>
            <li><a href="<?php echo 'books.php?page=1' . $link; ?>">&laquo;</a></li>
            <?php
              $results = $database->query(sprintf("SELECT COUNT(*) book_count FROM book_metas %s", $search));
              $results->data_seek(0);
              $row = $results->fetch_assoc();
              $page = 1;
              $total_pages = ceil($row['book_count'] / $book_count);
              if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0)
                $page = $_GET['page'];
              $lower_limit = ($page <= 5) ? 1 : $page - 5;
              // Note fix the issue where the last page only shows 5 instead of 10
              for ($i=0; $i < 10; $i++) {
                $temp_page = $i + $lower_limit;
                $active = ($page == $temp_page) ? ' class="active"' : '';
                echo "<li" . $active ."><a href='books.php?page=" . $temp_page . $link . "'>" . $temp_page . "</a></li>";
                if($row['book_count'] / ($book_count * $temp_page) < 1)
                  break;
              }
            ?>
            <li><a href="<?php echo 'books.php?page=' . $total_pages . $link ?>">&raquo;</a></li>
          </ul>
        </div>
      </div>
      <?php } ?>
<?php
  require_once("includes/footer.php");
?>
  </body>
</html>


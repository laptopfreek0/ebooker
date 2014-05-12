<?php
echo '<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Ebooker</a>
        </div>
        <!-- Commented one is for mobile -->
        <!-- <div class="col-sm-3 col-md-3 pull-right collapse navbar-collapse"> -->
        <div class="col-sm-3 col-md-3 pull-right">
        <form class="navbar-form" method="get" action="books.php" role="search">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" name="search" id="srch-term">
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
            </div>
        </div>
        </form>
        </div><!--/.navbar-collapse -->
      </div>
    </div>';
?>


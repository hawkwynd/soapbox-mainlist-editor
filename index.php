<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try{
    $cookiename = "jarviskey";
    $key    = isset($_COOKIE[$cookiename]) ? $_COOKIE[$cookiename] : null;
    $user_name = $_COOKIE['user_name'];
    if($key === null ) throw new Exception( ' No KEY FOUND! You have no authority here, Gandalf.' );
}catch(Exception $e){
    die( $e->getMessage() );
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jarvis Main List Editor</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   
    <!-- fontawesome -->
    <script src="https://kit.fontawesome.com/a48e0463c7.js" crossorigin="anonymous"></script>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="container-fluid">

<!-- float container -->
<div class="bs-example">
    <div class="toast" id="myToast" style="position: absolute; top: 5px; right: 20px;" >
        <div class="toast-header"></div>
        <div class="toast-body"></div>
    </div>
</div>
<!-- end float container -->

    <section>
        <div class="row">
            <div class="col-md-5"><h2 id="title">Jarvis List Editor </h2></div>
            <div class="col-md-2 mt-2">
                <?php echo "Welcome $user_name"; ?>
            </div>
        </div>
    </section>

    <section class="header">
        <div class="listinfo">
        </div>
    </section>

    <section>
        <form class="row row-cols-lg-auto mt-3">
                <div class="cols-5">
                    <div class="input-group">
                        <input class="form-control" type="search" name="title" id="title" placeholder="Search titles">                     
                        <input type="hidden" name="action" value="search">
                    </div>
                    <div class="form-check form-switch mt-2 mb-2">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="artist">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Artists</label>    
                            <span>&nbsp; &nbsp;</span>
                            <input type="checkbox" class="form-check-input" id="flexSwitchAlbum" name="album">
                            <label class="form-check-label" for="flexSwitchAlbum">Albums</label>
                    </div>
                </div>
                <div class="col">
                    <button id="goBtn" type="submit" class="btn btn-success">GO</button>
                    <button class="btn btn-primary  btn-in_list">Show All In Main</button>
                </div>
                <div class="col">
                </div>
        </form>
    </section>

    <div class="px-4">

            <div id="results"></div>             
    </div>

</div><!--container-->

<!-- Info Modal  -->

<div class="modal" id="infoModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-light"></h5>
      </div>
      
      <div class="modal-body">
            <div class="modal-album"></div>
            <div class="modal-genre"></div>
            <div class="modal-playtime"></div>
            <div class="modal-comment"></div>

      </div>
      
      <div class="modal-footer">
          <span class="notify"></span>
          <button class="btn btn-danger delete">Delete</button>
         <button class="btn btn-secondary uBtn">Update DB</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="js/mainlist.js" type="text/javascript"></script>

</body>
</html>



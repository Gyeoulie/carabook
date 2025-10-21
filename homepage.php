<?php
require 'ver.php';


if (!isset($_SERVER['HTTP_REFERER']) && isset($_SESSION['logged_in']) != TRUE) {
  header('location:/');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="x-icon" href="image/logo.png">
  <title>Carpooling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>
  <?php include 'header.php'; ?>

  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="section hero" id="home">
        <div class="container">

          <div class="hero-content">
            <h2 class="h1 hero-title">Earn. Connect. Contribute to Society</h2>

            <p class="hero-text">
              Partner with us to drive your own livelihood and more.
            </p>
          </div>

          <div class="hero-banner"></div>

        </div>
      </section>

      <section class="sec-2">
        <div class="container">

          <h2 class="heading-2">How It Work</h2>
          <br>
          <div class="col3">
            <div class="box">
              <i class="fa fa-hand-o-up icon-1"></i>
              <h3>Book in just 2 Tabs</h3>
              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
            </div>
          </div>
          <div class="col3">
            <div class="box">
              <i class="fa fa-automobile icon-1"></i>
              <h3>Get a Driver</h3>
              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
            </div>
          </div>
          <div class="col3">
            <div class="box">
              <i class="fa fa-map-o icon-1"></i>
              <h3>Track Your Driver</h3>
              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            </div>
          </div>
          <div class="col3">
            <div class="box">
              <i class="fa fa-user icon-1"></i>
              <h3>Arrive Safely</h3>
              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
            </div>
          </div>
          <div class="clearfix"></div>

        </div>
      </section>



      <!-- 
    - custom js link
  -->
      <script src="./js/script.js"></script>

      <!-- 
    - ionicon link
  -->
      <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
      <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
<?php
if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location:/');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel ="shortcut icon" type="x-icon" href="image/car.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
.wlogoutbtn{
    background-color: #ff0000; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
</style>


<body>
    <div align = "center">

            <h1>CHECK YOUR EMAIL FOR VERIFICATION!</h1>
            <hr width="50%">
            <a href="logout"><button  class="btn btn-danger wlogoutbtn">Logout</button></a>
        </fieldset>

    </div>
</body>
</html>

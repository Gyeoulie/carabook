<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]) && !isset($_SESSION['logged_in'])) {
    header("Location: /");
    exit;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <style>
        .logo {
            width: 60px;
            height: 60px;

        }

    </style>
</head>

<body>
    <!-- NAVIGATION BAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="logo carnav"><img src="image/logo.png" alt="" style="margin-top:25%;  margin-left: -20px;" ></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="homepage">Home </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile" href="profile">Profile</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="booking" href="booking">Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rcar" href="car">Register Car</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="mcar" href="mycar">My Cars</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="vrcar" href="viewregister">Car Registrations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="vacc" href="accounts">Verified Accounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="atransaction" href="transaction">View Transactions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="acashin" href="cashin">Cash In</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="acashout" href="cashout">Cash Out</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="areport" href="report">Report</a>
                </li>

            </ul>
            <ul class="navbar-nav logoutnav">
                <li class="nav-item">
                    <!-- PRINTS CURRENT USER BALANCE FROM SESSION -->
                    <a class="nav-link">Balance: <?=$_SESSION['UBAL']?> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
</body>

</html>

<!-- CONDITION FOR NAVIGATION BAR -->
<?php
if ($_SESSION['ulvl'] == 4) {
    ?>
    <script>
        document.getElementById("route").style.display = "block";
    </script>
<?php
}

if ($_SESSION['ulvl'] <= 4) {
    ?>
    <script>
        document.getElementById("booking").style.display = "block";
    </script>
<?php
}

if ($_SESSION['ulvl'] >= 3 && $_SESSION['ulvl'] < 6) {
    ?>
    <script>
        document.getElementById("rcar").style.display = "block";
        document.getElementById("mcar").style.display = "block";
    </script>
<?php
}

if ($_SESSION['ulvl'] >= 2 && $_SESSION['ulvl'] < 6) {
    ?>
    <script>
        document.getElementById("acashin").style.display = "block";
    </script>
<?php
}
if ($_SESSION['ulvl'] == 4) {
    ?>
    <script>
        document.getElementById("acashout").style.display = "block";
    </script>
<?php
}
if ($_SESSION['ulvl'] == 6) {
    ?>
    <script>
        document.getElementById("vrcar").style.display = "block";
        document.getElementById("vacc").style.display = "block";
        document.getElementById("atransaction").style.display = "block";
        document.getElementById("areport").style.display = "block";
    </script>
<?php
}
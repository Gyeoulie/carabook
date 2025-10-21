<?php
require 'ver.php';
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
    <link rel="shortcut icon" type="x-icon" href="image/car.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    //CHECKS IF CASH IN BUTTON IS PRESSED
    if (isset($_POST['cashin'])) {

        //SETS VARIABLES FROM INPUTS
        $CAMOUNT = $_POST['ticketamount'];
        $GNUM = $_POST['gnum'];
        $GREFNUM = $_POST['grefnum'];
        $USERID = $_SESSION['userID'];
        $TRANSTYPE = 'CASH IN';
        $INFEE = 0;

        //ASSIGN VALUES FOR THE SELECTED AMOUNT OF TICKETS/MONEY
        //INFEE IS CONVINI FEE 
        if ($CAMOUNT == 500) {
            $INFEE = 50;
        } else if ($CAMOUNT == 250) {
            $INFEE = 50;
        } else if ($CAMOUNT == 100) {
            $INFEE = 20;
        } else if ($CAMOUNT == 50) {
            $INFEE = 10;
        }

        //SQL STATEMENT FOR INSERT TO THE TRANSACTION TABLE
        $sql = ("INSERT INTO transaction_tbl (tuser_id, ttrans_type, tamount, tmobile_no, tref_no, tinfee) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isissi", $USERID, $TRANSTYPE, $CAMOUNT, $GNUM, $GREFNUM, $INFEE);
        if (mysqli_stmt_execute($stmt)) {

            //REDIRECT TO HOMEPAGE IF SUCCESS
            echo "<script>
        function alertAndRedirect() {
       alert('Cash In Successful, Wait for verification');
       window.location.href = 'homepage';
        }
        alertAndRedirect();
     </script>";
        } else {

            //PRINTS ERROR IF UNSUCCESSFUL 
            echo "<script>alert('ERROR!');</script>";
        }
    }

    ?>
    <?php include 'header.php'; ?>
    <!-- CASH IN FORM / CONTAINER -->
    <style>
        .cashin {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
    </head>

    <body>
        <div class="container">
            <div class="card cashin">
                <div class="container">
                    <form class="row g-3" id="form1" action="" method="post">
                        <div class="col-md-12">
                            <fieldset class="row mb-3">
                                <legend class="col-form-label col-sm-12">
                                    <h1>Ticket Amount</h1>
                                </legend>
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ticketamount" id="gridRadios1" value="50" required>
                                        <label class="form-check-label" for="gridRadios1">
                                            50 = 40 Tickets <i class="fas fa-ticket-alt"></i>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ticketamount" id="gridRadios2" value="100">
                                        <label class="form-check-label" for="gridRadios2">
                                            100 = 80 Tickets <i class="fas fa-ticket-alt"></i>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ticketamount" id="gridRadios3" value="250">
                                        <label class="form-check-label" for="gridRadios3">
                                            250 = 200 Tickets <i class="fas fa-ticket-alt"></i>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ticketamount" id="gridRadios4" value="500">
                                        <label class="form-check-label" for="gridRadios4">
                                            500 = 450 Tickets <i class="fas fa-ticket-alt"></i>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-md-6">
                            <label for="inputZip" class="form-label">GCash Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="gnum" maxlength="11" pattern="09\d{9}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="inputZip" class="form-label">Reference Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="text" class="form-control" name="grefnum" maxlength="8" pattern="\d{8}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="cashin" class="btn btn-danger"><i class="fas fa-money-bill"></i> Cash
                                In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    </body>

</html>
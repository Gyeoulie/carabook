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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Cash Out</title>
</head>
<style>

</style>

<body>
    <?php
    //CHECKS IF CASHOUT BTN IS BEING PASSED (OR IF CASHOUT BTN IS PRESSED)
    if (isset($_POST['cashout'])) {

        //SETS VARIABLES FOR CASHOUT AND ALSO FETCHED INPUT FROM THE FORM
        $USERID = $_SESSION['userID'];
        $COAMOUNT = $_POST['coamount'];
        $GNUM = $_POST['gnum'];
        $TRANSTYPE = 'CASH OUT';
        $PROCFEE = 0;


        //COMUTATION/FORMULA FOR THE AMOUNT OF PROCESSING FEE
        /*USED MODULO TO GET THE REMAINDER. IF THERE IS REMAINDER IT ALREADY MEANS THERE IS ALREADY ATLEAST 20 PROCCESSING FEE.
    MODUL0 IS THEN MINUS TO THE TOTAL CASHOUT TO CHECK IF ITS DIVISIBLE BY 1000 (EVERY 1000 = +20 PROFEE) THE RESULT IS THEN
    MULTIPLIED TO 20 TO GET PRO FEE.
    IF the MODULO = 0, THEN AUTOMATICALLY THERE IS +20 PRO FEE
    */
        $MODULO = $COAMOUNT % 1000;
        if ($MODULO > 0) {
            $WHOLE = $COAMOUNT - $MODULO;
            if ($WHOLE > 0) {
                $THOUSANDS = $WHOLE / 1000;
                $PROCFEE = 20 + ($THOUSANDS * 20);
            } else {
                $PROCFEE = 20;
            }
        } else if ($MODULO == 0) {
            $THOUSANDS = $COAMOUNT / 1000;
            $PROCFEE = $THOUSANDS * 20;
        }

        //CREATE A VARIABLE WITH 'PENDING'
        //SQL STATEMENT TO SELECT TRANSACTION ID OF THE USER. THIS IS USED TO CHECK IF THERE IS ALREADY A PENDING TRANSACTION OF THE USER(VALIDATION)
        $COSTATUS = 'PENDING';
        $COTYPE = 'CASH OUT';
        $sql = "SELECT trans_id FROM transaction_tbl WHERE tuser_id = ? AND ttrans_type = ? AND tstatus = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $USERID, $COTYPE, $COSTATUS);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        //CHECKS IF THERE IS NO RESULT ELSE PRINTS AN ERROR
        if (mysqli_num_rows($result) == 0) {

            //SQL STATEMENT TO SELECT/FETCH USER'S CURRENT BALANCE
            $sql = "SELECT ubalance FROM user_tbl WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $USERID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                $USER_INFO = mysqli_fetch_assoc($result);
                //SETS THE VALUE FOR CURRENT BALANCE AND TOTAL OF CASHOUT + PROCESSING FEE
                $CURBALANCE = $USER_INFO['ubalance'];
                $CURCASHOUT = $COAMOUNT + $PROCFEE;
            }

            //CONDITION TO CHECK IF THE BALANCE IS GREATER THAN MONEY BEING CASHED OUT. IF LESS THAN, PROCEED TO VALIDATION (PRINTS ERROR)
            if ($CURBALANCE >= $CURCASHOUT) {
                //SQL STATEMENT TO ADD CASH OUT REQUEST TO TRANSASTION_TBL (WILL BE A PENDING TRANSACTION UNTIL ADMIN APPROVED)
                $sql = "INSERT INTO transaction_tbl (tuser_id, ttrans_type, tamount, toutfee, tmobile_no) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "isiis", $USERID, $TRANSTYPE, $COAMOUNT, $PROCFEE, $GNUM);
                if (mysqli_stmt_execute($stmt)) {
                    //REDIRECT TO HOMEPAGE
                    echo "<script>
                  function alertAndRedirect() {
                 alert('Cash Out Successful, Wait for verification');
                 window.location.href = 'homepage';
                  }
                  alertAndRedirect();
               </script>";
                } else {
                    echo "<script>alert('ERROR!');</script>";
                }
            } else {
                echo "<script>alert('Not enough funds!');</script>";
            }
        } else {
            echo "<script>alert('You already have a pending Cashout transaction!');</script>";
        }
    }

    ?>
    <?php include 'header.php'; ?>
    <style>
        .cashout {
            margin-top: 150px;
            width: 50%;
            margin-left: 30%;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: #999;
        }
    </style>

    <!-- CASHOUT FORM/ CONTAINER -->
    <div class="container">
        <div class="cashout">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-money-bill"></i> Cash Out
                </div>
                <div class="card-body">
                    <form class="row g-3" id="form1" action="" method="post">
                        <div class="col-md-12">
                            <label for="inputZip" class="form-label">Amount</label>
                            <input type="number" class="form-control" name="coamount" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label for="inputZip" class="form-label">GCash Number</label>
                            <div class="position-relative">

                                <input type="tel" class="form-control" name="gnum" maxlength="11" pattern="09\d{9}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="cashout" class="btn btn-danger">Cash Out</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>

</html>
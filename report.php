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
    <!-- Add these lines within the <head> tag -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <title>Daily Reports</title>
</head>
<style>
    h1 {
        text-align: center;
    }
</style>

<body>
    <?php include 'header.php';?>

    <?php

//SETS VARIABLE FOR REPORTS
$totalPFEE = 0;
$totalCFEE = 0;
$currentDate = date('Y-m-d');
$TOTAL_CASHIN = 0;
$TOTAL_CASHOUT = 0;
$TOTAL_BALANCE = 0;
$TSTATUS = 'APPROVED';

//SQL STATEMENT TO GET TOTAL CASH IN WITHIN THE LAST 24 HOURS
$sql = 'SELECT SUM(t.tamount) AS "total_cashin"
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND DATE(t.ttimedate) = ? AND t.ttrans_type = ?';

$CASHIN = 'CASH IN';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $currentDate, $CASHIN);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($TOTAL_CASH_IN = mysqli_fetch_assoc($result)) {
        $TOTAL_CASHIN = $TOTAL_CASH_IN['total_cashin'];
    }
}
//SQL STATEMENT TO GET TOTAL CASH OUT WITHIN THE LAST 24 HOURS
$sql = 'SELECT SUM(t.tamount) AS "total_cashout"
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND DATE(t.ttimedate) = ? AND t.ttrans_type = ?';

$CASHOUT = 'CASH OUT';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $currentDate, $CASHOUT);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($TOTAL_CASH_IN = mysqli_fetch_assoc($result)) {

        $TOTAL_CASHOUT = $TOTAL_CASH_IN['total_cashout'];
    }
}

//SQL STATEMENT TO GET TOTAL BALANCE TRANSACTION WITHIN THE LAST 24 HOURS
$sql = 'SELECT SUM(ubalance) AS total_balance
FROM user_tbl
WHERE user_id IN (SELECT DISTINCT tuser_id FROM transaction_tbl WHERE DATE(ttimedate) = ? AND tstatus = ?)
';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $currentDate, $TSTATUS);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($TOTAL_CASH_IN = mysqli_fetch_assoc($result)) {
        $TOTAL_BALANCE = $TOTAL_CASH_IN['total_balance'];
    }
}

?>
    <br><br>
    <div class="container">
        <div class=" row">
        <!-- Daily Reports -->
        <div class="col-md-6" style="background-color: white; border-radius:5%; box-shadow: 5px 5px 5px;">
            <div class="container reports" id="reports">
                <br>
                <h1>Daily Report</h1>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Pro Fee</th>
                                <th scope="col">Con Fee</th>
                                <th scope="col">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//SQL STATEMENT TO FETCH TRANSACTIONS FROM THE LAST 24 HOURS
$sql = 'SELECT u.user_id, u.ufname, u.umname, u.ulname, u.ubalance, u.uidimg, t.trans_id, t.ttrans_type, t.tamount, t.toutfee, t.tinfee
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND DATE(t.ttimedate) = ?';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $TSTATUS, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($ROW_INFO = mysqli_fetch_assoc($result)) {

        //ADDS THE TOTAL OUT AND IN FEE
        $totalPFEE += $ROW_INFO['toutfee'];
        $totalCFEE += $ROW_INFO['tinfee'];
        ?>
                                    <tr>
                                        <th scope="row"><?=$ROW_INFO['trans_id']?></th>
                                        <td><?=$ROW_INFO['ufname'] . " " . $ROW_INFO['umname'] . " " . $ROW_INFO['ulname']?></td>
                                        <td><?=$ROW_INFO['ttrans_type']?></td>
                                        <td><?=$ROW_INFO['tamount']?></td>
                                        <td><?=$ROW_INFO['toutfee']?></td>
                                        <td><?=$ROW_INFO['tinfee']?></td>
                                        <td><?=$ROW_INFO['ubalance']?></td>
                                    </tr>
                            <?php
}
}
?>

                        </tbody>
                    </table>
                </div>
                <div>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#reportModal">Total Fee</button>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">Total Fee Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Total Cash In: <?php if (empty($TOTAL_CASHIN)) {
    echo 0;
} else {
    echo $TOTAL_CASHIN;
}?></h3>
                                <h3>Total Cash Out: <?php if (empty($TOTAL_CASHOUT)) {
    echo 0;
} else {
    echo $TOTAL_CASHOUT;
}?></h3>
                                <h3>Total Balance: <?php if (empty($TOTAL_BALANCE)) {
    echo 0;
} else {
    echo $TOTAL_BALANCE;
}?></h3>
                            </div>
                            <div class="col-md-6">
                                <h3>Generated Income:</h3>
                                <ol type="A">
                                    <li>Conversion Fee: <?=$totalCFEE?></li>
                                    <li style="color:red;">Processing Fee: <?=$totalPFEE?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Cash In -->
        <div class="col-md-6">
            <div class="container rcashins" id="rcashins" style="background-color: white; border-radius:5%; padding:5%; box-shadow: 5px 5px 5px; margin-left:5%">
                <h1>Cash In Transactions</h1>
                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Con Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//SQL STATEMENT TO ALL CASH IN TRANSACTIONS
$sql = 'SELECT u.ufname, u.umname, u.ulname, t.trans_id, t.ttrans_type, t.tamount, t.tinfee
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND t.ttrans_type = ? AND DATE(t.ttimedate) = ?';
$CASHIN = 'CASH IN';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $CASHIN, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($CASHIN_REPORT = mysqli_fetch_assoc($result)) {
        ?>
                                    <tr>
                                        <th scope="row"><?=$CASHIN_REPORT['trans_id']?></th>
                                        <td><?=$CASHIN_REPORT['ufname'] . " " . $CASHIN_REPORT['umname'] . " " . $CASHIN_REPORT['ulname']?></td>
                                        <td><?=$CASHIN_REPORT['tamount']?></td>
                                        <td><?=$CASHIN_REPORT['tinfee']?></td>
                                    </tr>
                            <?php
}
}
//SQL STATEMENT TO GET THE TOTAL OF CASH IN AND IN FEE
$sql = 'SELECT SUM(tamount) AS "rtamount", SUM(tinfee) AS "rtinfee"  FROM transaction_tbl WHERE tstatus = ? AND ttrans_type = ? AND DATE(ttimedate) = ?';
$CASHIN = 'CASH IN';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $CASHIN, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    $CASHIN_TOTALREPORT = mysqli_fetch_assoc($result);
}
?>
                            <!-- TABLE ROW TO DISPLAY TOTAL -->
                            <tr style="background-color: transparent; border: none;">
                                <td></td>
                                <td style="text-align: right;">TOTAL</td>
                                <td><?=$CASHIN_TOTALREPORT['rtamount']?></td>
                                <td><?=$CASHIN_TOTALREPORT['rtinfee']?></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cash Out -->
        <div class="col-md-6">
            <div class="container rcashouts" id="rcashouts" style="background-color: white; border-radius:5%; padding:5%; margin-top: 10%; box-shadow: 5px 5px 5px;">
                <h1>Cash Out Transactions</h1>
                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Pro Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//SQL STATEMENT TO ALL CASH IN TRANSACTIONS
$sql = 'SELECT u.ufname, u.umname, u.ulname, t.trans_id, t.ttrans_type, t.tamount, t.toutfee
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND t.ttrans_type = ? AND DATE(t.ttimedate) = ?';
$CASHOUT = 'CASH OUT';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $CASHOUT, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($CASHOUT_REPORT = mysqli_fetch_assoc($result)) {
        ?>
                                    <tr>
                                        <th scope="row"><?=$CASHOUT_REPORT['trans_id']?></th>
                                        <td><?=$CASHOUT_REPORT['ufname'] . " " . $CASHOUT_REPORT['umname'] . " " . $CASHOUT_REPORT['ulname']?></td>
                                        <td><?=$CASHOUT_REPORT['tamount']?></td>
                                        <td><?=$CASHOUT_REPORT['toutfee']?></td>
                                    </tr>
                            <?php
}
}
//SQL STATEMENT TO GET THE TOTAL OF CASH OUT AND OUT FEE
$sql = 'SELECT SUM(tamount) AS "rtamount", SUM(toutfee) AS "routfee"  FROM transaction_tbl WHERE tstatus = ? AND ttrans_type = ? AND DATE(ttimedate) = ?';
$CASHOUT = 'CASH OUT';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $TSTATUS, $CASHOUT, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    $CASHOUT_TOTALREPORT = mysqli_fetch_assoc($result);
}
?>
                            <!-- TABLE ROW TO DISPLAY TOTAL -->
                            <tr style="background-color: transparent; border: none;">
                                <td></td>
                                <td style="text-align: right;">TOTAL</td>
                                <td><?=$CASHOUT_TOTALREPORT['rtamount']?></td>
                                <td><?=$CASHOUT_TOTALREPORT['routfee']?></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Balances -->
        <div class="col-md-6">
            <div class="container rbalance" id="rbalance" style="background-color: white; border-radius:5%; padding:5%; box-shadow: 5px 5px 5px; margin-left:5%; margin-top:10%">
                <h1>Ticket Balances</h1>
                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//SQL STATEMENT TO ALL CASH IN TRANSACTIONS
$sql = 'SELECT ufname, umname, ulname, ubalance, user_id FROM user_tbl';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($BALANCE_INFO = mysqli_fetch_assoc($result)) {
        ?>
                                    <tr>
                                        <th scope="row"><?=$BALANCE_INFO['user_id']?></th>
                                        <td><?=$BALANCE_INFO['ufname'] . " " . $BALANCE_INFO['umname'] . " " . $BALANCE_INFO['ulname']?></td>
                                        <td><?=$BALANCE_INFO['ubalance']?></td>
                                    </tr>
                            <?php
}
}
//SQL STATEMENT TO GET THE TOTAL OF BALANCE
$sql = 'SELECT SUM(ubalance) AS "rbalance" FROM user_tbl';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    $BALANCE_TOTALREPORT = mysqli_fetch_assoc($result);
}
?>
                            <!-- TABLE ROW TO DISPLAY TOTAL -->
                            <tr style="background-color: transparent; border: none;">
                                <td></td>
                                <td style="text-align: right;">TOTAL</td>
                                <td><?=$BALANCE_TOTALREPORT['rbalance']?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Earnings -->
        <div class="col-md-6">
            <div class="container rearning" id="rearning" style="background-color: white; border-radius:5%; padding:5%; margin-top: 10%; box-shadow: 5px 5px 5px;">
                <h1>Booking Earnings</h1>
                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$sql = 'SELECT u.ufname, u.umname, u.ulname, p.pamount, p.payment_id, p.ptimestamp FROM user_tbl u
INNER JOIN booking_tbl b ON u.user_id = b.buser_id
INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
WHERE pstatus = ? AND DATE(p.ptimestamp) = ?';
$CANCEL2 = "CANCELLED2";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $CANCEL2, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($PAYMENT_INFO = mysqli_fetch_assoc($result)) {
        ?>
                            <tr>
                                <th><?=$PAYMENT_INFO['payment_id']?></th>
                                <td><?=$PAYMENT_INFO['pamouufnamet'] . " " . $PAYMENT_INFO['umname'] . " " . $PAYMENT_INFO['ulname']?></td>
                                <td><?=$PAYMENT_INFO['pamount']?></td>
                                <td><?=$PAYMENT_INFO['ptimestamp']?></td>
                            </tr>
                            <?php
}
}
$sql = 'SELECT SUM(p.pamount) AS "total_cancel" FROM user_tbl u
INNER JOIN booking_tbl b ON u.user_id = b.buser_id
INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
WHERE pstatus = ? AND DATE(p.ptimestamp) = ?';
$CANCEL2 = "CANCELLED2";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $CANCEL2, $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$PAYMENT_INFOTOTAL = mysqli_fetch_assoc($result)

?>
 <tr style="background-color: transparent; border: none;">
                                <td></td>
                                <td style="text-align: right;">TOTAL</td>
                                <td><?=$PAYMENT_INFOTOTAL['total_cancel']?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Balances -->
        <div class="col-md-6">
            <div class="container rbalance" id="rbalance" style="background-color: white; border-radius:5%; padding:5%; box-shadow: 5px 5px 5px; margin-left:5%; margin-top:10%">
                <h1>Route Earnings</h1>
                <div style="max-height: 400px; overflow:auto;">
                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$sql = 'SELECT * FROM routetrans_tbl WHERE DATE(rttimestamp) = ?';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($TAX_INFO = mysqli_fetch_assoc($result)) {
        ?>
                            <tr>
                                <th><?=$TAX_INFO['rtroute_id']?></th>
                                <td><?=$TAX_INFO['rttax']?></td>
                                <td><?=$TAX_INFO['rttimestamp']?></td>
                            </tr>
                            <?php
}
}
$sql = 'SELECT SUM(rttax) AS "TOTALTAX" FROM routetrans_tbl WHERE DATE(rttimestamp) = ?';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$TAX_TOTAL= mysqli_fetch_assoc($result);
        ?>
        <tr style="background-color: transparent; border: none;">
                                <td></td>
                                <td style="text-align: right;">TOTAL</td>
                                <td><?=$TAX_TOTAL['TOTALTAX']?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

</body>

</html>
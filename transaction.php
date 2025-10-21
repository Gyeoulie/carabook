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
    <title>Transactions</title>
</head>

<?php

//CHECKS IF THERE IS AN ACTION PASSED
if (isset($_GET['paction'])) {
    //GETS THE TRANSACTION ID PASSED
    $TRANSID = $_GET['tid'];
    //CHECKS IF THE ACTION PASSED IS EQUALS TO APPROVE
    if ($_GET['paction'] == 'approve') {

        //SELECT STATEMENT TO GET TRANSACTION INFORMATION
        $sql = 'SELECT u.user_id, u.ubalance, t.*, t.tamount - t.tinfee as "TICKETAMOUNT"
                FROM transaction_tbl t
                INNER JOIN user_tbl u
                ON t.tuser_id = u.user_id
                WHERE t.trans_id = ?';

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $TRANSID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)) {

            //SETS THE VARIABLE WITH VALUE FROM FETCHED DATA
            $USER_INFO = mysqli_fetch_assoc($result);
            $USERID = $USER_INFO['user_id'];
            $UBALANCE = $USER_INFO['ubalance'];
            $TOTALADD = $USER_INFO['TICKETAMOUNT'];
            $NEWBALANCE = $UBALANCE + $TOTALADD;
            $NEWSTATUS = 'APPROVED';
        }

        //SQL STATEMENT TO UPDATE CURRENT USER BALANCE
        $sql = "UPDATE transaction_tbl SET tstatus = ? WHERE trans_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $NEWSTATUS, $TRANSID);
        if (mysqli_stmt_execute($stmt)) {
            $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $NEWBALANCE, $USERID);

            if (mysqli_stmt_execute($stmt)) {
                //REDIRECT IF SUCCESFULL
                echo '<script>
        function alertAndRedirect() {
          alert("Cash In Accepted!");
          window.location.href = "transaction.php";
        }
        alertAndRedirect();
        </script>';
            } else {
                //ERROR IF UNSUCCESSFULL
                echo "<script>alert('ERROR2!');</script>";
            }
        } else {
            //ERROR IF UNSUCESSFULL
            echo "<script>alert('ERROR1!');</script>";
        }

        //CHECKS IF THE BUTTON PRESSED IS REJECT
    } else if ($_GET['paction'] == 'reject') {
        //DELETE THE TRANSACTION FROM THE TABLE
        $sql = "DELETE FROM transaction_tbl WHERE trans_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $TRANSID);
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
            function alertAndRedirect() {
              alert("Cash In Rejected!");
              window.location.href = "transaction.php";
            }
            alertAndRedirect();
            </script>';
        } else {
            echo "<script>alert('ERROR3!');</script>";
        }
    }

    //CHECKS IF THE ACTION IS FROM CASHOUT TABLE
} else if (isset($_GET['caaction'])) {
    //GET THE CURRENT TRANSACTION ID
    $TRANSID = $_GET['tid'];

    //CHECKS IF THE REJECT BUTTON IS PRESSED
    if ($_GET['caaction'] == 'reject') {

        //DELETE FROM REQUEST FROM TRANSACTION_TBL
        $sql = "DELETE FROM transaction_tbl WHERE trans_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $TRANSID);
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
            function alertAndRedirect() {
              alert("Cash Out Rejected!");
              window.location.href = "transaction.php";
            }
            alertAndRedirect();
            </script>';
        } else {
            echo "<script>alert('ERROR3!');</script>";
        }
    }

    //CHECKS IF THERE IS A POST APPROVE
} else if (isset($_POST['coapprove'])) {

    //SETS THE VARIABLE FROM FORM/MODAL
    $REFNUM = $_POST['grefnum'];
    $TRANSID = $_POST['transid'];

    //SQL STATEMENT FOR SELECTING AND FETCHING TRANSACTION INFORMATION
    $sql = 'SELECT u.user_id, u.ubalance, t.*,  t.tamount + t.toutfee as "CTICKETAMOUNT"
    FROM transaction_tbl t
    INNER JOIN user_tbl u
    ON t.tuser_id = u.user_id
    WHERE t.trans_id = ?';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $TRANSID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {

        //SETS THE VALUE FETCHED
        $USER_INFO = mysqli_fetch_assoc($result);
        $USERID = $USER_INFO['user_id'];
        $UBALANCE = $USER_INFO['ubalance'];
        $ADDBALANCE = $USER_INFO['CTICKETAMOUNT'];
        $NEWBALANCE = $UBALANCE - $ADDBALANCE;
        $NEWSTATUS = 'APPROVED';
    }

    //SQL STATEMENT TO UPDATE USER'S BALANCE
    $sql = "UPDATE transaction_tbl SET tstatus = ?, tref_no = ? WHERE trans_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $NEWSTATUS, $REFNUM, $TRANSID);
    if (mysqli_stmt_execute($stmt)) {
        $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $NEWBALANCE, $USERID);

        if (mysqli_stmt_execute($stmt)) {
            //REDIRECT IF SUCCESSFUL
            echo '<script>
function alertAndRedirect() {
alert("Cash Out Accepted!");
window.location.href = "transaction.php";
}
alertAndRedirect();
</script>';
        } else {
            echo "<script>alert('ERROR2!');</script>";
        }
    } else {
        echo "<script>alert('ERROR1!');</script>";
    }
}

?>


<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <!-- CASH IN TABLE/ CONTAINER -->
                <div class="container tcashin text-center" style="background-color: white; border-radius:5%; margin-top: 10%; padding-bottom:10px; box-shadow: 5px 5px 5px;">
                    <br>
                    <h1>Cash In</h1>

                    <table class="table table-light">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Cash Amount</th>
                                <th scope="col">Mobile Number</th>
                                <th scope="col">Reference Number</th>
                                <th scope="col">Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php
                                //SQL STATEMENT TO DISPLAY PENDING CASH IN TRANSACTIONS
                                $sql = 'SELECT u.user_id, u.ufname, u.umname, u.ulname, u.uidimg, t.*
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND t.ttrans_type = ?';

                                $TSTATUS = 'PENDING';
                                $CITTYPE = 'CASH IN';
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "ss", $TSTATUS, $CITTYPE);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (mysqli_num_rows($result)) {
                                    while ($ROW_INFO = mysqli_fetch_assoc($result)) {
                                ?>
                                        <td><?= $ROW_INFO['ufname'] . " " . $ROW_INFO['umname'] . " " . $ROW_INFO['ulname'] ?></td>
                                        <td><?= $ROW_INFO['tamount'] ?></td>
                                        <td><?= $ROW_INFO['tmobile_no'] ?></td>
                                        <td><?= $ROW_INFO['tref_no'] ?></td>
                                        <td><?= $ROW_INFO['ttimedate'] ?></td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; justify-content: center; align-items: center;">
                                                <button class="btn btn-danger" onclick="if (confirm('Are you sure you want to accept?')) { location.href='transaction.php?paction=approve&tid=<?= $ROW_INFO['trans_id'] ?>'; }">Accept</button>
                                                <button class="btn btn-danger" onclick="if (confirm('Are you sure you want to decline?')) { location.href='transaction.php?paction=reject&tid=<?= $ROW_INFO['trans_id'] ?>'; }">Decline</button>
                                            </div>
                                        </td>

                            </tr>
                    <?php
                                    }
                                }
                    ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="container tcashout" style="background-color: white; border-radius:5%; padding:5%; box-shadow: 5px 5px 5px; margin-left:5%; margin-top:10%">
                    <h1>Cash Out</h1>

                    <table class="table bg-light">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Cash Amount</th>
                                <th scope="col">Mobile Number</th>
                                <th scope="col">Date</th>
                                <th scope="col">Action</th>
                        </thead>
                        <tbody>
                            <?php

                            //SQL STATEMENT TO FETCH CASHOUT PENDING CASHOUT INFORMATION
                            $sql = 'SELECT u.user_id, u.ufname, u.umname, u.ulname, u.uidimg, u.ubalance, t.*
                FROM user_tbl u
                INNER JOIN transaction_tbl t
                ON u.user_id = t.tuser_id
                WHERE t.tstatus = ? AND t.ttrans_type = ?';

                            $TSTATUS = 'PENDING';
                            $CATTYPE = 'CASH OUT';
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "ss", $TSTATUS, $CATTYPE);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($result)) {
                                while ($ROW_INFO = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td><?= $ROW_INFO['ufname'] . " " . $ROW_INFO['umname'] . " " . $ROW_INFO['ulname'] ?></td>
                                        <td><?= $ROW_INFO['tamount'] ?></td>
                                        <td><?= $ROW_INFO['tmobile_no'] ?></td>
                                        <td><?= $ROW_INFO['ttimedate'] ?></td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; justify-content: center; align-items: center;">
                                                <?php
                                                // VALIDATION TO CHECK IF USER'S MONEY IS ENOUGH TO HAVE A CASHOUT TRANSACTION
                                                if ($ROW_INFO['ubalance'] >= $ROW_INFO['tamount'] + $ROW_INFO['toutfee']) {
                                                ?>
                                                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#referencemodal">
                                                        Accept
                                                    </button>
                                                <?php
                                                } else {
                                                ?>
                                                    <p style="font-weight: bold;">NOT ENOUGH FUNDS</p>
                                                <?php
                                                }
                                                ?>
                                                <button class="btn btn-dark" onclick="if (confirm('Are you sure you want to decline?')) { location.href='transaction.php?caaction=reject&tid=<?= $ROW_INFO['trans_id'] ?>'; }">Decline</button>
                                            </div>
                                        </td>
                                    </tr>

                                    </tr>

                                    <!-- REFERENCE NUMBER MODAL -->
                                    <div class="modal fade referencenum-modal" id="referencemodal" tabindex="-1" aria-labelledby="referencemodalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cash Out Confirmation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="" method="post">
                                                    <div class="modal-body">

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">Transaction ID</label>
                                                            <input type="num" class="form-control" name="transid" value="<?= $ROW_INFO['trans_id'] ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">GCash Reference Number</label>
                                                            <input type="num" class="form-control" name="grefnum" placeholder="########" maxlength="8" pattern="\d{8}" required>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="coapprove">Save changes</button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

</body>

</html>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
<?php
require 'ver.php';
if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location:/');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="x-icon" href="image/car.png" />
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Profile</title>
</head>
<style>
    @import url("https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap");

    body {
        background: #f9f9f9;
        font-family: "Roboto", sans-serif;
    }

    .shadow {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
    }

    .profile-tab-nav {
        min-width: 250px;
    }

    .tab-content {
        flex: 1;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .nav-pills a.nav-link {
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        border-radius: 0;
        color: #333;
    }

    .nav-pills a.nav-link i {
        width: 20px;
    }

    .img-circle img {
        height: 100px;
        width: 100px;
        border: 5px solid #fff;
        margin-left: 30%;
    }

    label {
        color: #333;
    }

    .mb-5 {
        color: whitesmoke;
    }

    @import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css);

    .rate {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .rating-wrap {
        max-width: 480px;
        margin: auto;
        padding: 15px;
        box-shadow: 0 0 3px 0 rgba(0, 0, 0, .2);
        text-align: center;
    }

    .center {
        width: 162px;
        margin: auto;
    }


    #rating-value {
        width: 220px;
        margin: 40px auto 0;
        padding: 10px 5px;
        text-align: center;
        box-shadow: inset 0 0 2px 1px rgba(46, 204, 113, .2);
    }

    /*styling star rating*/
    .rating {
        border: none;
        float: left;
    }

    .rating>input {
        display: none;
    }

    .rating>label:before {
        content: '\f005';
        font-family: FontAwesome;
        margin: 5px;
        font-size: 1.5rem;
        display: inline-block;
        cursor: pointer;
    }

    .rating>.half:before {
        content: '\f089';
        position: absolute;
        cursor: pointer;
    }


    .rating>label {
        color: #ddd;
        float: right;
        cursor: pointer;
    }

    .rating>input:checked~label,
    .rating:not(:checked)>label:hover,
    .rating:not(:checked)>label:hover~label {
        color: #ffea00;
    }

    .rating>input:checked+label:hover,
    .rating>input:checked~label:hover,
    .rating>label:hover~input:checked~label,
    .rating>input:checked~label:hover~label {
        color: #ffea00;
    }
</style>

<body class="bg">
    <?php

//FETCH USERID FROM SESSION
$USERID = $_SESSION['userID'];

if (isset($_POST['change'])) {

    //SETS VARIABLE FROM FORM INPUTS
    $OPASS = $_POST['oldpwd'];
    $NPASS = $_POST['npwd'];
    $CPASS = $_POST['cpwd'];

    //SQL STATEMENT TO FETCH USER CURRENT PASSWORD
    $sql = "SELECT upassword FROM user_tbl WHERE user_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $USERID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {
        $USER_INFO = mysqli_fetch_assoc($result);
        $OPWD = $USER_INFO['upassword'];
    }

    //VALIDATION TO CHECK IF CURRENT PASSWORD IS INCORRECT
    if ($OPASS != $OPWD) {
        echo "<script>
        alert('Current Password Incorrect!');
        </script>";

        //VALIDATION TO CHECK IF TWO PASS MATCH
    } else if ($NPASS != $CPASS) {
        echo "<script>
        alert('Both password must match!');
        </script>";

        //VALIDATION TO CHECK IF NEW PASSWORD MATCH WITH THE OLD ONE
    } else if ($NPASS == $OPWD) {
        echo "<script>
        alert('New password must not match with the old one!');
        </script>";
    } else {
        //SQL STATEMENT TO UPDATE CURRENT PASSW0RD
        $sql = "UPDATE user_tbl SET upassword = ? WHERE user_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $NPASS, $USERID);

        if (mysqli_stmt_execute($stmt)) {

            //REDIRECT TO PROFILE PAGE
            echo "<script>
            function alertAndRedirect() {
           alert('Password Successfully Changed!');
           window.location.href = 'profile';
            }
            alertAndRedirect();
         </script>";
        }
    }
} else if (isset($_POST['updateid'])) {
    //SETS THE VARIABLE FROM THE FORM
    $selected_option = $_POST['identificationType'];

    //IMAGE
    $fileName = $_FILES['idImage']['name'];
    $fileTmpName = $_FILES['idImage']['tmp_name'];
    // Get the file extension
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
    // Set the destination folder
    $fileDestination = 'storimg/dimg/' . $fileNameNew;
    // Move the file to the destination
    move_uploaded_file($fileTmpName, $fileDestination);

    //CONDITION TO CHANGE LEVEL BASED FROM ID SELECTED
    if ($selected_option == 1) {
        $NEWLEVEL = 2;
    } else if ($selected_option) {
        $NEWLEVEL = 3;
    }

    //SQL STATEMENT FOR UPDATING USERLEVEL , IMAGE, AND IDTYPE
    $sql = "UPDATE user_tbl SET uuserlevel = ?, uidtype = ?, uidimg = ? WHERE user_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisi", $NEWLEVEL, $selected_option, $fileDestination, $USERID);
    if (mysqli_stmt_execute($stmt)) {

        //REDICTS TO PROFILE PAGE IF SUCCESS
        echo "<script>
        function alertAndRedirect() {
       alert('ID Successfully Changed!');
       window.location.href = 'profile';
        }
        alertAndRedirect();
     </script>";
    }
} else if (isset($_POST['updatepro'])) {
    //IMAGE
    $fileName = $_FILES['profileImage']['name'];
    $fileTmpName = $_FILES['profileImage']['tmp_name'];
    // Get the file extension
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
    // Set the destination folder
    $fileDestination = 'storimg/pimg/' . $fileNameNew;
    // Move the file to the destination
    move_uploaded_file($fileTmpName, $fileDestination);

    //SQL STATEMENT FOR UPDATING USERLEVEL , IMAGE, AND IDTYPE
    $sql = "UPDATE user_tbl SET upimg = ? WHERE user_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $fileDestination, $USERID);
    if (mysqli_stmt_execute($stmt)) {

        //REDICTS TO PROFILE PAGE IF SUCCESS
        echo "<script>
        function alertAndRedirect() {
       alert('Profile Successfully Changed!');
       window.location.href = 'profile';
        }
        alertAndRedirect();
     </script>";
    }
} else if (isset($_POST['rsub'])) {
    $STARRATE = $_POST['frating'];
    $COMMENTS = $_POST['fcooment'];
    $BID = $_POST['BID'];

    $sql = "INSERT INTO feedback_tbl (fbooking_id, frating, fcomment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $BID, $STARRATE, $COMMENTS);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
        function alertAndRedirect() {
       alert('Feedback Complate !');
       window.location.href = 'profile';
        }
        alertAndRedirect();
     </script>";
    }
}

//SQL STATEMENT TO FETCH CURRENT USER INFO
$sql = "SELECT * FROM user_tbl WHERE user_ID = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $USERID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ACC_INFO = mysqli_fetch_assoc($result);

?>
    <?php include 'header.php';?>
    <!-- PROFILE TAB FORM/ CONTAINER -->
    <section class="py-5 my-5">
        <div class="container">
            <h1 class="mb-5">Account Settings</h1>
            <div class="bg-white shadow rounded-lg d-block d-sm-flex">
                <div class="profile-tab-nav border-right">
                    <div class="p-4">
                        <div class="img-circle text-center mb-3">
                            <img src="<?=$ACC_INFO['upimg']?>" alt="Image" class="shadow">
                        </div>
                        <h4 class="text-center"><?=$ACC_INFO['ufname'] . " " . $ACC_INFO['umname'] . " " . $ACC_INFO['ulname']?></h4>
                    </div>
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="account-tab" data-toggle="pill" href="#account" role="tab" aria-controls="account" aria-selected="true">
                            <i class="fa fa-home text-center mr-1"></i>
                            Account
                        </a>
                        <a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
                            <i class="fa fa-key text-center mr-1"></i>
                            Password
                        </a>
                        <a class="nav-link" id="security-tab" data-toggle="pill" href="#security" role="tab" aria-controls="security" aria-selected="false">
                            <i class="fa fa-history text-center mr-1"></i>
                            Transaction History
                        </a>
                        <a class="nav-link" id="application-tab" data-toggle="pill" href="#application" role="tab" aria-controls="application" aria-selected="false">
                            <i class="fa fa-history text-center mr-1"></i>
                            Booking History
                        </a>
                        <?php if ($_SESSION['ulvl'] == 4) {?>
                            <a class="nav-link" id="notification-tab" data-toggle="pill" href="#notification" role="tab" aria-controls="notification" aria-selected="false">
                                <i class="fa fa-history text-center mr-1"></i>
                                Route History
                            </a>
                        <?php
}
?>
                    </div>
                </div>
                <div class="tab-content p-4 p-md-5" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                        <h3 class="mb-4">Account Settings</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['ufname']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['umname']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['ulname']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['uemail']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Birthdate</label>
                                    <input type="text" class="form-control" value="<?=date('F j, Y', strtotime($ACC_INFO['ubirthdate']))?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone number</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['ucnumber']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inputZip" class="form-label">ID Image</label>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-danger " onclick="displayImage('<?php echo $ACC_INFO['uidimg']; ?>')">Image</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['uaddress']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['ucity']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Province</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['uprovince']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Zip</label>
                                    <input type="text" class="form-control" value="<?=$ACC_INFO['uzip']?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inputZip" class="form-label">Profile Image</label>
                                    <div class="row">
                                        <div class="col">
                                            <?php if ($_SESSION['ulvl'] == 2) {?>
                                                <button class="btn btn-danger  w-50" data-bs-toggle="modal" data-bs-target="#updateIDModal">Update ID</button>
                                            <?php
}
?>
                                            <button class="btn btn-danger w-45" data-bs-toggle="modal" data-bs-target="#updateProfileModal" style="width:48%">Update Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL ID UPDATE -->
                    <div class="modal fade" id="updateIDModal" tabindex="-1" aria-labelledby="updateIDModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateIDModalLabel">Update ID</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form1" action="" method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="idImage" class="form-label">New ID</label>
                                            <input type="file" class="form-control" name="idImage" id="idImage" accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Identification Type</label>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="identificationType" id="driversLicense" value="2" required>
                                                    <label class="form-check-label" for="driversLicense">Driver's License</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="identificationType" id="other" value="1" required>
                                                    <label class="form-check-label" for="other">Other</label>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="updateid" class="btn btn-primary">Save Changes</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL IMAGE UPDATE -->
                    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateProfileModalLabel">Update Profile Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="form2" action="" method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="profileImage" class="form-label">New Profile</label>
                                            <input type="file" class="form-control" name="profileImage" id="profileImage" accept="image/*">
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="updatepro" class="btn btn-primary">Save Changes</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Modal -->
                    <div id="imageModal" class="modal">
                        <span class="close">&times;</span>
                        <img class="modal-content" id="imageDisplay">
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
                    </script>

                    <script>
                        //TO DISPLAY THE MODAL (WHEN IMAGE BTN IS CLICKED)
                        function displayImage(imageData) {
                            var modal = document.getElementById("imageModal");
                            var image = document.getElementById("imageDisplay");
                            image.src = imageData;
                            modal.style.display = "block";

                            var span = document.getElementsByClassName("close")[0];
                            span.onclick = function() {
                                modal.style.display = "none";
                            }

                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }
                        }
                    </script>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h3 class="mb-4">Password Settings</h3>
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Old password</label>
                                        <input type="password" name="oldpwd" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>New password</label>
                                        <input type="password" name="npwd" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm new password</label>
                                        <input type="password" name="cpwd" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="change" class="btn btn-primary">Change</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h3 class="mb-4">Transaction History</h3>
                        <div class="table-responsive" style="overflow: auto; max-height:500px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
$sql = "SELECT ta.ttimedate AS 'DATE', ta.ttrans_type AS 'Type', ta.tamount AS 'Amount'
FROM user_tbl ua
LEFT JOIN transaction_tbl ta ON ua.user_id = ta.tuser_id
WHERE ua.user_id = ? AND ta.ttimedate IS NOT NULL

UNION ALL

SELECT pb.ptimestamp AS 'DATE',
CASE pb.pstatus
    WHEN 'PENDING' THEN 'PAYMENT'
    WHEN 'PAID' THEN 'PAYMENT'
    WHEN 'CANCELLED1' THEN 'REFUND'
    WHEN 'CANCELLED2' THEN 'DEDUCTION'
    WHEN 'RETURNED' THEN 'RETURNED'
END AS 'Type',
pb.pamount AS 'Amount'
FROM payment_tbl pb
LEFT JOIN booking_tbl bb ON pb.pbooking_id = bb.booking_id
LEFT JOIN user_tbl ub ON bb.buser_id = ub.user_id
WHERE ub.user_id = ? AND pb.ptimestamp IS NOT NULL

UNION ALL

SELECT rtc.rttimestamp AS 'DATE',
CASE
    WHEN rtc.rttype = 'EARNINGS' THEN 'EARNINGS'
    WHEN rtc.rttype = 'CANCEL' THEN 'DEDUCTION'
    WHEN rtc.rttype = 'ROUTE FEE' THEN 'ROUTE FEE'
END AS 'Type',
CASE
    WHEN rtc.rttype = 'EARNINGS' THEN rtc.rtamount - rtc.rttax
    WHEN rtc.rttype = 'CANCEL' THEN rtc.rttax
    WHEN rtc.rttype = 'ROUTE FEE' THEN rtc.rttax
END AS 'Amount'
FROM routetrans_tbl rtc
LEFT JOIN route_tbl rc ON rtc.rtroute_id = rc.route_id
LEFT JOIN vehicle_tbl vc ON rc.rvehicle_id = vc.vehicle_id
LEFT JOIN user_tbl uc ON vc.vuser_id = uc.user_id
WHERE uc.user_id = ? AND rtc.rttimestamp IS NOT NULL

ORDER BY 'DATE'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $_SESSION['userID'], $_SESSION['userID'], $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result)) {
    while ($TRANSACTION_HISTORY = mysqli_fetch_assoc($result)) {

        ?>
                                            <tr>
                                                <td><?=$TRANSACTION_HISTORY['DATE'] ? date("F j, Y H:i", strtotime($TRANSACTION_HISTORY['DATE'])) : 'N/A'?></td>
                                                <td><?=$TRANSACTION_HISTORY['Type']?></td>
                                                <td><?=$TRANSACTION_HISTORY['Amount']?></td>
                                        <?php
}
}
?>
                                            </tr>
                                            <!-- Add more rows for other transactions -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="application" role="tabpanel" aria-labelledby="application-tab">
                        <h3 class="mb-4">Booking History</h3>
                        <div class="table-responsive" style="overflow: auto; max-height:500px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Seat</th>
                                        <th>Status</th>
                                        <th>Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

$sql = "SELECT r.*, s.sseat_type_id, b.bbooking_status, b.booking_id  FROM route_tbl r
INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
INNER JOIN user_tbl u ON b.buser_id = u.user_id
WHERE u.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result)) {
    while ($BOOKING_HISTORY = mysqli_fetch_assoc($result)) {
        $FEEDBACKSTATUS = false;

        $sql = "SELECT feedback_id FROM feedback_tbl WHERE fbooking_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $BOOKING_HISTORY['booking_id']);
        mysqli_stmt_execute($stmt);
        $rs = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($rs) > 0) {
            $FEEDBACKSTATUS = true;
        }

        ?>
                                            <tr>
                                                <td><?=date("F j, Y H:i", strtotime($BOOKING_HISTORY['rdate_time']))?></td>
                                                <td><?=$BOOKING_HISTORY['rstart_point']?></td>
                                                <td><?=$BOOKING_HISTORY['rend_point']?></td>
                                                <td><?php
if ($BOOKING_HISTORY['sseat_type_id'] == 1) {
            echo 'Front Seat';
        } else if ($BOOKING_HISTORY['sseat_type_id'] == 2) {
            echo 'Left Seat';
        } else if ($BOOKING_HISTORY['sseat_type_id'] == 3) {
            echo 'Middle Seat';
        } else if ($BOOKING_HISTORY['sseat_type_id'] == 4) {
            echo 'Right Seat';
        }
        ?></td>
                                                <td><?=$BOOKING_HISTORY['bbooking_status']?></td>
                                                <td><button type="button" class="btn btn-primary" onclick="openModal('feedback_<?=$BOOKING_HISTORY['booking_id']?>')" <?php
if ($BOOKING_HISTORY['bbooking_status'] != 'COMPLETED' || $FEEDBACKSTATUS == true) {
            echo 'disabled';
        }
        ?>>Rate Driver</button></td>
                                            </tr>
                                             <!-- MODAL FEEDBACK -->
                    <div id="feedback_<?=$BOOKING_HISTORY['booking_id']?>" class="modal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ratingModalLabel">Rate Driver</h5>
                                    <button type="button" onclick="closeModal('feedback_<?=$BOOKING_HISTORY['booking_id']?>')" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Please rate the driver's service:</p>
                                    <form action = "" method="POST">
                                    <div class="container rate">
                                        <div class="rating-wrap">
                                            <div class="center">
                                                <fieldset class="rating">
                                                    <input type="radio" id="star5" name="frating" value="5" /><label for="star5" class="full" title="Awesome"></label>
                                                    <input type="radio" id="star4" name="frating" value="4" /><label for="star4" class="full"></label>
                                                    <input type="radio" id="star3" name="frating" value="3" /><label for="star3" class="full"></label>
                                                    <input type="radio" id="star2" name="frating" value="2" /><label for="star2" class="full"></label>
                                                    <input type="radio" id="star1" name="frating" value="1" /><label for="star1" class="full"></label>
                                                </fieldset>
                                            </div>
                                            <h4 id="rating-value"></h4>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <label for="comments">Comments:</label>
                                        <textarea class="form-control" id="comments" name="fcooment" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="BID" value="<?=$BOOKING_HISTORY['booking_id']?>">
                                    <button type="button" class="btn btn-secondary" onclick="closeModal('feedback_<?=$BOOKING_HISTORY['booking_id']?>')">Close</button>
                                    <button type="submit" class="btn btn-primary" name="rsub">Submit</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        let star = document.querySelectorAll('input');
                        let showValue = document.querySelector('#rating-value');

                        for (let i = 0; i < star.length; i++) {
                            star[i].addEventListener('click', function() {
                                i = this.value;

                                showValue.innerHTML = i + "out of 5";
                            });
                        }
                    </script>
                                    <?php
}
}
?>
                                    <!-- Add more rows for other carpool booking transactions -->
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class="tab-pane fade" id="notification" role="tabpanel" aria-labelledby="notification-tab">
                        <h3 class="mb-4">Route History</h3>
                        <div class="table-responsive" style="overflow: auto; max-height:500px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Start Point</th>
                                        <th>End Point</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
$sql = "SELECT r.* FROM route_tbl r
INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
INNER JOIN user_tbl u ON v.vuser_id = u.user_id
WHERE u.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result)) {
    while ($ROUTE_HISTORY = mysqli_fetch_assoc($result)) {

        ?>
                                            <tr>
                                                <td><?=date("F j, Y H:i", strtotime($ROUTE_HISTORY['rdate_time']))?></td>
                                                <td><?=$ROUTE_HISTORY['rstart_point']?></td>
                                                <td><?=$ROUTE_HISTORY['rend_point']?></td>
                                                <td><?=$ROUTE_HISTORY['rstatus']?></td>
                                            </tr>
                                    <?php
}
}
?>
                                    <!-- Add more rows for other route history transactions -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

</body>

</html>
<script>
    //VALIDATION FOR IMAGE INPUTTED, MUST BE JPG,JPEG,PNG ONLY
    document.getElementById("form1").addEventListener("submit", function(event) {
        var fileInput = document.getElementById("idImage");
        var file = fileInput.files[0];
        var allowedTypes = ['jpg', 'jpeg', 'png'];
        var fileType = file.name.split(".").pop().toLowerCase();
        var fileSize = file.size / 1024 / 1024;

        if (allowedTypes.indexOf(fileType) == -1) {
            alert("File type not allowed. Only jpg, jpeg, and png are allowed.");
            event.preventDefault();
            fileInput.value = "";
            return false;
        }

        if (fileSize > 5) {
            alert("File size is too large. Maximum allowed file size is 5 MB.");
            event.preventDefault();
            fileInput.value = "";
            return false;
        }
    });

    document.getElementById("form2").addEventListener("submit", function(event) {
        var fileInput = document.getElementById("profileImage");
        var file = fileInput.files[0];
        var allowedTypes = ['jpg', 'jpeg', 'png'];
        var fileType = file.name.split(".").pop().toLowerCase();
        var fileSize = file.size / 1024 / 1024;

        if (allowedTypes.indexOf(fileType) == -1) {
            alert("File type not allowed. Only jpg, jpeg, and png are allowed.");
            event.preventDefault();
            fileInput.value = "";
            return false;
        }

        if (fileSize > 5) {
            alert("File size is too large. Maximum allowed file size is 5 MB.");
            event.preventDefault();
            fileInput.value = "";
            return false;
        }
    });

    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
    }

    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }


</script>
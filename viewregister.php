<?php
require 'ver.php';

if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location:/');
}

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
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
    <title>View Car Registrations</title>
</head>

<body>
    <?php
//CHECKS IF THERE IS AN ANTION BEING PASSED
if (isset($_GET['actions'])) {
    //GET THE ID PASSED
    $VID = $_GET['id'];

    //SQL STATEMENT TO GET THE VEHICLE CURRENT INFORMATION
    $sql = "SELECT u.ufname, u.umname, u.ulname, u.uemail, u.uuserlevel, u.ubalance, u.user_id
    FROM vehicle_tbl v
    INNER JOIN user_tbl u ON v.vuser_id = u.user_id
    WHERE v.vehicle_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $VID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {
        //SETS VALUE TO VARIABLE FROM FETCHED DATA
        $USER_INFO = mysqli_fetch_assoc($result);
        $DFNAME = $USER_INFO['ufname'];
        $DMNAME = $USER_INFO['umname'];
        $DLNAME = $USER_INFO['ulname'];
        $USERLEVEL = $USER_INFO['uuserlevel'];
        $EMAIL = $USER_INFO['uemail'];
        $BALANCE = $USER_INFO['ubalance'];
        $DRIVERID = $USER_INFO['user_id'];
    }

    //CHECKS IF THE ACCEPT BUTTON IS PRESSED
    if ($_GET['actions'] == 'accept') {
        //IF USER LEVEL IS 3, PROMOTES TO DRIVER
        //ADDS 40 TICKETS BECAUSE NEW DRIVER MEANS NEW CAR APPROVED
        if ($USERLEVEL == 3) {
            $DNEWLEVEL = 4;
            $BALANCE += 40;
            //SQL STATEMENT TO UPDATE USERLEVEL
            $sql = "UPDATE user_tbl SET uuserlevel = ?, ubalance = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $DNEWLEVEL, $BALANCE, $DRIVERID);
            mysqli_stmt_execute($stmt);
        }

        //SQL STATEMENT TO UPDATE TO ACTIVE CAR/APPROVED
        $CSTATUS = 'ACTIVE';
        $sql = "UPDATE vehicle_tbl SET vstatus = ? WHERE vehicle_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $CSTATUS, $VID);
        if (mysqli_stmt_execute($stmt)) {

            //PHP MAILER NOTIFICATION
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = '*';
            $mail->SMTPAuth = true;
            $mail->Username = '*';
            $mail->Password = '*';
            $mail->SMTPSecure = '*';
            $mail->Port = 1;

            $mail->setFrom('*', 'Carpool App');
            $mail->addAddress($EMAIL);
            $mail->isHTML(true);
            $mail->Subject = 'Your Car Registration has been accepted!';

            // Customize the email message and formatting here
            $intro = "Dear " . $DFNAME . " " . $DMNAME . " " . $DLNAME . ",<br><br>";
            $line1 = "Thank you for contacting carpool<br>";
            $message = "Congratulations! We are pleased to inform you that your car has been successfully verified on our site.<br><br>";
            $signature = "Best regards,<br>Carpool App";
            $body = $intro . $message . "<br><br>" . $signature;

            $mail->Body = $body;

            if ($mail->send()) {
                echo '<script>
        function alertAndRedirect() {
          alert("Registration Confirmed!");
          window.location.href = "viewregister";
        }
        alertAndRedirect();
        </script>';
            } else {
                echo "<script>alert('Message could not be sent. Mailer Error: " . $mail->ErrorInfo . " 1');</script>";
            }
        }

        //CHECKS IF DECLINE BUTTON IS PRESSED
    } else if ($_GET['actions'] == 'decline') {

        //SQL STATEMENT TO DELETE CAR FROM VEHICLE TABLE IF REJECTED
        $sql = "DELETE FROM vehicle_tbl WHERE vehicle_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $VID);
        if (mysqli_stmt_execute($stmt)) {

            //PHP MAILER NOTIFICATION
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = '*';
            $mail->SMTPAuth = true;
            $mail->Username = '*';
            $mail->Password = '*';
            $mail->SMTPSecure = '*';
            $mail->Port = 1;

            $mail->setFrom('*', 'Carpool App');
            $mail->addAddress($EMAIL);
            $mail->isHTML(true);
            $mail->Subject = 'Your Car Registration has been Declined!';

            $intro = "Dear " . $DFNAME . " " . $DMNAME . " " . $DLNAME . ",<br><br>";
            $line1 = "Thank you for your interest in becoming a driver with Carpool.<br>";
            $message = "Unfortunately, we regret to inform you that your car registration has been rejected due to the following reason(s): <br><br> <i>[Insert reason for rejection here]</i> <br><br> We apologize for any inconvenience this may cause, and we appreciate your understanding in this matter. If you have any questions or concerns, please do not hesitate to contact us.<br><br>";
            $signature = "Best regards,<br>Carpool App";
            $body = $intro . $message . "<br><br>" . $signature;

            $mail->Body = $body;
            if ($mail->send()) {
                echo '<script>
        function alertAndRedirect() {
          alert("Registration Declined!");
          window.location.href = "viewregister";
        }
        alertAndRedirect();
        </script>';
            } else {
                echo "<script>alert('Message could not be sent. Mailer Error: " . $mail->ErrorInfo . " 2');</script>";
            }
        }
    }
}

?>
    <?php include 'header.php';?>

    <!-- VIEW CAR TABLE / CONTAINER -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .viewcartbl {
            margin-top: 20px;
        }

        .table-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            color: #fff;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 35px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    </head>

    <body>
        <div class="container">
            <div class="viewcartbl">
                <table class="table table-bordered table-light">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Driver Name</th>
                            <th scope="col">Vehicle Color</th>
                            <th scope="col">Vehicle Model</th>
                            <th scope="col">Vehicle Type</th>
                            <th scope="col">Plate Number</th>
                            <th scope="col">Engine Number</th>
                            <th scope="col">Insurance Number</th>
                            <th scope="col">Driver's License</th>
                            <th scope="col">Vehicle Image</th>
                            <th scope="col">Certificate of Registration</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
// SQL STATEMENT TO FETCH PENDING CARS
$sql = 'SELECT u.user_id, u.ufname, u.umname, u.ulname, u.uidimg, v.*
                            FROM user_tbl u
                            INNER JOIN vehicle_tbl v
                            ON u.user_id = v.vuser_id
                            WHERE v.vstatus = ?';

$CARSTATUS = "PENDING";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $CARSTATUS);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result)) {
    while ($ROW_INFO = mysqli_fetch_assoc($result)) {
        ?>
                                <tr>
                                    <th><?=$ROW_INFO['ufname'] . " " . $ROW_INFO['umname'] . " " . $ROW_INFO['ulname']?></th>
                                    <td style="background-color: <?=$ROW_INFO['vcolor']?>;"></td>
                                    <td><?=$ROW_INFO['vmodel']?></td>
                                    <td><?=$ROW_INFO['vtype']?></td>
                                    <td style="text-align: center;"><?=$ROW_INFO['vplate']?></td>
                                    <td><?=$ROW_INFO['vengnum']?></td>
                                    <td><?=$ROW_INFO['vinsurance']?></td>
                                    <td style="text-align: center;"><button class="btn btn-danger" type="button" onclick="displayImage('<?php echo $ROW_INFO['uidimg']; ?>')">Show Image</button></td>
                                    <td style="text-align: center;"><button class="btn btn-danger" type="button" onclick="displayImage('<?php echo $ROW_INFO['vimage']; ?>')">Show Image</button></td>
                                    <td style="text-align: center;"><button class="btn btn-danger" type="button" onclick="displayImage('<?php echo $ROW_INFO['vcrimg']; ?>')">Show Image</button></td>
                                    <td style="text-align: center;">
                                        <div class="table-actions">
                                            <button class="btn btn-danger" onclick="if (confirm('Are you sure you want to accept?')) { location.href='viewregister?actions=accept&id=<?=$ROW_INFO['vehicle_id']?>'; }">Accept</button>
                                            <button class="btn btn-danger" onclick="if (confirm('Are you sure you want to decline?')) { location.href='viewregister?actions=decline&id=<?=$ROW_INFO['vehicle_id']?>'; }">Decline</button>
                                        </div>
                                    </td>
                                </tr>
                        <?php
}
}
?>
                    </tbody>
                </table>
                <div class="pagination">
                    <!-- Pagination links go here -->
                </div>
            </div>
        </div>

        <!-- IMAGE DISPLAY MODAL -->
        <div id="imageModal" class="modal">
            <span class="close">&times;</span>
            <img class="modal-content" id="imageDisplay">
        </div>

        <script>
            // JavaScript code for displaying the image modal
            function displayImage(imageUrl) {
                var modal = document.getElementById("imageModal");
                var modalImg = document.getElementById("imageDisplay");
                modal.style.display = "block";
                modalImg.src = imageUrl;
                var span = document.getElementsByClassName("close")[0];
                span.onclick = function() {
                    modal.style.display = "none";
                };
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
        </script>

    </body>

</html>
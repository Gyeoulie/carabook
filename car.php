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
    <title>Car Registration</title>
</head>

<body>
    <?php
//CHECKS IF REGISTER BUTTON IS PRESSED
if (isset($_POST['register'])) {
    //SET THE VALUES TO VARIABLES
    $VCOLOR = $_POST['vcolor'];
    $VMODEL = $_POST['vmodel'];
    $VTYPE = $_POST['vtype'];
    $VPLATE = $_POST['vplate'];
    $VENGNUM = $_POST['vengnum'];
    $VINS = $_POST['vins'];
    $USERID = $_SESSION['userID'];

    //IMAGE1
    $fileName1 = $_FILES['vimage']['name'];
    $fileTmpName1 = $_FILES['vimage']['tmp_name'];
    // Get the file extension
    $fileExt1 = explode('.', $fileName1);
    $fileActualExt1 = strtolower(end($fileExt1));
    $fileNameNew1 = uniqid('', true) . "." . $fileActualExt1;
    // Set the destination folder
    $fileDestination1 = 'storimg/vimg/' . $fileNameNew1;
    // Move the file to the destination
    move_uploaded_file($fileTmpName1, $fileDestination1);

    //IMAGE2
    $fileName2 = $_FILES['crimage']['name'];
    $fileTmpName2 = $_FILES['crimage']['tmp_name'];
    // Get the file extension
    $fileExt2 = explode('.', $fileName2);
    $fileActualExt2 = strtolower(end($fileExt2));
    $fileNameNew2 = uniqid('', true) . "." . $fileActualExt2;
    // Set the destination folder
    $fileDestination2 = 'storimg/crimg/' . $fileNameNew2;
    // Move the file to the destination
    move_uploaded_file($fileTmpName2, $fileDestination2);

    //SQL STATEMENT FOR INSERT
    $sql = ("INSERT INTO vehicle_tbl (vuser_id, vcolor, vmodel, vtype, vplate, vengnum, vinsurance, vcrimg, vimage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssssss", $USERID, $VCOLOR, $VMODEL, $VTYPE, $VPLATE, $VENGNUM, $VINS, $fileDestination2, $fileDestination1);
    if (mysqli_stmt_execute($stmt)) {

        //FETCH CURRENT USER INFORMATION FOR EMAIL
        $sql = "SELECT ufname, umname, ulname, uemail FROM user_tbl WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $USERID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result)) {
            $REGISTER_INFO = mysqli_fetch_assoc($result);
            $DFNAME = $REGISTER_INFO['ufname'];
            $DMNAME = $REGISTER_INFO['umname'];
            $DLNAME = $REGISTER_INFO['ulname'];
            $EMAIL = $REGISTER_INFO['uemail'];
        }

        //PHP MAILER CODE
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
        $mail->Subject = 'Your application is being processed!';

        // Customize the email message and formatting here
        $intro = "Dear " . $DFNAME . " " . $DMNAME . " " . $DLNAME . ",<br><br>";
        $line1 = "Thank you for registering your car with Carpool.<br>";
        $message = "We are happy to inform you that your registration has been received and being processed. The next step is to bring your car to our designated inspection point for verification. Our team will inspect your car to ensure it meets our safety and quality standards.<br><br> Please bring your car to the inspection point at your earliest convenience. We cannot proceed with your application until your car passes the inspection. The location and hours of operation for the inspection point can be found on our website.<br><br>";
        $signature = "Best regards,<br>Carpool App";
        $body = $intro . $message . "<br><br>" . $signature;
        $mail->Body = $body;

        if ($mail->send()) {
            echo "<script>
          function alertAndRedirect() {
         alert('Successfully registered, Check your email the 2nd step!');
         window.location.href = 'car';
          }
          alertAndRedirect();
       </script>";
        } else {
            echo "<script>alert('Message could not be sent. Mailer Error: " . $mail->ErrorInfo . " 1');</script>";
        }
    } else {
        echo "<script>
      function alertAndRedirect() {
     alert('Registration Failed!');
     window.location.href = 'car';
      }
      alertAndRedirect();
   </script>";
    }
}

?>
    <?php include 'header.php';?>

    <style>
        .container {
            margin-top: 5%;
        }
    </style>
    <!-- REGISTERCAR IMAGE FORM/CONTAINER -->
    <!-- ENCTYPE IS IMPORTANT WHEN FORM HAS AN IMAGE INPUT -->
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="container registercar">
                    <form class="row g-3" action="" method="post" enctype="multipart/form-data">
                        <div class="col-md-4">
                            <label for="inputEmail4" class="form-label">Vehicle Color</label>
                            <input type="color" class="form-control" name="vcolor" required>
                        </div>
                        <div class="col-md-4">
                            <label for="inputPassword4" class="form-label">Vehicle Model</label>
                            <input type="text" class="form-control" name="vmodel" required>
                        </div>
                        <div class="col-md-4">
                            <label for="inputPassword4" class="form-label">Plate Number</label>
                            <input type="text" class="form-control" name="vplate" max="7" required>
                        </div>
                        <div class="col-md-6">
                            <label for="inputCity" class="form-label">Engine Number</label>
                            <input type="text" class="form-control" name="vengnum" min="11" max="17" required>
                        </div>
                        <div class="col-md-6">
                            <label for="inputCity" class="form-label">Insurance Number</label>
                            <input type="text" class="form-control" name="vins" min="8" max="19" required>
                        </div>
                        <div class="col-md-4">
                            <label for="inputState" class="form-label">Vehicle Type</label>
                            <select id="inputState" name="vtype" required class="form-select">
                                <option selected disabled hidden>Vehicle Type</option>
                                <option value="Hatchback">Hatchback</option>
                                <option value="SUV">SUV</option>
                                <option value="Van">Van</option>
                                <option value="Sedan">Sedan</option>
                                <option value="Pickup">Pickup</option>
                                <option value="SportsCar">SportsCar</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="inputZip" class="form-label">Vehicle Image</label>
                            <input class="form-control" type="file" id="vimage" name="vimage" accept="image/*" placeholder="Choose image" required>
                        </div>
                        <div class="col-md-4">
                            <label for="inputZip" class="form-label">Certificate of Registration</label>
                            <input class="form-control" type="file" id="crimage" name="crimage" accept="image/*" placeholder="Choose image" required>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck" required>
                                <label class="form-check-label" for="gridCheck">
                                    <a href="#">I agree with the terms and conditions.</a>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" name="register" class="btn btn-danger">Register</button>
                        </div>
                </div>
                </form>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>

<script>
    //VALIDATION FOR IMAGE INPUTTED, MUST BE JPG,JPEG,PNG ONLY
    document.getElementById("form1").addEventListener("submit", function(event) {
        var fileInput = document.getElementById("vimage");
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

    document.getElementById("form1").addEventListener("submit", function(event) {
        var fileInput = document.getElementById("crimage");
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
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carpooling</title>
    <link rel="shortcut icon" type="x-icon" href="image/car.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Include Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/bootstrap-icons.min.css">
</head>
<style>
    .accountclick {
        margin-top: 20px;
        text-align: center;
    }

    label {
        color: black;
    }

    .card {
        border-radius: 2%;
        margin-bottom: 10%;
    }

    .password-strength {
        margin-top: 5px;
    }

    .weak {
        color: red;
    }

    .medium {
        color: orange;
    }

    .strong {
        color: green;
    }
</style>
</head>
<?php
require 'conn_db.php';

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//CHECKS IF REGISTER BUTTON IS PRESSED
if (isset($_POST['register'])) {
    //SETS VARIABLE FROM FORM INPUT
    $FNAME = $_POST['fname'];
    $MNAME = $_POST['mname'];
    $LNAME = $_POST['lname'];
    $EMAIL = $_POST['email'];
    $CNUM = $_POST['cnum'];
    $PWD = $_POST['password'];
    $BDAY = $_POST['rbday'];
    $ADDRESS = $_POST['raddress'];
    $CITY = $_POST['rcity'];
    $PROV = $_POST['rprov'];
    $ZIP = $_POST['rzip'];
    $IDTYPE = $_POST['idtype'];

    //SETS NAME FOR THE IMAGE AND TRANSFER TO LOCAL LOCATION
    $fileName = $_FILES['idimage']['name'];
    $fileTmpName = $_FILES['idimage']['tmp_name'];
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
    $fileDestination = 'storimg/dimg/' . $fileNameNew;
    move_uploaded_file($fileTmpName, $fileDestination);

    //IMAGE2
    $fileName2 = $_FILES['pimage']['name'];
    $fileTmpName2 = $_FILES['pimage']['tmp_name'];
    // Get the file extension
    $fileExt2 = explode('.', $fileName2);
    $fileActualExt2 = strtolower(end($fileExt2));
    $fileNameNew2 = uniqid('', true) . "." . $fileActualExt2;
    // Set the destination folder
    $fileDestination2 = 'storimg/pimg/' . $fileNameNew2;
    // Move the file to the destination
    move_uploaded_file($fileTmpName2, $fileDestination2);

    //RANDOM GENERATED CODE FOR VERIFCAATION
    $VERCODE = bin2hex(random_bytes(16));

    //SQL STATEMENT TO CHECK IF EMAIL IS ALREADY REGISTERED
    $sql = "SELECT uemail FROM user_tbl WHERE uemail = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $EMAIL);

    if (mysqli_stmt_execute($stmt)) {
        $result = (mysqli_stmt_get_result($stmt));

        //IF EMAIL IS NOT FOUND PROCEED TO REGISTRATION ELSE PRINT AN ERROR
        if (mysqli_num_rows($result) == 0) {

            //SQL STATEMENT FOR REGISTERING ACCOUNT
            $sql = "INSERT INTO user_tbl (ufname, umname, ulname, uemail, upassword, ucnumber, ubirthdate, uaddress, ucity, uprovince, uzip, uvercode, uidtype, uidimg, upimg) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssssssssssss', $FNAME, $MNAME, $LNAME, $EMAIL, $PWD, $CNUM, $BDAY, $ADDRESS, $CITY, $PROV, $ZIP, $VERCODE, $IDTYPE, $fileDestination, $fileDestination2);
            if (mysqli_stmt_execute($stmt)) {

                //PHP MAILER
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
                $mail->Subject = 'User Registration';

                // Customize the email message and formatting here
                //INCLUDE IN THE EMAIL IS THE VERIFICATION CODE ALONG WITH THE LINK WHERE THE VERIFICATION CODE WILL BE CHECKED AND VERIFIED
                $intro = "<b>Carpool App</b><br>";
                $line1 = "<hr><br>";
                $line2 = "Good day, you only have one step to use the app. Click the link below to finalize the Carpool App Registration <br>";
                $verlink = "<a href='http://localhost/carpooling3/verify?token=" . $VERCODE . "'>Verify Email Address</a>";
                $body = $intro . $line1 . $line2 . $verlink;

                $mail->Body = $body;

                if ($mail->send()) {
                    echo '<script>
                    function alertAndRedirect() {
                      alert("Account Registered! Check your email for verification!");
                      window.location.href = "/";
                    }
                    alertAndRedirect();
                    </script>';
                } else {
                    echo "<script>alert('Message could not be sent. Mailer Error: " . $mail->ErrorInfo . "');</script>";
                }
            } else {
                echo "<script>alert('Registration Failed!');</script>";
            }
        } else {
            echo "<script>alert('Email Already Taken!');</script>";
        }
    } else {
        echo "<script>alert('Connect to Database Failed!');</script>";
    }
}

?>
<body>
    <div class="container-sm">
        <div class="card">
            <div class="card-header">
                <h5>Registration Form</h5>
            </div>
            <div class="card-body">
                <form class="row g-3" id="form1" action="" method="post" enctype="multipart/form-data">
                    <div class="col-md-4">
                        <label for="inputEmail4" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="fname" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputEmail4" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="mname" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputPassword4" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lname" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputPassword4" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div id="password-strength" class="password-strength"></div>
                        <p>Hint: The password should be at least twelve characters long. To make it stronger, use upper and lowercase letters, numbers, and symbols like ! " ? $ % ^ &.</p>
                    </div>

                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="cnum" maxlength="11" pattern="09\d{9}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputPassword4" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" name="rbday" required>
                    </div>
                    <div class="col-12">
                        <label for="inputAddress" class="form-label">Address</label>
                        <input type="text" class="form-control" name="raddress" placeholder="1234 Main St" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputCity" class="form-label">City</label>
                        <input type="text" class="form-control" name="rcity" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputProvince" class="form-label">Province</label>
                        <input type="text" class="form-control" name="rprov" required>
                    </div>
                    <div class="col-md-2">
                        <label for="inputZip" class="form-label">Zip</label>
                        <input type="text" class="form-control" name="rzip" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputID" class="form-label">ID Image</label>
                        <input class="form-control" type="file" id="idimage" name="idimage" accept="image/*" placeholder="Choose image" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputProfile" class="form-label">Profile Image</label>
                        <input class="form-control" type="file" id="pimage" name="pimage" accept="image/*" placeholder="Choose image" required>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="row mb-3">
                            <legend class="col-form-label col-sm-4 pt-0">Radios</legend>
                            <div class="col-sm-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="idtype" id="gridRadios1" value="1" checked>
                                    <label class="form-check-label" for="gridRadios1">
                                        Other
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="idtype" id="gridRadios2" value="2">
                                    <label class="form-check-label" for="gridRadios2">
                                        Driver's License
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck" required>
                            <label class="form-check-label" for="gridCheck" data-bs-toggle="modal" data-bs-target="#termsModal">
                                <a href="#"> I agree with the terms and conditions.</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" id="sub-reg" name="register" class="btn btn-danger" disabled>Register</button>
                    </div>
                </form>
            </div>
            <div class="accountclick">
                <p style="color:black;">Already have an account?</p>
                <a style="color:red;" href="/">
                    <p>Login Here!</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add your terms and conditions content here -->
                    <p>
                        User Eligibility<br>
                        1.1 You must be at least 18 years old to use the Website and avail of its services.<br>
                        1.2 By using the Website, you represent and warrant that you have the legal capacity to enter into this Agreement.<br><br>

                        Carpool Services<br>
                        2.1 The Website provides a platform for individuals to connect with each other for carpooling purposes.<br>
                        2.2 The Website does not provide transportation services and is not responsible for any actions or conduct of the users of the Website.<br>
                        2.3 Users of the Website are solely responsible for arranging and organizing their carpooling arrangements, including meeting points, schedules, and fees.<br><br>

                        User Conduct<br>
                        3.1 Users of the Website must comply with all applicable laws and regulations.<br>
                        3.2 Users must not engage in any fraudulent, illegal, or harmful activities on the Website.<br>
                        3.3 Users must not harass, threaten, or harm other users of the Website.<br>
                        3.4 Users are solely responsible for any content they post or share on the Website and must ensure that it does not infringe upon the rights of others or contain any unlawful, offensive, or inappropriate material.<br><br>

                        Privacy<br>
                        4.1 The Website respects your privacy and handles your personal information in accordance with its Privacy Policy.<br>
                        4.2 By using the Website, you consent to the collection, use, and disclosure of your personal information as described in the Privacy Policy.<br><br>

                        Disclaimer of Liability<br>
                        5.1 The Website is provided on an "as is" and "as available" basis without any warranties or representations, express or implied.<br>
                        5.2 The Website does not guarantee the accuracy, completeness, or reliability of any information provided on the Website.<br>
                        5.3 The Website shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of your use or inability to use the Website or the services provided by the Website.<br><br>

                        Intellectual Property<br>
                        6.1 The Website and its content, including but not limited to text, graphics, logos, and images, are protected by copyright and other intellectual property rights.<br>
                        6.2 Users may not reproduce, distribute, modify, or create derivative works of the Website or its content without the prior written consent of the Website.<br><br>

                        Modifications to the Agreement<br>
                        7.1 The Website reserves the right to modify or update this Agreement at any time without prior notice.<br>
                        7.2 Users are responsible for regularly reviewing this Agreement to stay informed of any changes.<br><br>

                        Governing Law and Jurisdiction<br>
                        8.1 This Agreement shall be governed by and construed in accordance with the laws of [Jurisdiction].<br>
                        8.2 Any disputes arising out of or relating to this Agreement shall be subject to the exclusive jurisdiction of the courts of [Jurisdiction].
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="document.getElementById('gridCheck').checked = true;">Accept</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
     // Function to calculate password strength and validate
     function checkPasswordStrength() {
        var password = document.getElementById("password").value;
        var passwordStrength = document.getElementById("password-strength");
        var submitButton = document.getElementById("sub-reg");

        var strength = 0;
        if (password.match(/[a-z]+/)) {
            strength += 1;
        }
        if (password.match(/[A-Z]+/)) {
            strength += 1;
        }
        if (password.match(/[0-9]+/)) {
            strength += 1;
        }
        if (password.length >= 8) {
            strength += 1;
        }

        if (strength === 0) {
            passwordStrength.textContent = "";
            submitButton.disabled = true; // Disable the submit button
        } else if (strength <= 2) {
            passwordStrength.textContent = "Weak";
            passwordStrength.className = "password-strength weak";
            submitButton.disabled = true; // Disable the submit button
        } else if (strength === 3) {
            passwordStrength.textContent = "Medium";
            passwordStrength.className = "password-strength medium";
            submitButton.disabled = false; // Enable the submit button
        } else {
            passwordStrength.textContent = "Strong";
            passwordStrength.className = "password-strength strong";
            submitButton.disabled = false; // Enable the submit button
        }
    }

    // Event listener for password input
    document.getElementById("password").addEventListener("input", checkPasswordStrength);


    //VALIDATION FOR IMAGE INPUTTED, MUST BE JPG,JPEG,PNG ONLY
    document.getElementById("form1").addEventListener("submit", function(event) {
        var fileInput = document.getElementById("idimage");
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
        var fileInput = document.getElementById("pimg");
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

    var today = new Date().toISOString().split("T")[0];
    document.getElementsByName("rbday")[0].setAttribute("max", today);

</script>

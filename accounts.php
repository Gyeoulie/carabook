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
    <title>Accounts</title>
</head>

<body>

    <?php include 'header.php';?>
    <!-- START OF ACCOUNTS TABLE -->
    <div class="container acctbl">
        <table class="table table-bordered table-light">
            <thead>
                <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col">User Type</th>
                    <th scope="col">ID Type</th>
                    <th scope="col">ID Image</th>
                </tr>
            </thead>
            <tbody>
                <?php
//SQL STATEMENT FOR FETECHING USERS - VER=2 MEANS USERS THAT ARE VERIFIED(EMAIL VERIFIED)
$ver = 2;
$sql = "SELECT ufname, umname, ulname, uemail, ucnumber, uuserlevel, uidtype, uidimg FROM user_tbl WHERE uuserlevel >= ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $ver);
if (mysqli_stmt_execute($stmt)) {
    $result = (mysqli_stmt_get_result($stmt));

    if (mysqli_num_rows($result) > 0) {
        while ($ROW_INFO = mysqli_fetch_assoc($result)) {

            //DISPLAY THE DESCRIPTION OF USER'S LEVEL
            if ($ROW_INFO['uuserlevel'] == 2 || $ROW_INFO['uuserlevel'] == 3) {
                $USERTYPE = "Passenger";
            } else if ($ROW_INFO['uuserlevel'] == 4) {
                $USERTYPE = "Driver";
            } else if ($ROW_INFO['uuserlevel'] == 6) {
                $USERTYPE = "Admin";
            }

            //DISPLAY THE DESCRIPTION OF USER'S ID TYPE
            if ($ROW_INFO['uidtype'] == 1) {
                $IDTYPE = "Others";
            } else if ($ROW_INFO['uidtype'] == 2) {
                $IDTYPE = "Driver's License";
            } else if ($ROW_INFO['uidtype'] == null) {
                $IDTYPE = "N/A";
            }

            ?>
                            <tr>
                                <td><?=$ROW_INFO['ufname'] . " " . $ROW_INFO['umname'] . " " . $ROW_INFO['ulname'];?></td>
                                <td><?=$ROW_INFO['uemail'];?></td>
                                <td><?=$ROW_INFO['ucnumber'];?></td>
                                <td><?=$USERTYPE?></td>
                                <td><?=$IDTYPE?></td>
                                <td style="text-align: center;"><button class="btn btn-danger" type="button" onclick="displayImage('<?php echo $ROW_INFO['uidimg']; ?>')"> Show Image </button></td>
                            </tr>
                <?php
}
    }
} else {
    //REDIRECT IF THERE IS AN ERROR IN FETCHING DATA
    echo '<script>
    function alertAndRedirect() {
    alert("Fetching Error!");
    window.location.href = "/";
    }
    alertAndRedirect();
    </script>';
}
?>
            </tbody>
        </table>
    </div>
    <!-- SHOW IMAGE MODAL -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imageDisplay">
    </div>
</body>

</html>

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
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>My Cars</title>
</head>

<body>
    <?php include 'header.php'; ?>
    <!-- CAR TABLE FORM -->
    <style>
        .tablecar {
            margin-top: 50px;
            margin-left: 10%;
        }

        .tablecar table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .tablecar th,
        .tablecar td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .tablecar th {
            background-color: white;
            color: black;
            font-weight: bold;
            vertical-align: middle;
        }

        .tablecar tbody tr:hover {
            background-color: #f8f9fa;
        }

        .tablecar tbody td {
            vertical-align: middle;
        }

        .tablecar .btn {
            padding: 6px 12px;
        }
    </style>
    </head>

    <body>
        <div class="container">
            <div class="tablecar">
                <table class="table table-light">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Vehicle ID</th>
                            <th scope="col">Color</th>
                            <th scope="col">Model</th>
                            <th scope="col">Type</th>
                            <th scope="col">Plate Number</th>
                            <th scope="col">Status</th>
                            <th scope="col">Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // FETCH USER ID FROM SESSOIN
                        $USERID = $_SESSION['userID'];

                        // SQL STATEMENT TO VIEW CURRENT CARS OF THE USER
                        $sql = 'SELECT * FROM vehicle_tbl WHERE vuser_id = ?';
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $USERID);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($result)) {
                            while ($ROW_INFO = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><?= $ROW_INFO['vehicle_id'] ?></td>
                                    <td style="background-color: <?= $ROW_INFO['vcolor'] ?>;"></td>
                                    <td><?= $ROW_INFO['vmodel'] ?></td>
                                    <td><?= $ROW_INFO['vtype'] ?></td>
                                    <td><?= $ROW_INFO['vplate'] ?></td>
                                    <td><?= "<b>" . $ROW_INFO['vstatus'] . "</b>" ?></td>
                                    <td><button class="btn btn-danger" type="button" onclick="displayImage('<?php echo $ROW_INFO['vimage']; ?>')">Show Image</button></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- IMAGE DISPLAY MODAL -->
            <div id="imageModal" class="modal">
                <span class="close">&times;</span>
                <img class="modal-content" id="imageDisplay">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
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
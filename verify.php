<?php
require 'conn_db.php';


//CHECKS IF THERE IS A TOKEN 
if (isset($_GET['token'])) {
    //FETCH THE TOKEN
    $vercode = $_GET['token'];


    //SQL STATEMENT TO CHECK IF THE TOKEN EXISTS
    $sql = "SELECT user_id, uuserlevel, uvercode, uidtype FROM user_tbl WHERE uvercode = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $vercode);
    if (mysqli_stmt_execute($stmt)) {
        $result = (mysqli_stmt_get_result($stmt));


        //IF TOKEN EXIST PROCEED ELSE REDIRECT
        if (mysqli_num_rows($result) == 1) {
            $ROW_INFO = mysqli_fetch_assoc($result);
            

            //SETS THE VALUE FROM FETCHED DATA
            $ACCID = $ROW_INFO['user_id'];  
            $IDTYPE = $ROW_INFO['uidtype'];
            $LEVEL = 2;
            $NEWV = null;
            $TICKETS = 10;

            //VALIDATION TO CHECK THE TYPE OF USER ID
            //IF USER HAS DRIVERS LICENSE, PROMOTE TO ELIGIBLE ROLE
            if ($IDTYPE == "2") {
                $DLEVEL = 3;
                $sql = "UPDATE user_tbl SET uuserlevel = ?, uvercode = ?, ubalance = ? WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'isii', $DLEVEL, $NEWV, $TICKETS, $ACCID);
                if (mysqli_stmt_execute($stmt)) {

                    echo '<script>
                function alertAndRedirect() {
                alert("Successfully Verified!");
                window.location.href = "index";
                }
                alertAndRedirect();
                </script>';
                }
            } else {
                //ELSE IF USER HAS OTHER TYPE OF ID, SET LEVEL TO PASSENGER
                $sql = "UPDATE user_tbl SET uuserlevel = ?, uvercode = ?, ubalance = ? WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'isii', $LEVEL, $NEWV,  $TICKETS, $ACCID);
                if (mysqli_stmt_execute($stmt)) {

                    echo '<script>
            function alertAndRedirect() {
            alert("Successfully Verified!");
            window.location.href = "index";
            }
            alertAndRedirect();
            </script>';
                } else {
                    echo '<script>
        function alertAndRedirect() {
        alert("Verification Failed!");
        window.location.href = "registeraccount";
        }
        alertAndRedirect();
        </script>';
                }
            }
        } else {
            echo '<script>
        function alertAndRedirect() {
        alert("Invalid Verification!");
        window.location.href = "registeraccount";
        }
        alertAndRedirect();
        </script>';
        }
    } else {
        echo '<script>
        function alertAndRedirect() {
        alert("Verification Error!");
        window.location.href = "registeraccount";
        }
        alertAndRedirect();
        </script>';
    }
} else {
    header('location:/');
}
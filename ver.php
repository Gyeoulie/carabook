<?php
session_start();
require 'conn_db.php';

//CHECKS IF LOGIN BUTTON IS PRESSED(IF FROM LOGIN PAGE)
if (isset($_POST['login'])) {
    $LEMAIL = $_POST['lemail'];
    $LPWD = $_POST['lpwd'];

    //SQL STATEMENT TO FETCH DATA FROME MAIL
    $check_credentials_sql = "SELECT upassword, user_ID, uuserlevel FROM user_tbl WHERE uemail = ?";
    $stmt = mysqli_prepare($conn, $check_credentials_sql);
    mysqli_stmt_bind_param($stmt, "s", $LEMAIL);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    //CHECKS IF THE ACCOUNT IS FOUND ELSE PRINTS AN ERROR
    if (mysqli_num_rows($result) === 1) {
        $account = mysqli_fetch_assoc($result);

        //CHECKS IF THE PASSWORD IS EQUAL TO INPUT ELSE PRINTS AN ERROR
        if ($account['upassword'] === $LPWD) {

            //SETS USERIDLEVEL AND SETS SESSION
            $_SESSION['userID'] = $account['user_ID'];
            $_SESSION['ulvl'] = $account['uuserlevel'];
            $_SESSION['logged_in'] = 'TRUE';

            $USERID = $_SESSION['userID'];
            $USERLVL = $_SESSION['ulvl'];

            //REDIRECT BASE FROM USER'S LEVEL
            if ($USERLVL == 1) {
                header("location:waiting.html");
            } else if ($USERLVL >= 2) {

                header("location:homepage");
            }

        } else {
            header("location: index?error=Incorrect Password!");
            exit;
        }
    } else {
        header("location: index?error=Unknown Account!");
        exit;
    }
}

//CHECKS IF THERE IS A SESSION TO CONSTANTLY UPDATE USER LEVEL AND BALANCE
if (isset($_SESSION)) {
    $sql = "SELECT uuserlevel, ubalance FROM user_tbl WHERE user_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['userID']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ACC_INFOLEVEL = mysqli_fetch_assoc($result);
    $_SESSION['ulvl'] = $ACC_INFOLEVEL['uuserlevel'];
    $_SESSION['UBAL'] = $ACC_INFOLEVEL['ubalance'];

}

if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"]) && !isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit;
}

<?php
require 'ver.php';
if (!isset($_SERVER['HTTP_REFERER'])) {
    header('location:/');
}

date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <!--
    - google font link
  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="shortcut icon" type="x-icon" href="image/car.png">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600&family=Open+Sans&display=swap"
        rel="stylesheet">
    <title>Booking</title>
</head>
<style>
.btn {
    position: relative;
    background: var(--background, var(--carolina-blue));
    color: var(--color, var(--white));
    min-width: var(--width, 40px);
    min-height: var(--height, 40px);
    padding: 5px;
    display: grid;
    place-items: center;
    border-radius: var(--radius-14);
    font-family: var(--ff-nunito);
    font-size: var(--fs-6);
    font-weight: var(--fw-600);
    overflow: hidden;
}

.btn ion-icon {
    font-size: 22px;
    --ionicon-stroke-width: 40px;
}

.btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, hsla(0, 0%, 100%, 0.4), transparent);
    opacity: 0;
    pointer-events: none;
    transition: var(--transition);
}

.btn:is(:hover, :focus) {
    box-shadow: var(--shadow-2);
}

.btn:is(:hover, :focus)::before {
    opacity: 1;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

label {
    font-weight: bold;
}

.form-group {
    margin-top: 10px;
}
</style>

<body>
    <?php
$HASROUTE = false;

//ROUTE CREATION
if (isset($_POST['broute'])) {

    $STARTPOINT = $_POST['startp'];
    $ENDPOINT = $_POST['endp'];
    $CARSEL = $_POST['carp'];
    $DATETIME = $_POST['datep'] . ' ' . $_POST['timep'];

    $sql = "SELECT ubalance FROM user_tbl WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $DRIVERBALANCE = mysqli_fetch_assoc($result);

    if ($DRIVERBALANCE['ubalance'] > 5) {

        $sql = "INSERT INTO route_tbl (rvehicle_id, rstart_point, rend_point, rdate_time) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'isss', $CARSEL, $STARTPOINT, $ENDPOINT, $DATETIME);
        if (mysqli_stmt_execute($stmt)) {
            $routeID = mysqli_insert_id($conn);
            $array = [];

            for ($i = 1; $i <= 4; $i++) {
                $seatKey = 'seat' . $i . '-input';
                if (!empty($_POST[$seatKey])) {
                    $seatValue = $_POST[$seatKey];
                    array_push($array, $i . '-' . $seatValue);
                }
            }

            foreach ($array as $seatValue) {
                $parts = explode("-", $seatValue);
                $sql = "INSERT INTO seatrate_tbl (sroute_id, sseat_type_id, sprice) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iii', $routeID, $parts[0], $parts[1]);
                mysqli_stmt_execute($stmt);
            }

            $TOTALTAX = 5;
            $RTYPE = 'ROUTE FEE';
            $sql = "INSERT INTO routetrans_tbl (rtroute_id, rttype, rttax) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $routeID, $RTYPE, $TOTALTAX);
            if (mysqli_stmt_execute($stmt)) {
                $sql = "UPDATE user_tbl SET ubalance = ubalance - ? WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $TOTALTAX, $_SESSION['userID']);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>
                                    function alertAndRedirect() {
                                   alert('Route Registered!');
                                   window.location.href = 'booking';
                                    }
                                    alertAndRedirect();
                                 </script>";
                }
            }

        }
    } else {
        echo "<script>alert('Insufficient Tickets!');</script>";
    }
    //SEAT BOOKING
} else if (isset($_POST['seatbook'])) {
    function processBooking($SID, $SSEAT, $SPICK, $SDROP, $conn)
    {

        $sql = "SELECT ubalance FROM user_tbl WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $SID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $USERINFO = mysqli_fetch_assoc($result);
        $USERCURBAL = $USERINFO['ubalance'];

        if ($USERCURBAL >= 250) {
            $sql = "SELECT sstatus, sprice FROM seatrate_tbl WHERE seatrate_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $SSEAT);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 1) {
                $SEATINFO = mysqli_fetch_assoc($result);
                $SEATPRICE = $SEATINFO['sprice'];
                if ($SEATINFO['sstatus'] == 'AVAILABLE') {
                    if ($USERCURBAL >= $SEATPRICE) {
                        $sql = "INSERT INTO booking_tbl (buser_id, bseatrate_id, bpickup_location, bdropoff_location) VALUES (?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, 'iiss', $SID, $SSEAT, $SPICK, $SDROP);
                        if (mysqli_stmt_execute($stmt)) {
                            $BOOKINGID = mysqli_insert_id($conn);
                            $sql = "INSERT INTO payment_tbl (pbooking_id, pamount) VALUES (?, ?)";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, 'ii', $BOOKINGID, $SEATPRICE);
                            if (mysqli_stmt_execute($stmt)) {
                                $NEWBAL = $USERCURBAL - $SEATPRICE;

                                $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, 'ii', $NEWBAL, $SID);
                                if (mysqli_stmt_execute($stmt)) {
                                    echo "<script>
                                    function alertAndRedirect() {
                                   alert('Successfully Booked!');
                                   window.location.href = 'booking';
                                    }
                                    alertAndRedirect();
                                 </script>";
                                }
                            }
                        }
                    } else {
                        echo "<script>alert('Insufficient Tickets!');</script>";
                    }
                } else {
                    echo "<script>alert('Seat Taken');</script>";
                }
            }
        } else {
            echo "<script>alert('Must atleast have 250 tickets to book!');</script>";
        }
    }

    $SID = $_SESSION['userID'];
    $SSEAT = $_POST['selseat'];
    $SPICK = $_POST['pickupl'];
    $SDROP = $_POST['dropoffl'];
    $STIME = strtotime($_POST['rtime']);

    $PENDINGB = 'APPROVED';
    $ACTIVER = 'ACTIVE';
    $TIMES = false;
    $HoursAhead = strtotime('+12 hours', $STIME);
    $HoursDelayed = strtotime('-12 hours', $STIME);

    $sql = "SELECT b.bbooking_status, r.rdate_time FROM booking_tbl b
    INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
    INNER JOIN route_tbl r ON s.sroute_id = r.route_id
    WHERE b.buser_id = ? AND b.bbooking_status = ? AND r.rstatus = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $SID, $PENDINGB, $ACTIVER);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 0) {
        processBooking($SID, $SSEAT, $SPICK, $SDROP, $conn);
    } else if (mysqli_num_rows($result) > 0) {
        while ($TIME_CHECK = mysqli_fetch_assoc($result)) {
            if (strtotime($TIME_CHECK['rdate_time']) > $HoursAhead || strtotime($TIME_CHECK['rdate_time']) > $HoursDelayed) {
                $TIMES = true;
            }
        }
        if ($TIMES == false) {
            processBooking($SID, $SSEAT, $SPICK, $SDROP, $conn);
        } else {
            echo "<script>alert('Booking must be atleast 12 hours');</script>";
        }
    }
    //BOOKING ACCEPT
} else if (isset($_POST['bapprove'])) {
    $BOOKID = $_POST['Abid'];
    $SEATID = $_POST['Asid'];

    $BSTATUS = 'APPROVED';
    $STSTATUS = 'TAKEN';

    $sql = "SELECT sstatus FROM seatrate_tbl WHERE seatrate_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $SEATID);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $SEATINFO = mysqli_fetch_assoc($result);
            if ($SEATINFO['sstatus'] != 'TAKEN') {
                $sql = "UPDATE booking_tbl SET bbooking_status = ? WHERE booking_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $BSTATUS, $BOOKID);
                if (mysqli_stmt_execute($stmt)) {

                    $sql = "UPDATE seatrate_tbl SET sstatus = ? WHERE seatrate_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "si", $STSTATUS, $SEATID);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<script>
                        function alertAndRedirect() {
                       alert('Successfully Approved!');
                       window.location.href = 'booking';
                        }
                        alertAndRedirect();
                     </script>";
                    }
                }
            }
        }
    }
    //BOOKING REJECT
} else if (isset($_POST['breject'])) {
    $BOOKID = $_POST['Abid'];
    $SEATID = $_POST['Asid'];

    $BSTATUSR = 'REJECT';

    $sql = "SELECT b.buser_id, p.payment_id FROM booking_tbl b
    INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
    WHERE b.booking_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $BOOKID);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $USERINFO = mysqli_fetch_assoc($result);
        $USERID = $USERINFO['buser_id'];
        $PAYID = $USERINFO['payment_id'];

        $sql = "UPDATE booking_tbl SET bbooking_status = ? WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $BSTATUSR, $BOOKID);
        if (mysqli_stmt_execute($stmt)) {
            $SRETURN = 'RETURNED';
            $sql = "UPDATE payment_tbl SET pstatus = ? WHERE payment_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $SRETURN, $BOOKID);
            if (mysqli_stmt_execute($stmt)) {
                $sql = "SELECT p.pamount, u.ubalance FROM payment_tbl p
            INNER JOIN booking_tbl b ON p.pbooking_id = b.booking_id
            INNER JOIN user_tbl u ON b.buser_id = u.user_id
            WHERE p.payment_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $PAYID);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $PAYMENTINFO = mysqli_fetch_assoc($result);
                    $NEWBAL = $PAYMENTINFO['ubalance'] + $PAYMENTINFO['pamount'];

                    $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ii", $NEWBAL, $USERID);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<script>alert('Rejected!');</script>";
                    }
                }
            }
        }
    } else {
        echo "<script>alert('Error Rejecting');</script>";
    }
    //ROUTE START
} else if (isset($_POST['routestart'])) {
    $STROUTEID = $_POST['srouteid'];
    $RSTATUS = 'ENROUTE';

    $sql = "UPDATE route_tbl SET rstatus = ? WHERE route_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $RSTATUS, $STROUTEID);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
        function alertAndRedirect() {
       alert('Route Started!');
       window.location.href = 'booking';
        }
        alertAndRedirect();
     </script>";

        $BOOKPENDING = 'PENDING';

        $sql = "SELECT b.booking_id, b.buser_id, p.payment_id, p.pamount, u.ubalance
        FROM route_tbl r
        INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
        INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
        INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
        INNER JOIN user_tbl u ON b.buser_id = u.user_id
        WHERE r.route_id = ? AND b.bbooking_status = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $STROUTEID, $BOOKPENDING);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($BOOKINGREJINFO = mysqli_fetch_assoc($result)) {
                $BOOKID = $BOOKINGREJINFO['booking_id'];
                $USERID = $BOOKINGREJINFO['buser_id'];
                $PAYID = $BOOKINGREJINFO['payment_id'];
                $PAMOUNT = $BOOKINGREJINFO['pamount'];
                $UBALANCE = $BOOKINGREJINFO['ubalance'];

                $BSTATUSR = 'REJECT';
                $SRETURN = 'RETURNED';
                $NEWBAL = $UBALANCE + $PAMOUNT;

                $sql = "UPDATE booking_tbl SET bbooking_status = ? WHERE booking_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $BSTATUSR, $BOOKID);
                mysqli_stmt_execute($stmt);

                $sql = "UPDATE payment_tbl SET pstatus = ? WHERE payment_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $SRETURN, $PAYID);
                mysqli_stmt_execute($stmt);

                $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $NEWBAL, $USERID);
                mysqli_stmt_execute($stmt);
            }
        }
    }
    //ROUTE FINISH
} else if (isset($_POST['routefinish'])) {
    $FRID = $_POST['srouteid'];
    $RSTATUS = 'COMPLETED';

    $sql = "UPDATE route_tbl SET rstatus = ? WHERE route_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $RSTATUS, $FRID);
    if (mysqli_stmt_execute($stmt)) {
        $SEATSTATUS = 'TAKEN';
        $BOOKINGSTATUS = 'APPROVED';
        $TOTAL_EARNINGS = 0;

        $sql = "SELECT b.booking_id, p.pamount AS total_earnings
                FROM route_tbl r
                INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
                INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
                INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
                WHERE r.route_id = ? AND s.sstatus = ? AND b.bbooking_status = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $FRID, $SEATSTATUS, $BOOKINGSTATUS);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($FINISH_INFO = mysqli_fetch_assoc($result)) {
            $TOTAL_EARNINGS += $FINISH_INFO['total_earnings'];
            $BOOKINGIDF = $FINISH_INFO['booking_id'];

            $COMPB = "COMPLETED";
            $COMPP = "PAID";

            $sql = "UPDATE booking_tbl b
                    INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
                    SET b.bbooking_status = ?,
                        p.pstatus = ?
                    WHERE b.booking_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $COMPB, $COMPP, $BOOKINGIDF);
            mysqli_stmt_execute($stmt);
        }

        $TOTAL_TAX = round($TOTAL_EARNINGS * 0.05, 0);
        $RTYPE = 'EARNINGS';
        $sql = "INSERT INTO routetrans_tbl (rtroute_id, rttype, rtamount, rttax) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isii", $FRID, $RTYPE, $TOTAL_EARNINGS, $TOTAL_TAX);
        if (mysqli_stmt_execute($stmt)) {
            $sql = "UPDATE user_tbl SET ubalance = ubalance + ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $TOTAL_EARNINGS, $_SESSION['userID']);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                function alertAndRedirect() {
               alert('Route Finished!');
               window.location.href = 'booking';
                }
                alertAndRedirect();
             </script>";
            }
        }
    }
    //ROUTE CANCEL
} else if (isset($_POST['routecancel'])) {
    $STROUTEID = $_POST['srouteid'];
    $RSTATUS = 'CANCELLED';

    $sql = "UPDATE route_tbl SET rstatus = ? WHERE route_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $RSTATUS, $STROUTEID);
    if (mysqli_stmt_execute($stmt)) {
        //echo "<script>alert('Route Started!');</script>";

        $sql = "SELECT u.user_id FROM route_tbl r
        INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
        INNER JOIN user_tbl u ON v.vuser_id = u.user_id
        WHERE r.route_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $STROUTEID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $DRIVERINFO = mysqli_fetch_assoc($result);
        $DRIVERID = $DRIVERINFO['user_id'];

        $DEDUCTION = 20;
        $CANCELTYPE = 'CANCEL';

        $sql = "INSERT INTO routetrans_tbl (rtroute_id, rttype, rttax) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $STROUTEID, $CANCELTYPE, $DEDUCTION);
        if (mysqli_stmt_execute($stmt)) {

            $sql = "UPDATE user_tbl SET ubalance = ubalance - ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $DEDUCTION, $DRIVERID);
            if (mysqli_stmt_execute($stmt)) {
                $BOOKPENDING1 = 'APPROVED';
                $BOOKPENDING2 = 'PENDING';

                $sql = "SELECT b.booking_id, b.buser_id, p.payment_id, p.pamount, u.ubalance
                FROM route_tbl r
                INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
                INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
                INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
                INNER JOIN user_tbl u ON b.buser_id = u.user_id
                WHERE r.route_id = ? AND b.bbooking_status = ? OR b.bbooking_status = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iss", $STROUTEID, $BOOKPENDING1, $BOOKPENDING2);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result)) {
                        while ($BOOKINGREJINFO = mysqli_fetch_assoc($result)) {
                            $BOOKID = $BOOKINGREJINFO['booking_id'];
                            $USERID = $BOOKINGREJINFO['buser_id'];
                            $PAYID = $BOOKINGREJINFO['payment_id'];
                            $PAMOUNT = $BOOKINGREJINFO['pamount'];
                            $UBALANCE = $BOOKINGREJINFO['ubalance'];

                            $BSTATUSR = 'CANCELLED';
                            $SRETURN = 'RETURNED';
                            $NEWBAL = $UBALANCE + $PAMOUNT;

                            $sql = "UPDATE booking_tbl SET bbooking_status = ? WHERE booking_id = ?";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "si", $BSTATUSR, $BOOKID);
                            if (mysqli_stmt_execute($stmt)) {

                                $sql = "UPDATE payment_tbl SET pstatus = ? WHERE payment_id = ?";
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "si", $SRETURN, $PAYID);
                                if (mysqli_stmt_execute($stmt)) {

                                    $sql = "UPDATE user_tbl SET ubalance = ? WHERE user_id = ?";
                                    $stmt = mysqli_prepare($conn, $sql);
                                    mysqli_stmt_bind_param($stmt, "ii", $NEWBAL, $USERID);
                                    if (mysqli_stmt_execute($stmt)) {
                                        echo "<script>
                            function alertAndRedirect() {
                           alert('Route Cancelled!');
                           window.location.href = 'booking';
                            }
                            alertAndRedirect();
                         </script>";
                                    }
                                }
                            }
                        }
                    } else {
                        echo "<script>
                    function alertAndRedirect() {
                   alert('Route Cancelled!');
                   window.location.href = 'booking';
                    }
                    alertAndRedirect();
                 </script>";
                    }
                }
            }
        }
    }
    //USER CANCEL BOOKING
} else if (isset($_POST['ubcancel'])) {
    $CURTIME = time();
    $BTIMESTAMP = $_POST['btime'];
    $ROUTETIME = $_POST['rtime'];
    $BID = $_POST['bkid'];

    $bookingTime = strtotime($BTIMESTAMP);
    $timeDiff = abs($CURTIME - $bookingTime);

    $routetTime = strtotime($ROUTETIME);
    $timeDiff2 = ($routetTime - $CURTIME);

    if ($timeDiff2 <= 600) {
        echo "<script>alert('Cannot be cancelled!');</script>";
    } else if ($timeDiff <= 600) {
        $BOOKSTATUS = 'CANCELLED';
        $SEATSTATUS = 'AVAILABLE';
        $PSTATUS = 'CANCELLED1';
        $NEWBAL = 0;

        $sql = "UPDATE booking_tbl b
                INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
                INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
                INNER JOIN user_tbl u ON u.user_id = b.buser_id
                SET b.bbooking_status = ?,
                    s.sstatus = ?,
                    p.pstatus = ?,
                    u.ubalance = u.ubalance + p.pamount
                WHERE b.booking_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $BOOKSTATUS, $SEATSTATUS, $PSTATUS, $BID);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
            function alertAndRedirect() {
           alert('Cancelled!');
           window.location.href = 'booking';
            }
            alertAndRedirect();
         </script>";
        }
    } else if ($timeDiff > 600) {
        $BOOKSTATUS = 'CANCELLED';
        $SEATSTATUS = 'AVAILABLE';
        $PSTATUS = 'CANCELLED2';
        $NEWBAL = 0;
        $DEDUCTION = 10;

        $sql = "UPDATE booking_tbl b
                INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
                INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
                INNER JOIN user_tbl u ON u.user_id = b.buser_id
                SET b.bbooking_status = ?,
                    s.sstatus = ?,
                    p.pstatus = ?,
                    p.pamount = ?,
                    u.ubalance = u.ubalance - ?
                WHERE b.booking_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssiii", $BOOKSTATUS, $SEATSTATUS, $PSTATUS, $DEDUCTION, $DEDUCTION, $BID);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
            function alertAndRedirect() {
           alert('Cancelled!');
           window.location.href = 'booking';
            }
            alertAndRedirect();
         </script>";
        }
    }
    //DRIVER CANCEL USER BOOKING
} else if (isset($_POST['rejuser'])) {
    $CBOOKINGID = $_POST['canbook'];
    $BOOKSTATUS = 'CANCELLED';
    $SEATSTATUS = 'AVAILABLE';
    $PSTATUS = 'RETURNED';

    $sql = "UPDATE booking_tbl b
    INNER JOIN payment_tbl p ON b.booking_id = p.pbooking_id
    INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
    INNER JOIN user_tbl u ON u.user_id = b.buser_id
    SET b.bbooking_status = ?,
        s.sstatus = ?,
        p.pstatus = ?,
        u.ubalance = u.ubalance + p.pamount
    WHERE b.booking_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $BOOKSTATUS, $SEATSTATUS, $PSTATUS, $CBOOKINGID);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            function alertAndRedirect() {
           alert('User Booking Cancelled!');
           window.location.href = 'booking';
            }
            alertAndRedirect();
         </script>";
    }
}

?>





    <?php include 'header.php';?>



    <br>
    <!-- ROUTE BUTTON -->
    <?php
if ($_SESSION['ulvl'] == 4) {
    $DRIVERROUTE1 = 'ACTIVE';
    $DRIVERROUTE2 = 'ENROUTE';
    $sql = "SELECT r.route_id FROM route_tbl r
    INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
    INNER JOIN user_tbl u ON v.vuser_id = u.user_id
    WHERE r.rstatus = ? OR r.rstatus = ? AND u.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $DRIVERROUTE1, $DRIVERROUTE2, $_SESSION['userID']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 0) {
        ?>
    <div class="text-center">
        <button class="btn btn-primary" onclick="openModal('routemodal')">Route</button>
    </div>
    <?php

    }

}
?>

    <!-- SETTING ROUTE MODAL -->
    <div id="routemodal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('routemodal')">&times;</span>
            <h1>Set your Route</h1>
            <div class="form-group">
                <form action="" method="POST" onsubmit="return validateSeats()">
                    <label for="start">Start Point:</label>
                    <input type="text" id="start" name="startp" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end">End Point:</label>
                <input type="text" id="end" name="endp" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="car">Car:</label>
                <select id="car" name="carp" class="form-control" required>
                    <option value="" disabled selected>Select Car</option>
                    <?php
$sql = 'SELECT * FROM vehicle_tbl WHERE vuser_id = ?';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result)) {
    while ($CAR_LIST = mysqli_fetch_assoc($result)) {

        ?>
                    <option value="<?=$CAR_LIST['vehicle_id']?>">
                        <?=$CAR_LIST['vmodel'] . " - " . $CAR_LIST['vehicle_id']?></option>
                    <?php
}
}
?>
                </select>
            </div>
            <div class="form-group">
                <label for="time">Date:</label>
                <input type="date" id="date" name="datep" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="time">Time:</label>
                <input type="time" id="time" name="timep" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="seats">Seat Capacity:</label>
                <br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="seat1" id="seat1">
                    <label class="form-check-label" for="seat1">Front Seat</label>
                    <input type="number" class="form-control" name="seat1-input" id="seat1-input" max="5000" required>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="seat2" id="seat2">
                    <label class="form-check-label" for="seat2">Left Seat</label>
                    <input type="number" class="form-control" name="seat2-input" id="seat2-input" max="5000" required>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="seat3" id="seat3">
                    <label class="form-check-label" for="seat3">Middle Seat</label>
                    <input type="number" class="form-control" name="seat3-input" id="seat3-input" max="5000" required>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="seat4" id="seat4">
                    <label class="form-check-label" for="seat4">Right Seat</label>
                    <input type="number" class="form-control" name="seat4-input" id="seat4-input" max="5000" required>
                </div>
            </div>
            <br>
            <div class="button-group text-center">
                <button type="submit" class="btn btn-sm btn-primary" name="broute">Submit</button>
                <button class="btn btn-sm btn-danger" onclick="closeModal('routemodal')">Cancel</button>
            </div>
            </form>
        </div>
    </div>




    <!-- USER CURRENT BOOKING -->
    <?php
$sql = "SELECT * FROM booking_tbl WHERE buser_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    while ($USERBOOKING = mysqli_fetch_assoc($result)) {
        if ($USERBOOKING['bbooking_status'] == 'APPROVED' || $USERBOOKING['bbooking_status'] == 'ENROUTE') {

            $sql = "SELECT b.bpickup_location, b.bdropoff_location, b.btimestamp, s.sseat_type_id, s.sprice, r.rstart_point, r.rend_point, r.rstatus, r.rdate_time, v.vimage, v.vtype, u.ufname, u.umname, u.ulname
            FROM booking_tbl b
            INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
            INNER JOIN route_tbl r ON s.sroute_id = r.route_id
            INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
            INNER JOIN user_tbl u ON v.vuser_id = u.user_id
           WHERE b.booking_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $USERBOOKING['booking_id']);
            mysqli_stmt_execute($stmt);
            $rs = mysqli_stmt_get_result($stmt);
            $USERBOOKINGINFO = mysqli_fetch_assoc($rs)
            ?>

    <div class="container current-booking" style="margin-top: 2%;">
        <div class="card current-booking">
            <h1 class="text-center">Current Booking</h1>
            <ul class="featured-car-list" style="margin-right: 3%; margin-bottom:2%">

                <li>
                    <div class="featured-car-card" style="margin-left: auto; margin-right: 0;">

                        <figure class="card-banner">
                            <img src="<?=$USERBOOKINGINFO['vimage']?>" alt="Toyota RAV4 2021" loading="lazy" width="440"
                                height="300" class="w-100">
                        </figure>

                    </div>
                </li>

                <li>
                    <div class="featured-car-card"
                        style="margin-left: auto; margin-right: 0; width:800px; height: 250px">


                        <div class="card-content">

                            <div class="card-title-wrapper">
                                <h3 class="h3 card-title">
                                    <a
                                        href="#"><?=$USERBOOKINGINFO['rstart_point'] . " - " . $USERBOOKINGINFO['rend_point']?></a>
                                </h3>

                                <data class="year" value="2019"><?=$USERBOOKINGINFO['vtype']?></data>
                            </div>

                            <ul class="card-list">

                                <li class="card-list-item">
                                    <ion-icon name="people-outline"></ion-icon>

                                    <span class="card-item-text"><?php
if ($USERBOOKINGINFO['sseat_type_id'] == 1) {
                echo 'Front Seat';
            } else if ($USERBOOKINGINFO['sseat_type_id'] == 2) {
                echo 'Left Seat';
            } else if ($USERBOOKINGINFO['sseat_type_id'] == 3) {
                echo 'Middle Seat';
            } else if ($USERBOOKINGINFO['sseat_type_id'] == 4) {
                echo 'Right Seat';
            }
            ?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="calendar-outline"></ion-icon>

                                    <span
                                        class="card-item-text"><?=date("F j, Y", strtotime($USERBOOKINGINFO['rdate_time']))?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="person-outline"></ion-icon>

                                    <span
                                        class="card-item-text"><?=$USERBOOKINGINFO['ufname'] . " " . $USERBOOKINGINFO['umname'] . " " . $USERBOOKINGINFO['ulname']?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="time-outline"></ion-icon>

                                    <span
                                        class="card-item-text"><?=date("h:i A", strtotime($USERBOOKINGINFO['rdate_time']))?></span>
                                </li>
                                <li class="card-list-item">
                                    <ion-icon name="location-outline"></ion-icon>

                                    <span class="card-item-text"><?=$USERBOOKINGINFO['bpickup_location']?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="location-outline"></ion-icon>

                                    <span class="card-item-text"><?=$USERBOOKINGINFO['bdropoff_location']?></span>
                                </li>

                            </ul>

                            <div class="card-price-wrapper">

                                <p class="card-price">
                                    <strong>₱<?=$USERBOOKINGINFO['sprice']?></strong>
                                </p>

                            </div>

                        </div>

                    </div>
                    <div
                        style="margin-top: 2%; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-left:90%">
                        <form action="" method="post"
                            onsubmit="return confirm('Are you sure you want to cancel? Charges may apply.');">
                            <input type="hidden" name="btime" value="<?=$USERBOOKINGINFO['btimestamp']?>">
                            <input type="hidden" name="bkid" value="<?=$USERBOOKING['booking_id']?>">
                            <input type="hidden" name="rtime" value="<?=$USERBOOKINGINFO['rdate_time']?>">
                            <button type="submit" class="btn btn-danger" name="ubcancel" <?php
$CURTIME = time();
            $routetTime = $USERBOOKINGINFO['rdate_time'];

            $bookingTime = strtotime($routetTime);
            $timeDiff = $bookingTime - $CURTIME;

            if ($timeDiff <= 600 || $USERBOOKINGINFO['rstatus'] == 'ENROUTE' || $USERBOOKINGINFO['rstatus'] == 'COMPLETED') {
                echo "disabled";
            }

            ?>>Cancel</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php
}
    }
}

?>


    <!-- DRIVER CURRENT ROUTE -->
    <?php
if ($_SESSION['ulvl'] == 4) {
    $ACT = 'ACTIVE';
    $OTW = 'ENROUTE';
    $sql = "SELECT r.*, v.vimage FROM route_tbl r
    INNER JOIN vehicle_tbl v ON r.rvehicle_id  = v.vehicle_id
    INNER JOIN user_tbl u ON v.vuser_id = u.user_id
    WHERE u.user_id = ? AND r.rstatus = ? OR r.rstatus = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $_SESSION['userID'], $ACT, $OTW);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) === 1) {
        $HASROUTE = true;

        $ROUTEPASS = mysqli_fetch_assoc($result)

        ?>
    <div class="container current-route" style="margin-top: 2%;">
        <div class="card current-route">
            <h1 class="text-center">Current Route</h1>
            <ul class="featured-car-list" style="margin-right: 3%; margin-bottom:2%">

                <li>
                    <div class="featured-car-card" style="margin-left: auto; margin-right: 0;">

                        <figure class="card-banner">
                            <img src="<?=$ROUTEPASS['vimage']?>" alt="Toyota RAV4 2021" loading="lazy" width="440"
                                height="300" class="w-100">
                        </figure>

                    </div>
                </li>

                <li>
                    <div class="featured-car-card">


                        <div class="card-content">
                            <ul class="card-list">

                                <li class="card-list-item">
                                    <ion-icon name="location-outline"></ion-icon>

                                    <span class="card-item-text"><strong>Start Point</strong></span>
                                </li>

                                <li class="card-list-item">

                                </li>
                                <li class="card-list-item">
                                    <span class="card-item-text"><strong><?=$ROUTEPASS['rstart_point']?></strong></span>
                                </li>
                                <li class="card-list-item">

                                </li>


                                <li class="card-list-item">
                                    <ion-icon name="location-outline"></ion-icon>

                                    <span class="card-item-text"><strong>End Point</strong></span>
                                </li>
                                <li class="card-list-item">
                                </li>
                                <li class="card-list-item">
                                    <span class="card-item-text"><strong><?=$ROUTEPASS['rend_point']?></strong></span>
                                </li>
                                <li class="card-list-item">
                                </li>
                                <li class="card-list-item">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                    <span class="card-item-text"><strong>Date & Time</strong></span>
                                </li>
                                <li class="card-list-item">
                                </li>
                                <li class="card-list-item">
                                    <span
                                        class="card-item-text"><strong><?=date("F j, Y H:i", strtotime($ROUTEPASS['rdate_time']))?></strong></span>
                                </li>
                                <li class="card-list-item">
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="featured-car-card">
                        <div class="card-content">

                            <div class="card-title-wrapper">
                                <h3 class="h3 card-title">
                                    <a>Seat Information</a>
                                </h3>
                            </div>
                            <ul class="card-list">
                                <?php
$BKSTATUS1 = 'APPROVED';
        $BKSTATUS2 = 'PENDING';

        $TOTALEARNINGS = 0;
        $sql = "SELECT * FROM seatrate_tbl WHERE sroute_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ROUTEPASS['route_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)) {
            while ($SEATLIST = mysqli_fetch_assoc($result)) {
                //$TOTALEARNINGS += $SEATLIST['sprice'] - ($SEATLIST['sprice'] * 0.05);

                ?>
                                <li class="card-list-item">
                                    <ion-icon name="people-outline"></ion-icon>
                                    <span class="card-item-text" <?php
if ($SEATLIST['sstatus'] == 'TAKEN') {
                    ?> onclick="openModal('seatinfo_<?=$SEATLIST['seatrate_id']?>')" <?php
}?>><?php
if ($SEATLIST['sstatus'] == 'AVAILABLE') {
                    echo 'SEAT ' . $SEATLIST['sseat_type_id'] . ' - AVAILABLE';
                } else if ($SEATLIST['sstatus'] == 'TAKEN') {
                    echo 'SEAT ' . $SEATLIST['sseat_type_id'] . ' - BOOKED';
                }
                ?></span>
                                    <?php
$sql = "SELECT u.ufname, u.umname, u.ulname, u.ucnumber, u.upimg, s.sprice, s.sstatus, b.booking_id FROM seatrate_tbl s
                     INNER JOIN booking_tbl b ON s.seatrate_id  = b.bseatrate_id
                     INNER JOIN user_tbl u ON  b.buser_id = u.user_id
                     WHERE s.seatrate_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $SEATLIST['seatrate_id']);
                mysqli_stmt_execute($stmt);
                $rs = mysqli_stmt_get_result($stmt);
                $SEATUSERINFO = mysqli_fetch_assoc($rs);

                //echo "<script>alert('".$SEATUSERINFO['sstatus']."');</script>";
                ?>
                                    <!-- Modal -->
                                    <div id="seatinfo_<?=$SEATLIST['seatrate_id']?>" class="modal">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close"
                                                        onclick="closeModal('seatinfo_<?=$SEATLIST['seatrate_id']?>')"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="<?=$SEATUSERINFO['upimg']?>" alt="">
                                                    <h5 class="modal-title" id="termsModalLabel">
                                                        <?=$SEATUSERINFO['ufname'] . " " . $SEATUSERINFO['umname'] . " " . $SEATUSERINFO['ulname']?>
                                                    </h5>
                                                    <p>
                                                        <?=$SEATUSERINFO['ucnumber']?>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">

                                                    <button class="btn btn-danger" data-bs-dismiss="modal"
                                                        style="margin-right: 200px;"
                                                        onclick="closeModal('seatinfo_<?=$SEATLIST['seatrate_id']?>')">Close</button>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="canbook"
                                                            value="<?=$SEATUSERINFO['booking_id']?>">
                                                        <button type="submit" name="rejuser" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancel Booking</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
$sqlearnings = "SELECT sprice, sstatus FROM seatrate_tbl WHERE seatrate_id = ?";
                $stmt = mysqli_prepare($conn, $sqlearnings);
                mysqli_stmt_bind_param($stmt, "i", $SEATLIST['seatrate_id']);
                mysqli_stmt_execute($stmt);
                $rsearnings = mysqli_stmt_get_result($stmt);
                $SEATEARNINGS = mysqli_fetch_assoc($rsearnings);

                if ($SEATEARNINGS['sstatus'] == 'TAKEN') {
                    $TOTALEARNINGS += $SEATEARNINGS['sprice'];
                }
            }
        }
        $TOTALEARNINGS = round($TOTALEARNINGS - ($TOTALEARNINGS * 0.05), 0);

        ?>

                            </ul>
                            <div class="card-price-wrapper">
                                <p class="card-price">
                                    <strong>₱<?=$TOTALEARNINGS?></strong> Booking Earnings
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="button text-center" style="margin-top:5%">
                        <form action="" method="POST">
                            <input type="hidden" name="srouteid" value="<?=$ROUTEPASS['route_id']?>">
                            <?php if ($ROUTEPASS['rstatus'] == 'ACTIVE') {
            ?>
                            <button type="submit" name="routestart" class="btn btn-primary" <?php
$CURTIME = time();
            if ($CURTIME <= strtotime($ROUTEPASS['rdate_time'])) {
                echo 'disabled';
            }

            ?>>Start</button>
                            <?php
} else if ($ROUTEPASS['rstatus'] == 'ENROUTE') {
            ?>
                            <button type="submit" name="routefinish" class="btn btn-primary">Finish</button>
                            <?php
}
        ?>
                            <button type="submit" name="routecancel" class="btn btn-danger">Cancel</button>
                        </form>
                    </div>

                </li>


            </ul>


            <!-- TABLE FOR APPROVAL -->
            <?php if ($ROUTEPASS['rstatus'] == 'ACTIVE') {
            ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Passenger</th>
                            <th>Seat</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$BSTATUS = 'PENDING';

            $sql = "SELECT u.ufname, u.umname, u.ulname, u.ubalance, u.user_id, s.sprice, s.sseat_type_id, s.seatrate_id, b.booking_id, b.bpickup_location, b.bdropoff_location, r.rdate_time FROM route_tbl r
                            INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
                            INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
                            INNER JOIN user_tbl u ON b.buser_id = u.user_id
                            WHERE r.route_id = ? AND b.bbooking_status = ? ORDER BY b.btimestamp";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $ROUTEPASS['route_id'], $BSTATUS);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                while ($PENSEATS = mysqli_fetch_assoc($result)) {
                    $USERBOOK = false;
                    $USERID = $PENSEATS['user_id'];

                    $HoursAhead12 = strtotime('+12 hours', strtotime($PENSEATS['rdate_time']));
                    $HoursDelayed12 = strtotime('-12 hours', strtotime($PENSEATS['rdate_time']));

                    $PENDINGB = 'APPROVED';
                    $ACTIVER = 'ACTIVE';

                    $sql = "SELECT b.bbooking_status, r.rdate_time FROM booking_tbl b
                INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
                INNER JOIN route_tbl r ON s.sroute_id = r.route_id
                WHERE b.buser_id = ? AND b.bbooking_status = ? AND r.rstatus = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "iss", $USERID, $PENDINGB, $ACTIVER);
                    mysqli_stmt_execute($stmt);
                    $rs = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($rs) > 0) {
                        while ($TIME_CHECK = mysqli_fetch_assoc($rs)) {
                            if (strtotime($TIME_CHECK['rdate_time']) > $HoursAhead12 || strtotime($TIME_CHECK['rdate_time']) > $HoursDelayed12) {

                                $USERBOOK = true;
                            }
                        }
                    }

                    ?>

                        <tr>
                            <td><?=$PENSEATS['ufname'] . " " . $PENSEATS['umname'] . " " . $PENSEATS['ulname']?>
                            </td>
                            <td><?=$PENSEATS['sseat_type_id']?></td>
                            <td><?=$PENSEATS['bpickup_location']?></td>
                            <td><?=$PENSEATS['bdropoff_location']?></td>
                            <td>
                                <form action="" method="POST">

                                    <input type="hidden" name="Abid" value="<?=$PENSEATS['booking_id']?>">
                                    <input type="hidden" name="Asid" value="<?=$PENSEATS['seatrate_id']?>">
                                    <div style="display:inline-block;">
                                        <button type="submit" class="btn btn-primary" name="bapprove" <?php if ($USERBOOK == true) {
                        echo 'disabled';
                    }?>>Approve</button>
                                        <button type="submit" class="btn btn-danger" name="breject">Reject</button>
                                    </div>
                                </form>
                            </td>

                        </tr>
                        <?php
}
            }
            ?>
                        <!-- Add more rows for other passengers -->
                    </tbody>
                </table>
            </div>
            <?php
}
        ?>
        </div>
    </div>
    </div>

    <?php
}
}
?>
    </div>
    </div>


    <!-- LIST OF BOOKINGS -->
    <section class="section featured-car" id="featured-car">
        <div class="container">

            <ul class="featured-car-list">

                <?php
$sql = 'SELECT r.*, v.vimage, v.vtype, u.ufname, u.umname, u.ulname, u.user_id
                FROM route_tbl r
                INNER JOIN vehicle_tbl v
                ON r.rvehicle_id = v.vehicle_id
                INNER JOIN user_tbl u
                ON v.vuser_id = u.user_id
                WHERE r.rstatus = ?';

$RSTATUS = 'ACTIVE';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $RSTATUS);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result)) {
    while ($ROUTE_LIST = mysqli_fetch_assoc($result)) {
        $sql = "SELECT COUNT(s.seatrate_id) AS seat_count
        FROM route_tbl r
        INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
        WHERE r.route_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ROUTE_LIST['route_id']);
        mysqli_stmt_execute($stmt);
        $rs = mysqli_stmt_get_result($stmt);
        $NUMSEATS = mysqli_fetch_assoc($rs);

        ?>

                <li>
                    <div class="featured-car-card">

                        <figure class="card-banner">
                            <img src="<?=$ROUTE_LIST['vimage']?>" alt="Toyota RAV4 2021" loading="lazy" width="440"
                                height="300" class="w-100">
                        </figure>

                        <div class="card-content">

                            <div class="card-title-wrapper">
                                <h3 class="h3 card-title">
                                    <a><?=$ROUTE_LIST['rstart_point'] . " - " . $ROUTE_LIST['rend_point']?></a>
                                </h3>

                                <data class="year" value="2021"><?=$ROUTE_LIST['vtype']?></data>
                            </div>

                            <ul class="card-list">

                                <li class="card-list-item">
                                    <ion-icon name="people-outline"></ion-icon>

                                    <span class="card-item-text"><?=$NUMSEATS['seat_count']?> Seats</span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="calendar-outline"></ion-icon>

                                    <span
                                        class="card-item-text"><?=date('F j, Y', strtotime($ROUTE_LIST['rdate_time']))?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="people-outline"></ion-icon>

                                    <span class="card-item-text"
                                        onclick="openModal('driverinfo_<?=$ROUTE_LIST['route_id']?>')"><?=$ROUTE_LIST['ufname'] . "  " . $ROUTE_LIST['umname'] . "  " . $ROUTE_LIST['ulname']?></span>
                                </li>

                                <li class="card-list-item">
                                    <ion-icon name="time-outline"></ion-icon>

                                    <span
                                        class="card-item-text"><?=date('h:i A', strtotime($ROUTE_LIST['rdate_time']))?></span>
                                </li>



                                <!-- Modal -->
                                <?php
$AVE = 0;
        $sql3 = "SELECT AVG(f.frating) as 'AVERAGE_RATING'
FROM feedback_tbl f
INNER JOIN booking_tbl b ON f.fbooking_id = b.booking_id
INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
INNER JOIN route_tbl r ON s.sroute_id = r.route_id
INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
INNER JOIN user_tbl u ON v.vuser_id = u.user_id
WHERE u.user_id = ?";

        $stmt3 = mysqli_prepare($conn, $sql3);
        mysqli_stmt_bind_param($stmt3, "i", $ROUTE_LIST['user_id']);
        mysqli_stmt_execute($stmt3);
        $rs3 = mysqli_stmt_get_result($stmt3);
        $AVERAGE_RATING = mysqli_fetch_assoc($rs3);
        $AVE = $AVERAGE_RATING['AVERAGE_RATING'];
        $formatted_average = ($AVE - intval($AVE) == 0) ? intval($AVE) : number_format($AVE, 1);
        ?>
                                <div id="driverinfo_<?=$ROUTE_LIST['route_id']?>" class="modal">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="reviewModalLabel">Driver Ratings and
                                                    Comments</h5>
                                                <button type="button" class="btn-close"
                                                    onclick="closeModal('driverinfo_<?=$ROUTE_LIST['route_id']?>')"></button>
                                            </div>
                                            <h6><?=$ROUTE_LIST['ufname'] . "  " . $ROUTE_LIST['umname'] . "  " . $ROUTE_LIST['ulname']?>
                                            </h6>
                                            <h6>Average Rating: <?=$formatted_average;?></h6>
                                            <div class="modal-body">
                                                <!-- Add the driver ratings and comments content here -->
                                                <?php
$sql2 = "SELECT f.frating, f.fcomment
FROM feedback_tbl f
INNER JOIN booking_tbl b ON f.fbooking_id = b.booking_id
INNER JOIN seatrate_tbl s ON b.bseatrate_id = s.seatrate_id
INNER JOIN route_tbl r ON s.sroute_id = r.route_id
INNER JOIN vehicle_tbl v ON r.rvehicle_id = v.vehicle_id
INNER JOIN user_tbl u ON v.vuser_id = u.user_id
WHERE u.user_id = ?";

        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $ROUTE_LIST['user_id']);
        mysqli_stmt_execute($stmt2);
        $rs2 = mysqli_stmt_get_result($stmt2);
        if (mysqli_num_rows($rs2)) {
            $counter = 0; // Initialize the counter
            while ($FEEDBACKINFO = mysqli_fetch_assoc($rs2)) {

                ?>
                                                <div class="driver-ratings">

                                                    <div class="rating">
                                                        <span class="star"><?=$FEEDBACKINFO['frating']?>&#9733;</span>
                                                    </div>
                                                    <p class="comment"><?=$FEEDBACKINFO['fcomment']?></p>
                                                    <!-- Add more driver ratings and comments if needed -->
                                                </div>
                                                <?php
}
        }
        ?>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        onclick="closeModal('driverinfo_<?=$ROUTE_LIST['route_id']?>')">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <li class="card-list-item">
                                        <ion-icon name="time-outline"></ion-icon>

                                        <span
                                            class="card-item-text"><?=date('h:i A', strtotime($ROUTE_LIST['rdate_time']))?></span>
                                    </li>

                            </ul>
                            <div class="card-price-wrapper">
                                <?php if ($HASROUTE == false) {?>
                                <button class="btn btn-primary text-center"
                                    onclick="openModal('seatmodal_<?=$ROUTE_LIST['route_id']?>')" <?php
$sql2 = "SELECT r.route_id FROM route_tbl r
INNER JOIN seatrate_tbl s ON r.route_id = s.sroute_id
INNER JOIN booking_tbl b ON s.seatrate_id = b.bseatrate_id
INNER JOIN user_tbl u ON b.buser_id = u.user_id
WHERE r.route_id = ? AND b.bbooking_status = ? AND u.user_id = ?";

            $BPENDING = 'PENDING';

            $stmt2 = mysqli_prepare($conn, $sql2);
            mysqli_stmt_bind_param($stmt2, "isi", $ROUTE_LIST['route_id'], $BPENDING, $_SESSION['userID']);
            mysqli_stmt_execute($stmt2);
            $rs2 = mysqli_stmt_get_result($stmt2);

            if (mysqli_num_rows($rs2) > 0) {
                echo 'disabled';
            }
            ?>>Book now</button>
                                <?php }
        ?>
                            </div>

                            <!-- Modal -->
                            <div id="seatmodal_<?=$ROUTE_LIST['route_id']?>" class="modal">
                                <div class="modal-content">
                                    <span class="close"
                                        onclick="closeModal('seatmodal_<?=$ROUTE_LIST['route_id']?>')">&times;</span>
                                    <h1>Book your Seat here!</h1>
                                    <form action="" method="POST">
                                        <div class="form-group row">
                                            <label for="pickup" class="col-sm-3 col-form-label">Pick up point:</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="pickupl" id="pickup"
                                                    required><br>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dropoff" class="col-sm-3 col-form-label">Drop off point:</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="dropoffl" id="dropoff"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="seats">Seats:</label>
                                            <?php
$sqlseat = 'SELECT * FROM seatrate_tbl WHERE sroute_id = ?';

        $stmtseat = mysqli_prepare($conn, $sqlseat);
        mysqli_stmt_bind_param($stmtseat, "s", $ROUTE_LIST['route_id']);
        mysqli_stmt_execute($stmtseat);
        $resultseat = mysqli_stmt_get_result($stmtseat);
        $SEATCOUNTER = 1;

        if (mysqli_num_rows($resultseat)) {
            while ($SEAT_LIST = mysqli_fetch_assoc($resultseat)) {

                ?>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="selseat"
                                                    value="<?=$SEAT_LIST['seatrate_id']?>" <?php if ($SEAT_LIST['sstatus'] == 'TAKEN') {
                    echo 'disabled';
                }?> required>

                                                <label class="form-check-label" for="seat<?=$SEATCOUNTER?>"><?php
if ($SEAT_LIST['sseat_type_id'] == 1) {
                    echo 'Front Seat';
                } else if ($SEAT_LIST['sseat_type_id'] == 2) {
                    echo 'Left Seat';
                } else if ($SEAT_LIST['sseat_type_id'] == 3) {
                    echo 'Middle Seat';
                } else if ($SEAT_LIST['sseat_type_id'] == 4) {
                    echo 'Right Seat';
                }
                ?> -



                                                    <?php
if ($SEAT_LIST['sstatus'] == 'TAKEN') {
                    echo 'TAKEN';
                } else {
                    echo $SEAT_LIST['sprice'];
                }

                ?></label>
                                            </div>
                                            <?php
}
        }
        ?>
                                        </div>
                                        <div class="button-group text-center">
                                            <input type="hidden" name="rtime" value="<?=$ROUTE_LIST['rdate_time']?>">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                name="seatbook">Book</button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="closeModal('seatmodal_<?=$ROUTE_LIST['route_id']?>')">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                </li>
                <?php

    }
}
?>


            </ul>

        </div>
        </div>
    </section>


</body>

</html>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script>
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "block";
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "none";
}


// Get the checkbox elements
const seat1Checkbox = document.getElementById('seat1');
const seat2Checkbox = document.getElementById('seat2');
const seat3Checkbox = document.getElementById('seat3');
const seat4Checkbox = document.getElementById('seat4');

// Get the seat input fields
const seat1Input = document.getElementById('seat1-input');
const seat2Input = document.getElementById('seat2-input');
const seat3Input = document.getElementById('seat3-input');
const seat4Input = document.getElementById('seat4-input');

// Disable the seat input fields by default
seat1Input.disabled = true;
seat2Input.disabled = true;
seat3Input.disabled = true;
seat4Input.disabled = true;

// Enable or disable the seat input fields based on checkbox status
seat1Checkbox.addEventListener('change', function() {
    seat1Input.disabled = !this.checked;
    if (!this.checked) {
        seat1Input.value = '';
    }
});

seat2Checkbox.addEventListener('change', function() {
    seat2Input.disabled = !this.checked;
    if (!this.checked) {
        seat2Input.value = '';
    }
});

seat3Checkbox.addEventListener('change', function() {
    seat3Input.disabled = !this.checked;
    if (!this.checked) {
        seat3Input.value = '';
    }
});

seat4Checkbox.addEventListener('change', function() {
    seat4Input.disabled = !this.checked;
    if (!this.checked) {
        seat4Input.value = '';
    }
});


function validateSeats() {
    // Get the checkbox inputs
    var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="seat"]');

    // Check if at least one checkbox is checked
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            return true; // At least one seat is selected, allow form submission
        }
    }

    // No seat selected, show an alert message
    alert("Please select at least one seat.");
    return false; // Prevent form submission
}
</script>
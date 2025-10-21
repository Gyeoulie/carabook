<?php
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    header("Location: /");
    exit;
}


$conn = mysqli_connect('localhost', 'root', '', 'carpool_db');
if (!$conn) {
    echo 'Connection Failed!';

}

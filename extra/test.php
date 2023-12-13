<?php
include('bookmark_fns.php');
session_start();

$username = $_SESSION['valid_user'];

    $conn = db_connect();

     $conn->query("SELECT passwd FROM user WHERE username = '$username'");
?>

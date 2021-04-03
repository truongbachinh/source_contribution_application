<?php
$conn = mysqli_connect("localhost", "root", "1234", "contribution_application_db", 8889);
$db_selected = mysqli_select_db($conn, "contribution_application_db");

if ($conn->connect_error) {
    die("Connect DB failed" . $conn->connect_error);
}

//echo "Connected successfully";
function formatDate($date){
    return date('g:i a',strtotime($date));
}

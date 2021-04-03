<?php
$conn = mysqli_connect("localhost", "root", "1234", "contribution_application", 8889);
$db_selected = mysqli_select_db($conn, "contribution_application");

if ($conn->connect_error) {
    die("Connect DB failed" . $conn->connect_error);
}

//echo "Connected successfully";
function formatDate($date){
    return date('g:i a',strtotime($date));
}

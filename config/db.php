<?php 
    $serverhost = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'php_auth_project';
    $conn = mysqli_connect($serverhost,$username,$password,$dbname);
    if(!$conn){
        mysqli_close($conn);    
        die("Database connection failed: " . mysqli_connect_error());
    }
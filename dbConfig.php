<?php

    $dbHost = "localhost";
    $dbUsername = "devinacho";
    $dbPassword = "";
    $dbName = "test";

    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    if($db->connect_error){
        die("Connection unsuccesful: " . $db->connect_error);
    }
?>
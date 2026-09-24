<?php

$host = "sql301.infinityfree.com";
$dbname = "if0_42938133_kashmir";
$username = "if0_42938133";
$password = "Javed13676";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Database connection failed.");

}

?>
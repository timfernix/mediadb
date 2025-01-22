<?php
$host = "";
$username = "";
$password = "";
$dbname = "";
$port = ; 

function connectToDatabase($host, $username, $password, $dbname, $port) {
    $conn = new mysqli($host, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if (!$conn->ssl_set(NULL, NULL, 'ca.pem', NULL, NULL)) {
        die("Failed to set SSL: " . $conn->error);
    }
    if (!$conn->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
        die("SSL connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function fetchMediaItems($conn) {
    $sql = "SELECT * FROM mediadb";
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

$conn = connectToDatabase($host, $username, $password, $dbname, $port);
$data = fetchMediaItems($conn);
echo json_encode($data);
$conn->close();
?>

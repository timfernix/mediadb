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

function searchMediaItems($conn, $query) {
    $sql = "SELECT * FROM mediadb WHERE title LIKE ? OR originTitle LIKE ? OR genre LIKE ? OR director LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeQuery = '%' . $query . '%';
    $stmt->bind_param("ssss", $likeQuery, $likeQuery, $likeQuery, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$conn = connectToDatabase($host, $username, $password, $dbname, $port);
$data = searchMediaItems($conn, $query);
echo json_encode($data);
$conn->close();
?>

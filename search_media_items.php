<?php
$host = ""; 
$username = ""; 
$password = ""; 
$dbname = "";
$port = ; 

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->ssl_set(NULL, NULL, 'ca.pem', NULL, NULL);

if (!$conn->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("SSL connection failed: " . mysqli_connect_error());
}

$query = isset($_GET['query']) ? $_GET['query'] : '';

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

echo json_encode($data);

$stmt->close();
$conn->close();
?>

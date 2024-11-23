<?php
$conn = new mysqli("", "", "", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $_GET['query'];
$sql = "SELECT * FROM mediadb WHERE title LIKE '%$query%' OR originTitle LIKE '%$query%' OR genre LIKE '%$query%' OR director LIKE '%$query%'";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>

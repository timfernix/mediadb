<?php
$conn = new mysqli("", "", "", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM mediadb";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>

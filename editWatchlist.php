<?php
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
} else {
    echo "Error: No ID provided";
    exit;
}

$conn = new mysqli("", "", "", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM mediadb WHERE id = $itemId";
$result = $conn->query($sql);
$item = $result->fetch_assoc();

?>
<head>
    <style>
.dark-mode {
  background-color: #333;
  color: #fff;
  font-family: Arial, sans-serif;
}

.dark-mode a {
  color: #66d9ef;
  text-decoration: none;
}

.dark-mode a:hover {
  color: #9fdefe;
}

.dark-mode form {
  max-width: 500px;
  margin: 40px auto;
  padding: 20px;
  background-color: #444;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.dark-mode label {
  display: block;
  margin-bottom: 10px;
}

.dark-mode input[type="text"],
.dark-mode input[type="date"],
.dark-mode select {
  width: 100%;
  height: 40px;
  margin-bottom: 20px;
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: #555;
  color: #fff;
}

.dark-mode input[type="checkbox"] {
  margin-bottom: 20px;
}

.dark-mode input[type="submit"] {
  width: 100%;
  height: 40px;
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: #66d9ef;
  color: #333;
  cursor: pointer;
}

.dark-mode input[type="submit"]:hover {
  background-color: #9fdefe;
}
    </style>
</head>
<body class="dark-mode">

<form action="update_item.php" method="post">
    <label for="watchlist">Watchlist:</label>
    <input type="checkbox" id="watchlist" name="watchlist" <?php echo $item['watchlist'] ? 'checked' : ''; ?>><br><br>

    <input type="hidden" name="id" value="<?php echo $itemId; ?>">
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($item['type']); ?>">
    <input type="hidden" name="title" value="<?php echo htmlspecialchars($item['title']); ?>">
    <input type="hidden" name="originTitle" value="<?php echo htmlspecialchars($item['originTitle']); ?>">
    <input type="hidden" name="coverUrl" value="<?php echo htmlspecialchars($item['coverUrl']); ?>">
    <input type="hidden" name="releaseDate" value="<?php echo htmlspecialchars($item['releaseDate']); ?>">
    <input type="hidden" name="director" value="<?php echo htmlspecialchars($item['director']); ?>">
    <input type="hidden" name="genre" value="<?php echo htmlspecialchars($item['genre']); ?>">
    <input type="hidden" name="notes" value="<?php echo htmlspecialchars($item['notes']); ?>">
    <input type="hidden" name="currentlyWatching" value="<?php echo htmlspecialchars($item['currentlyWatching']); ?>">
    <input type="hidden" name="length" value="<?php echo htmlspecialchars($item['length']); ?>">
    <input type="hidden" name="favorite" value="<?php echo htmlspecialchars($item['favorite']); ?>">

    <input type="submit" value="Update Item">
</form>
</body>

<?php
$conn->close();
?>

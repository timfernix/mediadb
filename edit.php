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
    <label for="type">Rating:</label>
      <select id="type" name="type">
          <option value="<?php echo $item['type']; ?>"><?php echo $item['type']; ?></option>
          <option value="Movie">Movie</option>
          <option value="Series">Series/Show</option>
          <option value="Game">Game</option>
        </select><br><br>

    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo $item['title']; ?>"><br><br>

    <label for="originTitle">Original title:</label>
    <input type="text" id="originTitle" name="originTitle" value="<?php echo $item['originTitle']; ?>"><br><br>

    <label for="coverUrl">Cover URL:</label>
    <input type="text" id="coverUrl" name="coverUrl" value="<?php echo $item['coverUrl']; ?>"><br><br>

    <label for="releaseDate">Release Date:</label>
    <input type="date" id="releaseDate" name="releaseDate" value="<?php echo $item['releaseDate']; ?>"><br><br>

    <label for="director">Director/Author:</label>
    <input type="text" id="director" name="director" value="<?php echo $item['director']; ?>"><br><br>

    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre" value="<?php echo $item['genre']; ?>"><br><br>

    <label for="length">Length:</label>
    <input type="text" id="length" name="length" value="<?php echo $item['length']; ?>"><br><br>
    
    <label for="currentlyWatching">Currently Watching:</label>
    <input type="checkbox" id="currentlyWatching" name="currentlyWatching" <?php echo $item['currentlyWatching'] ? 'checked' : ''; ?>><br><br>
    
    <label for="favorite">Favorite:</label>
    <input type="checkbox" id="favorite" name="favorite" <?php echo $item['favorite'] ? 'checked' : ''; ?>><br><br>
    
    <label for="watchlist">Watchlist:</label>
    <input type="checkbox" id="watchlist" name="watchlist" <?php echo $item['watchlist'] ? 'checked' : ''; ?>><br><br>

    <label for="notes">Notes:</label>
    <input type="text" id="notes" name="notes" value="<?php echo $item['notes']; ?>"><br><br>

    <input type="hidden" name="id" value="<?php echo $itemId; ?>">
    <input type="submit" value="Update Item">
</form>
</body>

<?php
$conn->close();
?>

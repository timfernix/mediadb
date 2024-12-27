<?php
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
} else {
    echo "Error: No ID provided";
    exit;
}

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

$sql = "SELECT * FROM mediadb WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId); 
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        body {
            background-color: #1a1a1a; 
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #2a2a2a; 
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            max-width: 500px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="checkbox"] {
            width: calc(100% - 20px); 
            height: 40px;
            margin-bottom: 15px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #444;
            color: #fff;
            transition: background-color 0.3s;
            box-sizing: border-box; 
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            background-color: #555;
            outline: none;
        }

        input[type="checkbox"] {
            margin-bottom: 15px;
            width: auto; 
        }

        input[type="submit"] {
            width: 100%;
            height: 40px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #66d9ef;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #9fdefe;
        }
    </style>
</head>
<body>

<form action="update_item.php" method="post">
    <h2>Update Item</h2>
    
    <label for="watchlist">Watchlist:</label>
    <input type="checkbox" id="watchlist" name="watchlist" <?php echo $item['watchlist'] ? 'checked' : ''; ?>>

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
</html>

<?php
$conn->close();
?>

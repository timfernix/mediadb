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

function getItem($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM mediadb WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    return $item;
}

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    $item = getItem($conn, $itemId);
} else {
    die("No item ID provided.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #222222;
            color: #fff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            animation: slideInUp 0.5s ease-in-out;
            box-sizing: border-box;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .row>div {
            flex: 1;
            margin-right: 10px;
        }

        .row>div:last-child {
            margin-right: 0;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            background-color: #3a3a3a;
            color: #fff;
            border-radius: 5px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .checkbox-label {
            display: inline-block;
            margin-right: 10px;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="update_item.php" method="post">
            <h2>Edit Item</h2>

            <div class="row">
                <div>
                    <label for="type">Type:</label>
                    <select id="type" name="type">
                        <option value="<?php echo htmlspecialchars($item['type']); ?>"><?php echo htmlspecialchars($item['type']); ?></option>
                        <option value="Movie">Movie</option>
                        <option value="Series">Series/Show</option>
                        <option value="Game">Game</option>
                    </select>
                </div>
                <div>
                    <label for="coverUrl">Cover URL:</label>
                    <input type="text" id="coverUrl" name="coverUrl" value="<?php echo htmlspecialchars($item['coverUrl']); ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                </div>
                <div>
                    <label for="originTitle">Original title:</label>
                    <input type="text" id="originTitle" name="originTitle" value="<?php echo htmlspecialchars($item['originTitle']); ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="releaseDate">Release Date:</label>
                    <input type="date" id="releaseDate" name="releaseDate" value="<?php echo htmlspecialchars($item['releaseDate']); ?>">
                </div>
                <div>
                    <label for="director">Director/Author:</label>
                    <input type="text" id="director" name="director" value="<?php echo isset($item['director']) ? htmlspecialchars($item['director']) : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="genre">Genre:</label>
                    <input type="text" id="genre" name="genre" value="<?php echo isset($item['genre']) ? htmlspecialchars($item['genre']) : ''; ?>">
                </div>
                <div>
                    <label for="length">Length:</label>
                    <input type="text" id="length" name="length" value="<?php echo isset($item['length']) ? htmlspecialchars($item['length']) : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes"><?php echo isset($item['notes']) ? htmlspecialchars($item['notes']) : ''; ?></textarea>
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="watchlist" class="checkbox-label">Watchlist:</label>
                    <input type="checkbox" id="watchlist" name="watchlist" <?php echo isset($item['watchlist']) && $item['watchlist'] ? 'checked' : ''; ?>>
                    <label for="currentlyWatching" class="checkbox-label">Currently Watching:</label>
                    <input type="checkbox" id="currentlyWatching" name="currentlyWatching" <?php echo isset($item['currentlyWatching']) && $item['currentlyWatching'] ? 'checked' : ''; ?>>
                    <label for="favorite" class="checkbox-label">Favorite:</label>
                    <input type="checkbox" id="favorite" name="favorite" <?php echo isset($item['favorite']) && $item['favorite'] ? 'checked' : ''; ?>>
                </div>
                <div>
                    <label for="timesWatched">Times Watched:</label>
                    <input type="number" id="timesWatched" name="timesWatched" value="<?php echo isset($item['timeswatched']) ? htmlspecialchars($item['timeswatched']) : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="lastEdited">Last Edited:</label>
                    <input type="date" id="lastEdited" name="lastEdited" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label for="duration">Duration:</label>
                    <input type="number" id="duration" name="duration" value="<?php echo isset($item['duration']) ? htmlspecialchars($item['duration']) : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="seasons">Seasons:</label>
                    <input type="number" id="seasons" name="seasons" value="<?php echo isset($item['seasons']) ? htmlspecialchars($item['seasons']) : ''; ?>">
                </div>
                <div>
                    <label for="episodesPerSeason">Episodes per Season:</label>
                    <input type="text" id="episodesPerSeason" name="episodesPerSeason" value="<?php echo isset($item['episodesperseason']) ? htmlspecialchars($item['episodesperseason']) : ''; ?>">
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo isset($item['id']) ? htmlspecialchars($item['id']) : ''; ?>">
            <input type="submit" value="Update Item">
        </form>
    </div>
</body>

</html>

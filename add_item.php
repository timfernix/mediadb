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

if (!$conn->ssl_set(NULL, NULL, 'ca.pem', NULL, NULL)) {
    die("Failed to set SSL: " . $conn->error);
}

if (!$conn->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("SSL connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = htmlspecialchars($_POST["type"] ?? '');
    $title = htmlspecialchars($_POST["title"] ?? '');
    $originTitle = htmlspecialchars($_POST["originTitle"] ?? '');
    $coverUrl = htmlspecialchars($_POST["coverUrl"] ?? '');
    $releaseDate = htmlspecialchars($_POST["releaseDate"] ?? '');
    $director = htmlspecialchars($_POST["director"] ?? '');
    $genre = htmlspecialchars($_POST["genre"] ?? '');
    $notes = htmlspecialchars($_POST["notes"] ?? '');
    $currentlyWatching = isset($_POST["currentlyWatching"]) ? 1 : 0;
    $length = htmlspecialchars($_POST["length"] ?? '');
    $favorite = isset($_POST["favorite"]) ? 1 : 0;
    $watchlist = isset($_POST["watchlist"]) ? 1 : 0;
    $timesWatched = htmlspecialchars($_POST["timesWatched"] ?? NULL);
    $lastEdited = date("Y-m-d H:i:s"); 
    $duration = htmlspecialchars($_POST["duration"] ?? NULL);
    $seasons = htmlspecialchars($_POST["seasons"] ?? NULL);
    $episodesPerSeason = htmlspecialchars($_POST["episodesPerSeason"] ?? NULL);

    $timesWatched = $timesWatched === '' ? 1 : $timesWatched;
    $duration = $duration === '' ? NULL : $duration;
    $seasons = $seasons === '' ? NULL : $seasons;
    $episodesPerSeason = $episodesPerSeason === '' ? NULL : $episodesPerSeason;

    $stmtCheckTitle = $conn->prepare("SELECT COUNT(*) FROM mediadb WHERE title = ?");
    $stmtCheckTitle->bind_param("s", $title);
    $stmtCheckTitle->execute();
    $stmtCheckTitle->bind_result($count);
    $stmtCheckTitle->fetch();
    $stmtCheckTitle->close();

    if ($count > 0) {
        $message = "Error: The title already exists in the database.";
    } else {
        $sql = "INSERT INTO mediadb (type, title, originTitle, coverUrl, releaseDate, director, genre, notes, currentlyWatching, length, favorite, watchlist, timesWatched, lastEdited, duration, seasons, episodesPerSeason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssssssssssss", $type, $title, $originTitle, $coverUrl, $releaseDate, $director, $genre, $notes, $currentlyWatching, $length, $favorite, $watchlist, $timesWatched, $lastEdited, $duration, $seasons, $episodesPerSeason);
            if ($stmt->execute() === TRUE) {
                echo "Item added successfully!";
                echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>"; 
            } else {
                echo "Error adding item: " . $conn->error;
                echo "<script>setTimeout(function(){ window.close(); }, 10000);</script>"; 
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}

$conn->close();
?>

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
    .message {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        background-color: #333; 
        color: #fff; 
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    .message.show {
        opacity: 1;
    }
    p {
        color: #aaa;
        font-size: 12px;
    }
    @keyframes fadeOut {
        0% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }
</style>

<?php if (isset($message)) { ?>
    <div class="message" id="message"><?= $message ?></div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var message = document.getElementById("message");
            message.classList.add("show");
            setTimeout(function() {
                message.classList.remove("show");
            }, 3000);
        });
    </script>
<?php } ?>

<body>
    <div class="container">
        <form action="" method="post">
            <h2>Add New Item</h2>

            <div class="row">
                <div>
                    <label for="type">Type:</label>
                    <select id="type" name="type">
                        <option value="" disabled selected>Select a type</option>
                        <option value="Movie">Movie</option>
                        <option value="Series">Series/Show</option>
                        <option value="Game">Game</option>
                    </select>
                </div>
                <div>
                    <label for="coverUrl">Cover URL:</label>
                    <input type="text" id="coverUrl" name="coverUrl">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div>
                    <label for="originTitle">Original title:</label>
                    <input type="text" id="originTitle" name="originTitle">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="releaseDate">Release Date:</label>
                    <input type="date" id="releaseDate" name="releaseDate">
                </div>
                <div>
                    <label for="director">Director/Author:</label>
                    <input type="text" id="director" name="director">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="genre">Genre:</label>
                    <input type="text" id="genre" name="genre">
                </div>
                <div>
                    <label for="length">Length:</label>
                    <input type="text" id="length" name="length">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="watchlist" class="checkbox-label">Watchlist:</label>
                    <input type="checkbox" id="watchlist" name="watchlist">
                    <label for="currentlyWatching" class="checkbox-label">Currently Watching:</label>
                    <input type="checkbox" id="currentlyWatching" name="currentlyWatching">
                    <label for="favorite" class="checkbox-label">Favorite:</label>
                    <input type="checkbox" id="favorite" name="favorite">
                </div>
                <div>
                    <label for="timesWatched">Times Watched:</label>
                    <input type="number" id="timesWatched" name="timesWatched">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="lastEdited">Last Edited:</label>
                    <input type="date" id="lastEdited" name="lastEdited" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label for="duration">Duration:</label>
                    <input type="number" id="duration" name="duration">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="seasons">Seasons:</label>
                    <input type="number" id="seasons" name="seasons">
                </div>
                <div>
                    <label for="episodesPerSeason">Episodes per Season:</label>
                    <input type="text" id="episodesPerSeason" name="episodesPerSeason">
                </div>
            </div>

            <input type="hidden" name="id" value="">
            <input type="submit" value="Add Item">
        </form>
    </div>
</body>

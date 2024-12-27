<?php
$host = ""; 
$username = "";
$password = ""; 
$dbname = ""; 
$port = ;

// Create a new mysqli connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set SSL
$conn->ssl_set(NULL, NULL, 'ca.pem', NULL, NULL);
if (!$conn->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("SSL connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $type = $_POST["type"] ?? '';
    $title = $_POST["title"] ?? '';
    $originTitle = $_POST["originTitle"] ?? '';
    $coverUrl = $_POST["coverUrl"] ?? '';
    $releaseDate = $_POST["releaseDate"] ?? '';
    $director = $_POST["director"] ?? '';
    $genre = $_POST["genre"] ?? '';
    $notes = $_POST["notes"] ?? '';
    $currentlyWatching = isset($_POST["currentlyWatching"]) ? 1 : 0;
    $length = $_POST["length"] ?? '';
    $favorite = isset($_POST["favorite"]) ? 1 : 0;
    $watchlist = isset($_POST["watchlist"]) ? 1 : 0;

    // Check if title already exists
    $stmtCheckTitle = $conn->prepare("SELECT COUNT(*) FROM mediadb WHERE title = ?");
    $stmtCheckTitle->bind_param("s", $title);
    $stmtCheckTitle->execute();
    $stmtCheckTitle->bind_result($count);
    $stmtCheckTitle->fetch();
    $stmtCheckTitle->close();

    if ($count > 0) {
        echo "Error: The title already exists in the database.";
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>";
    } else {
        // Insert new record
        $sql = "INSERT INTO mediadb (type, title, originTitle, coverUrl, releaseDate, director, genre, notes, currentlyWatching, length, favorite, watchlist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $type, $title, $originTitle, $coverUrl, $releaseDate, $director, $genre, $notes, $currentlyWatching, $length, $favorite, $watchlist);
        
        if ($stmt->execute()) {
            echo "Item added successfully!";
        } else {
            echo "Error updating item: " . $stmt->error;
        }
        $stmt->close();
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>"; 
    }
}

$conn->close();
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #1a1a1a; 
        color: #e0e0e0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    form {
        max-width: 450px;
        width: 100%;
        background-color: #2a2a2a; 
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
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
        animation: fadeOut 3s forwards;
        background-color: #333; 
        color: #fff; 
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
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
    <div class="message"><?= $message ?></div>
<?php } ?>

<form action="" method="post">
    <h2>Add Media</h2>
    <label for="type">Type:</label>
    <select id="type" name="type" required>
        <option value="" disabled selected>Select a type</option>
        <option value="Movie">Movie</option>
        <option value="Series">Series/Show</option>
        <option value="Game">Game</option>
    </select>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    <label for="originTitle">Original title:</label>
    <input type="text" id="originTitle" name="originTitle">
    <label for="coverUrl">Cover URL:</label>
    <input type="text" id="coverUrl" name="coverUrl">
    <label for="releaseDate">Release Date:</label>
    <input type="date" id="releaseDate" name="releaseDate" required>
    <label for="director">Director/Author/Source:</label>
    <input type="text" id="director" name="director">
    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre">
    <label for="length">Length:</label> <p>seasons (episodes) / hours minutes</p>
    <input type="text" id="length" name="length">
    <label for="notes">Notes:</label>
    <textarea id="notes" name="notes"></textarea>
    <label for="currentlyWatching">Currently Watching:</label>
    <input type="checkbox" id="currentlyWatching" name="currentlyWatching">
    <label for="favorite">Favorite:</label>
    <input type="checkbox" id="favorite" name="favorite">
    <label for="watchlist">Watchlist:</label>
    <input type="checkbox" id="watchlist" name="watchlist">
    <input type="submit" name="submit" value="Add Item">
</form>

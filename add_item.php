<?php
$conn = new mysqli(hostname:"", username:"", password: "", database: "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $title = $_POST["title"];
    $originTitle = $_POST["originTitle"];
    $coverUrl = $_POST["coverUrl"];
    if (strpos($coverUrl, "./cover/") !== 0) {
        $coverUrl = "./cover/" . $coverUrl;
    }
    $releaseDate = $_POST["releaseDate"];
    $director = $_POST["director"];
    $genre = $_POST["genre"];
    $notes = $_POST["notes"];
    $currentlyWatching = isset($_POST["currentlyWatching"]) ? 1 : 0;
    $length = $_POST["length"];
    $favorite = isset($_POST["favorite"]) ? 1 : 0;
    $watchlist = isset($_POST["watchlist"]) ? 1 : 0;

    $stmtCheckTitle = $conn->prepare("SELECT COUNT(*) FROM mediadb WHERE title = ?");
    $stmtCheckTitle->bind_param("s", $title);
    $stmtCheckTitle->execute();
    $result = $stmtCheckTitle->get_result();
    $row = $result->fetch_row();

    if ($row[0] > 0) {
        echo "Error: The title already exists in the database.";
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>";
    } else {
    
    $sql = "INSERT INTO mediadb (type, title, originTitle, coverUrl, releaseDate, director, genre, notes, currentlyWatching, length, favorite, watchlist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $type, $title, $originTitle, $coverUrl, $releaseDate, $director, $genre, $notes, $currentlyWatching, $length, $favorite, $watchlist);
    $result = $stmt->execute();

    if ($result === TRUE) {
        echo "Item added successfully!";
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>"; 
    } else {
        echo "Error updating item: " . $conn->error;
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>"; 
    }
}

$stmtCheckTitle->close();
$stmt->close();
$conn->close();
}
?>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 20px;
        background-color: #333; 
        color: #fff;
    }

    form {
        max-width: 400px;
        margin: 40px auto;
        padding: 20px;
        border: 1px solid #444; 
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
        display: inline-block;
        margin-bottom: 10px;
        color: #fff; 
    }

    input,
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #444; 
        background-color: #444; 
        color: #fff;
    }

    input[type="submit"] {
        background-color: #666; 
        color: #fff; 
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #555; 
    }

    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #444; 
        border-radius: 5px;
        font-size: 16px;
        font-family: Arial, sans-serif;
        background-color: #444; 
        color: #fff; 
    }

    select:focus {
        border-color: #aaa;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .message {
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        animation: fadeOut 3s forwards;
        background-color: #333; 
        color: #fff; 
        padding: 10px;
        border-radius: 5px;
    }

    p {
        color: #aaa;
        display: inline;
        font-size: 12;
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
    <div class="message" <?= $style ?>><?= $message ?></div>
<?php } ?>

<form action="" method="post">
    <h2>Add Media</h2>

    <label for="type">Type:</label>
    <select id="type" name="type">
        <option value="" disabled>Select a type</option>
        <option value="Movie">Movie</option>
        <option value="Series">Series/Show</option>
        <option value="Game">Game</option>
    </select><br><br>

    <label for="title">Title:</label>
    <input type="text" id="title" name="title"><br><br>
    
    <label for="originTitle">Original title:</label>
    <input type="text" id="originTitle" name="originTitle"><br><br>

    <label for="coverUrl">Cover URL:</label> <p>Local path: ./cover/</p>
    <input type="text" id="coverUrl" name="coverUrl"><br><br>

    <label for="releaseDate">Release Date:</label>
    <input type="date" id="releaseDate" name="releaseDate"><br><br>

    <label for="director">Director/Author/Source:</label>
    <input type="text" id="director" name="director"><br><br>

    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre"><br><br>

    <label for="length">Length:</label> <p> seasons (episodes) / hours minutes</p>
    <input type="text" id="length" name="length"><br><br>

    <label for="notes">Notes:</label>
    <textarea id="notes" name="notes"></textarea><br><br>

    <label for="currentlyWatching">Currently Watching:</label>
    <input type="checkbox" id="currentlyWatching" name="currentlyWatching"><br><br>

    <label for="favorite">Favorite:</label>
    <input type="checkbox" id="favorite" name="favorite"><br><br>

    <label for="watchlist">Watchlist:</label>
    <input type="checkbox" id="watchlist" name="watchlist"><br><br>

    <input type="submit" name="submit" value="Add Item">
</form>

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

$type = $_POST['type'];
$itemId = $_POST['id'];
$title = $_POST['title'];
$originTitle = $_POST['originTitle'];
$coverUrl = $_POST['coverUrl'];
$releaseDate = $_POST['releaseDate'];
$director = $_POST['director'];
$genre = $_POST['genre'];
$notes = $_POST['notes'];
$currentlyWatching = isset($_POST["currentlyWatching"]) ? 1 : 0;
$length = $_POST['length'];
$favorite = isset($_POST["favorite"]) ? 1 : 0;
$watchlist = isset($_POST["watchlist"]) ? 1 : 0;
$lastEdited = $_POST['lastEdited'];
$timesWatched = $_POST['timesWatched']; 
$duration = $_POST['duration'];
$seasons = $_POST['seasons'];
$episodesPerSeason = $_POST['episodesPerSeason'];

$timesWatched = $timesWatched === '' ? NULL : $timesWatched;
$duration = $duration === '' ? NULL : $duration;
$seasons = $seasons === '' ? NULL : $seasons;
$episodesPerSeason = $episodesPerSeason === '' ? NULL : $episodesPerSeason;


$sql = "UPDATE mediadb SET 
        type = ?,
        title = ?,
        originTitle = ?,
        coverUrl = ?, 
        releaseDate = ?, 
        director = ?, 
        genre = ?, 
        notes = ?,
        currentlyWatching = ?,
        length = ?,
        favorite = ?,
        watchlist = ?,
        timeswatched = ?,
        lastedited = ?,
        duration = ?,
        seasons = ?,
        episodesperseason = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssssssssssssssi",
    $type,
    $title,
    $originTitle,
    $coverUrl,
    $releaseDate,
    $director,
    $genre,
    $notes,
    $currentlyWatching,
    $length,
    $favorite,
    $watchlist,
    $timesWatched,
    $lastEdited,
    $duration,
    $seasons,
    $episodesPerSeason,
    $itemId
);

if ($stmt->execute() === TRUE) {
    echo "Item updated successfully!";
    echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>"; 
} else {
    echo "Error updating item: " . $conn->error;
    echo "<script>setTimeout(function(){ window.close(); }, 10000);</script>"; 
}

$stmt->close();
$conn->close();
?>
?>

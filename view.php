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

$sql = "SELECT *, favorite, currentlyWatching FROM mediadb WHERE id = ?";
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
    <title><?= htmlspecialchars($item['title']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .background-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?= htmlspecialchars($item['coverUrl']) ?>');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 20px;
            background-color: rgba(34, 34, 34, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: stretch; /* Stretch items to fill the container */
            gap: 20px; /* Space between image and text */
        }

        img {
            width: 300px;
            height: 450px;
            border-radius: 10px;
            object-fit: cover;
        }

        .text-container {
            flex: 1; /* Allow text container to take remaining space */
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            justify-content: space-between; /* Space out elements */
        }

        h1 {
            font-size: 36px;
            margin: 10px 0;
        }

        .currently-watching {
            color: #ff69b4;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center; /* Center the text */
        }

        p {
            font-size: 18px;
            margin: 5px 0;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #444;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 10px;
            z-index: 2;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #555;
        }
    </style>
</head>

<body>
    <div class="background-container"></div>
    <div class="container">
        <img src="<?= htmlspecialchars($item['coverUrl']) ?>" alt="<?= htmlspecialchars($item ['title']) ?> cover image">
        <div class="text-container">
            <div class="currently-watching">
                <?php if ($item['currentlyWatching'] == 1) { ?>
                    <p>✨ Currently Watching ✨</p>
                <?php } else if ($item['favorite'] == 1) { ?>
                    <p>❤️ Favorite ❤️</p>
                <?php } ?>
            </div>
            <h1>
                <?php 
                if ($item['type'] == "Movie") { 
                    echo "🎥 " . htmlspecialchars($item['title']); 
                } else if ($item['type'] == "Series") { 
                    echo "📺 " . htmlspecialchars($item['title']); 
                } else if ($item['type'] == "Game") { 
                    echo "🎮 " . htmlspecialchars($item['title']); 
                } 
                ?>
            </h1>
            <p>📢 <b>Original Title:</b> <?= htmlspecialchars($item['originTitle']) ?></p>
            <p>📆 <b>Release Date:</b> <?= htmlspecialchars($item['releaseDate']) ?></p>
            <p>🎭 <b>Genre:</b> <?= htmlspecialchars($item['genre']) ?></p>
            <p>🕒 <b>Length:</b> <?= htmlspecialchars($item['length']) ?></p>
            <p>👨 <b>Director:</b> <?= htmlspecialchars($item['director']) ?></p>
            <p>📑 <b>Note:</b> <?= htmlspecialchars($item['notes']) ?></p>
        </div>
    </div>
    <button class="back-button" onclick="window.close()">⬅ Back</button>
</body>
</html>

<?php
$conn->close();
?>

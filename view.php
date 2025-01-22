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

$sql = "SELECT *, favorite, currentlyWatching, timesWatched, lastEdited, duration, seasons, episodesPerSeason FROM mediadb WHERE id = ?";
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
            font-family: 'Poppins', sans-serif;
            background-color: #0f0f0f;
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
            background-image: linear-gradient(to bottom, rgba(15, 15, 15, 0.8), rgba(15, 15, 15, 0.9)), url('<?= htmlspecialchars($item['coverUrl']) ?>');
            background-size: cover;
            background-position: center;
            z-index: 1;
            animation: fadeIn 2s ease-in-out;
        }

        .container {
            position: relative;
            z-index: 2;
            max-width: 900px;
            width: 90%;
            padding: 30px;
            background-color: rgba(34, 34, 34, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            gap: 30px;
            animation: slideIn 1s ease-in-out;
        }

        img {
            width: 300px;
            height: 450px;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease;
        }

        img:hover {
            transform: scale(1.05);
        }

        .text-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        h1 {
            font-size: 42px;
            margin: 0;
            font-weight: 600;
            color: #ff69b4;
            text-shadow: 0 4px 10px rgba(255, 105, 180, 0.3);
        }

        /* Badge Styling */
        .badge {
            font-size: 18px;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .currently-watching {
            background-color: #ff69b4;
            color: #fff;
        }

        .favorite {
            background-color: #ffcc00;
            color: #000;
        }

        .watched {
            background-color: #4caf50;
            color: #fff;
        }

        p {
            font-size: 18px;
            margin: 5px 0;
            line-height: 1.5;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #444;
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 10px;
            z-index: 2;
            transition: background-color 0.3s, transform 0.3s;
        }

        .back-button:hover {
            background-color: #555;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="background-container"></div>
    <div class="container">
        <img src="<?= htmlspecialchars($item['coverUrl']) ?>" alt="<?= htmlspecialchars($item['title']) ?> cover image">
        <div class="text-container">
            <div class="badge">
                <?php if ($item['currentlyWatching'] == 1) { ?>
                    <span class="currently-watching">✨ Currently Watching ✨</span>
                <?php } else if ($item['favorite'] == 1) { ?>
                    <span class="favorite">❤️ Favorite ❤️</span>
                <?php } else { ?>
                    <span class="watched">✅ Watched/Played ✅</span>
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
            <p>🔂 <b>Watched:</b> <?= htmlspecialchars($item['timesWatched']) ?></p>
            <p>⌛ <b>Total time spent:</b>
                <?php
                if ($item['type'] == "Game") {
                    $totalMinutes = $item['duration'];
                } else if ($item['type'] == "Series") {
                    $episodesPerSeasonArray = explode(',', $item['episodesPerSeason']);
                    $totalEpisodes = array_sum($episodesPerSeasonArray);
                    $totalMinutes = $item['duration'] * $totalEpisodes * $item['timesWatched'];
                }
                else if ($item['type'] == "Movie") {
                    $totalMinutes = $item['duration'] * $item['timesWatched'];
                }

                $days = floor($totalMinutes / 1440);
                $hours = floor(($totalMinutes % 1440) / 60);
                $minutes = $totalMinutes % 60;

                echo htmlspecialchars($days) . " day(s), " . htmlspecialchars($hours) . " hour(s), " . htmlspecialchars($minutes) . " minute(s)";
                ?>
            </p>
            <p>📑 <b>Note:</b> <?= htmlspecialchars($item['notes']) ?></p>
            <p>✏️ <b>Last Edited:</b> <?= htmlspecialchars($item['lastEdited']) ?></p>
        </div>
    </div>
    <button class="back-button" onclick="window.close()">⬅ Back</button>
</body>

</html>

<?php
$conn->close();
?>

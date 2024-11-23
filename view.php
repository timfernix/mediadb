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

$sql = "SELECT *, favorite, currentlyWatching FROM mediadb WHERE id = $itemId";
$result = $conn->query($sql);
$item = $result->fetch_assoc();
?>

<html>

<head>
    <title><?= $item['title'] ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: black;
            color: #fff;
        }

        h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            display: inline-block;
        }

        img {
            width: 500px;
            height: 750px;
            border-radius: 10px;
            object-fit: cover;
            border-radius: 0.5em 0.5em 0.5em 0.5em;
        }

        .container {
            max-width: 800px;
            margin: 40px;
            padding: 20px;
            background-color: #444;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .currently-watching {
            color: #ff69b4;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #444;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 10px;
        }

        .back-button:hover {
            background-color: #555;
        }

        .background-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?= $item['coverUrl'] ?>');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
        }

        .wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: row;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="background-container"></div>
    <div class="wrapper">
        <img src="<?= $item['coverUrl'] ?>" alt="<?= $item['title'] ?> cover image">
        <div class="container">
            <div class="currently-watching">
                <?php if ($item['currentlyWatching'] == 1) { ?>
                    <p>✨ Currently Watching ✨</p>
                <?php } else if ($item['favorite'] == 1) { ?>
                    <p>❤️ Favorite ❤️</p>
                    <?php  } ?>
            </div>
            <?php if ($item['type'] == "Movie") { ?>
                <h1>🎥 </h1>
            <?php } else if ($item['type'] == "Series") { ?>
                <h1>📺 </h1>
            <?php } else if ($item['type'] == "Game") { ?>
                <h1>🎮 </h1>
            <?php } ?>

            <h1><?= $item['title'] ?></h1>
            <p>📢 <b>Original Title:</b> <?= $item['originTitle'] ?></p>
            <p>📆 <b>Release Date:</b> <?= $item['releaseDate'] ?></p>
            <p>🎭 <b>Genre:</b> <?= $item['genre'] ?></p>
            <p>🕒 <b>Length:</b> <?= $item['length'] ?></p>
            <p>👨 <b>Director:</b> <?= $item['director'] ?></p>
            <p>📑 <b>Note:</b> <?= $item['notes'] ?></p>
        </div>
    </div>
    <button class="back-button" onclick="window.close()">⬅ Back</button>
</body>

</html>

<?php
$conn->close();
?>

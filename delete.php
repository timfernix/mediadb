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

function showMessage($message) {
    echo "<div class='message'>$message</div>";
    echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>";
}

function showDeleteForm($itemId) {
    echo "<div class='container'>";
    echo "<h2>Delete Confirmation</h2>";
    echo "<form action='delete.php' method='post'>";
    echo "<p>Are you sure you want to delete item #$itemId? (Y/N)</p>";
    echo "<input type='hidden' name='id' value='$itemId'>";
    echo "<div class='button-group'>";
    echo "<input type='submit' name='confirm' value='Yes' class='btn btn-success'>";
    echo "<input type='submit' name='confirm' value='No' class='btn btn-danger'>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
}

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    showDeleteForm($itemId);
} elseif (isset($_POST['id'])) {
    $itemId = $_POST['id'];
    if (isset($_POST['confirm'])) {
        if ($_POST['confirm'] == 'Yes') {
            $sql = "DELETE FROM mediadb WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $itemId); 
            $result = $stmt->execute();

            if ($result) {
                showMessage("Item deleted successfully!");
            } else {
                showMessage("Error deleting item: " . $conn->error);
            }
            $stmt->close(); 
        } else {
            showMessage("Deletion cancelled.");
        }
    }
}

$conn->close();
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #1a1a1a; 
        color: #e0e0e0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background-color: #2a2a2a; 
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        text-align: center;
        max-width: 400px;
        width: 100%;
    }
    h2 {
        margin-bottom: 20px;
        color: #fff;
    }
    p {
        margin-bottom: 20px;
        color: #ccc;
    }
    .button-group {
        display: flex;
        justify-content: space-around;
    }
    input[type="submit"] {
        background-color: #007bff; 
        color: #fff; 
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    input[type="submit"].btn-danger {
        background-color: #dc3545; 
    }
    input[type="submit"]:hover {
        background-color: #0056b3; 
    }
    input[type="submit"].btn-danger:hover {
        background-color: #c82333; 
    }
    .message {
        margin-top: 20px;
        padding: 10px;
        border-radius: 5px;
        background-color: #333; 
        color: #fff; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5 );
    }
</style>

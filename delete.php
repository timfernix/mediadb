<?php
$conn = new mysqli("", "", "", "");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    echo "<form action='delete.php' method='post'>";
    echo "Are you sure you want to delete item #$itemId? (Y/N) ";
    echo "<input type='hidden' name='id' value='$itemId'>";
    echo "<input type='submit' name='confirm' value='Yes' class='btn btn-success'>";
    echo "<input type='submit' name='confirm' value='No' class='btn btn-danger'>";
    echo "</form>";
} elseif (isset($_POST['id'])) {
    $itemId = $_POST['id'];
    if (isset($_POST['confirm'])) {
        if ($_POST['confirm'] == 'Yes') {
            // Delete the item from the database
            $sql = "DELETE FROM mediadb WHERE id = $itemId";
            $result = $conn->query($sql);

            if ($result) {
                echo "Item deleted successfully!";
                echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>";
            } else {
                echo "Error deleting item: " . $conn->error;
                echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>";
            }
        } else {
            echo "Deletion cancelled.";
            echo "<script>setTimeout(function(){ window.close(); }, 1000);</script>";
        }
    }
}

$conn->close();
?>

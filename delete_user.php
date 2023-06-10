<?php
include_once("./database/constants.php");
if (!isset($_SESSION["userid"])) {
    header("location:".DOMAIN."/");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once("./database/db.php");
    $db = new Database();
    $conn = $db->connect();
    if ($conn === "DATABASE_CONNECTION_FAIL") {
        die("Database connection failed");
    }

    $userId = $_POST["id"];

    // Delete user from the database
    $sql = "DELETE FROM user WHERE id = '$userId'";
    if ($conn->query($sql) === TRUE) {
        echo "DELETED";
    } else {
        echo "ERROR";
    }

    $conn->close();
}
?>

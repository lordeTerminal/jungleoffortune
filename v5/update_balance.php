<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "root"; // Update with your database username
$password = "golimar10*"; // Update with your database password
$dbname = "fatec_cassino";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_balance = $_POST['new_balance'];

    // Update the user's balance
    $sql = "UPDATE users SET fatec_coins = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_balance, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'new_balance' => $new_balance]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update balance']);
    }

    $stmt->close();
}

$conn->close();
?>


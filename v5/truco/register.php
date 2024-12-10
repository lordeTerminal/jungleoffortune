<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



$servername = "db012024dvw3g6.mysql.dbaas.com.br";
$username = "db012024dvw3g6";
$password = "devWeb317@2024";
$dbname = "db012024dvw3g6";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$nome = $_POST['nome'];
$password_raw = $_POST['password'];

// Hash the password for security
$password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO usuarios (nome, password) VALUES (?, ?)");
$stmt->bind_param("ss", $nome, $password_hashed);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>


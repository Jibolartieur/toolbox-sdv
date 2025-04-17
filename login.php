<?php
session_start();

$host = "localhost";
$db = "NOM DB";
$user = "root";
$pass = "MDP DB";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Erreur de connexion MySQL: " . $conn->connect_error);
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$password_hashed = hash('sha256', $password);

$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password_hashed);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $_SESSION['username'] = $username; // Sauvegarde en session
  header("Location: cat.php");
  exit();
} else {
  echo "<script>alert('Identifiants incorrects'); window.location.href='index.html';</script>";
}

$stmt->close();
$conn->close();
?>

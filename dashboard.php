<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4">
    <h3>Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h3>
    <p>You have successfully logged in.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
</div>
</body>
</html>

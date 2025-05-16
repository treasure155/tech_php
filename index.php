<?php
session_start();
require 'db.php';
require 'mailer.php'; // Include mailer

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (isset($_POST['register'])) {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        if ($stmt->execute([$email, $password])) {
            // Send welcome email
            if (!sendWelcomeEmail($email, $email)) {
                $message = "Registered successfully, but failed to send welcome email.";
            } else {
                header("Location: thankyou.php");
                exit();
            }
        } else {
            $message = "Registration failed.";
        }
    } elseif (isset($_POST['login'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid login credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Auth System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-toggle {
      display: none;
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-12">
      <div class="card shadow p-4">
        <h4 class="text-center mb-3" id="formTitle">Login</h4>
        <?php if ($message): ?>
          <div class="alert alert-warning"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div id="registerToggle" class="form-toggle mb-3">
            <label>Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword">
          </div>
          <div class="d-grid">
            <button type="submit" name="login" class="btn btn-primary" id="loginBtn">Login</button>
            <button type="submit" name="register" class="btn btn-success form-toggle mt-2" id="registerBtn">Register</button>
          </div>
          <p class="mt-3 text-center">
            <a href="#" id="toggleForm">Don't have an account? Register</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const toggleLink = document.getElementById('toggleForm');
  const formTitle = document.getElementById('formTitle');
  const loginBtn = document.getElementById('loginBtn');
  const registerBtn = document.getElementById('registerBtn');
  const formToggles = document.querySelectorAll('.form-toggle');

  toggleLink.addEventListener('click', (e) => {
    e.preventDefault();
    formToggles.forEach(el => el.style.display = (el.style.display === 'none') ? 'block' : 'none');
    loginBtn.style.display = (loginBtn.style.display === 'none') ? 'block' : 'none';
    registerBtn.style.display = (registerBtn.style.display === 'none') ? 'block' : 'none';
    formTitle.textContent = (formTitle.textContent === 'Login') ? 'Register' : 'Login';
    toggleLink.textContent = (toggleLink.textContent.includes("Register")) ? "Already have an account? Login" : "Don't have an account? Register";
  });
</script>
</body>
</html>

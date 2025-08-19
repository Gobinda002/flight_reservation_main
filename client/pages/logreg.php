<?php
session_start();

// Include database connection
require_once '../../db.php';


$errors = [];
$success = '';
$showForm = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formType = $_POST['form_type'] ?? '';

  if ($formType === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if user exists in database
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
         $_SESSION['username'] = ucwords(strtolower($user['name']));   
        $_SESSION['user_id'] = $user['user_id']; 

        $success = "Login successful! Welcome, {$user['name']}.";
        header("Location: ../index.php");
        exit();
      } else {
        $errors[] = "Invalid username or password.";
      }
    } else {
      $errors[] = "Invalid username or password.";
    }
    $stmt->close();
    $showForm = 'login';
  }

  if ($formType === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if name already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $errors[] = "Name already taken.";
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $errors[] = "Email already registered.";
    }
    $stmt->close();

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email address.";
    }
    if (strlen($password) < 6) {
      $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
      // Hash password and insert user into database
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $email, $hashedPassword);

      if ($stmt->execute()) {
        $success = "Registration successful! You can now login.";
        $showForm = 'login';
      } else {
        $errors[] = "Registration failed. Please try again.";
        $showForm = 'register';
      }
      $stmt->close();
    } else {
      $showForm = 'register';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login & Register</title>
  <style>
    /* your existing CSS here */
    /* ... */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 25px 30px;
      border-radius: 10px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
      width: 320px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
    }

    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    input:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      width: 100%;
      padding: 10px;
      border: none;
      background: #007bff;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }

    button:hover {
      background: #0056b3;
    }

    p {
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
    }

    a {
      color: #007bff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .form {
      display: none;
    }

    /* Show the correct form based on PHP $showForm */
    <?php if ($showForm === 'login'): ?>
      #login-form {
        display: block;
      }

    <?php else: ?>
      #register-form {
        display: block;
      }

    <?php endif; ?>

    .error {
      background: #f8d7da;
      color: #842029;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      font-size: 14px;
      text-align: center;
    }

    .success {
      background: #d1e7dd;
      color: #0f5132;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      font-size: 14px;
      text-align: center;
    }
  </style>
</head>

<body>

  <div class="container">
    <?php if ($errors): ?>
      <div class="error">
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="login-form" class="form" method="POST" action="">
      <h2>Login</h2>
      <input type="hidden" name="form_type" value="login" />
      <div class="form-group">
        <label for="login-username">Username</label>
        <input id="login-username" type="text" name="username" placeholder="Enter your username" required
          value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
      </div>
      <div class="form-group">
        <label for="login-password">Password</label>
        <input id="login-password" type="password" name="password" placeholder="Enter password" required />
      </div>
      <button type="submit">Login</button>
      <p>Don't have an account? <a href="#register"
          onclick="document.getElementById('login-form').style.display='none';document.getElementById('register-form').style.display='block';">Register</a>
      </p>
    </form>

    <!-- Register Form -->
    <form id="register-form" class="form" method="POST" action="">
      <h2>Register</h2>
      <input type="hidden" name="form_type" value="register" />
      <div class="form-group">
        <label for="reg-username">Username</label>
        <input id="reg-username" type="text" name="username" placeholder="Choose username" required
          value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
      </div>
      <div class="form-group">
        <label for="reg-email">Email</label>
        <input id="reg-email" type="email" name="email" placeholder="Enter email" required
          value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
      </div>
      <div class="form-group">
        <label for="reg-password">Password</label>
        <input id="reg-password" type="password" name="password" placeholder="Create password" required />
      </div>
      <button type="submit">Register</button>
      <p>Already have an account? <a href="#login"
          onclick="document.getElementById('register-form').style.display='none';document.getElementById('login-form').style.display='block';">Login</a>
      </p>
    </form>
  </div>

  <script>
    // Show correct form if user clicks links (optional)
    document.querySelectorAll('a[href="#login"], a[href="#register"]').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const target = e.target.getAttribute('href').substring(1);
        document.getElementById('login-form').style.display = (target === 'login') ? 'block' : 'none';
        document.getElementById('register-form').style.display = (target === 'register') ? 'block' : 'none';
      });
    });
  </script>

</body>

</html>
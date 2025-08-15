<?php
session_start();
require '../db.php'; // make sure $conn is a mysqli connection

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT admin_id, email, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // Direct comparison (no hashing)
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_email'] = $admin['email'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm rounded-xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Admin Login</h2>
        <form method="post" action="">
            <label class="block mb-2 font-semibold" for="email">Email</label>
            <input type="email" id="email" name="email" required
                class="w-full px-4 py-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />

            <label class="block mb-2 font-semibold" for="password">Password</label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2 mb-6 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />

            <button type="submit" 
                class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">Login</button>
        </form>
        <?php if($error): ?>
            <p class="mt-4 text-red-600 text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

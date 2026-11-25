<?php
require_once 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password) VALUES (?, ?)"
            );

            $stmt->execute([$username, $hashedPassword]);

            $message = "Account created successfully!";
        } catch (PDOException $e) {
            $message = "Username already exists or registration failed.";
        }
    }
}
?>

<h2>Create Account</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <button type="submit">Register</button>
</form>
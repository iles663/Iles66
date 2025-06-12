<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {  // Added missing parenthesis and > 0
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            // Redirect instead of JSON response
            header('Location: index.php');
            exit;
        }
    }

    // If login fails, redirect back to login with error
    header('Location: login.php?error=1');
    exit;
}
    

    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}
?>
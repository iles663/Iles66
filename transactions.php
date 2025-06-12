<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get transaction history
    $sql = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $transactions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    
    echo json_encode($transactions);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'];
    $amount = floatval($data['amount']);
    $shares = intval($data['shares']);
    
    // Get current user balance and shares
    $user_sql = "SELECT balance, shares FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_sql);
    $user = mysqli_fetch_assoc($user_result);
    
    if ($action === 'buy') {
        if ($user['balance'] < $amount) {
            echo json_encode(['success' => false, 'message' => 'Insufficient funds']);
            exit;
        }
        
        $new_balance = $user['balance'] - $amount;
        $new_shares = $user['shares'] + $shares;
        
        // Update user
        $update_sql = "UPDATE users SET balance = $new_balance, shares = $new_shares WHERE id = '$user_id'";
        mysqli_query($conn, $update_sql);
        
        // Record transaction
        $tx_sql = "INSERT INTO transactions (user_id, type, amount, shares) 
                   VALUES ('$user_id', 'buy', $amount, $shares)";
        mysqli_query($conn, $tx_sql);
        
        echo json_encode(['success' => true, 'new_balance' => $new_balance, 'new_shares' => $new_shares]);
        
    } elseif ($action === 'sell') {
        if ($user['shares'] < $shares) {
            echo json_encode(['success' => false, 'message' => 'Insufficient shares']);
            exit;
        }
        
        $new_balance = $user['balance'] + $amount;
        $new_shares = $user['shares'] - $shares;
        
        // Update user
        $update_sql = "UPDATE users SET balance = $new_balance, shares = $new_shares WHERE id = '$user_id'";
        mysqli_query($conn, $update_sql);
        
        // Record transaction
        $tx_sql = "INSERT INTO transactions (user_id, type, amount, shares) 
                   VALUES ('$user_id', 'sell', $amount, $shares)";
        mysqli_query($conn, $tx_sql);
        
        echo json_encode(['success' => true, 'new_balance' => $new_balance, 'new_shares' => $new_shares]);
    }
}
?>
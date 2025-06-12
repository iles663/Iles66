<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'deposit') {
        $amount = floatval($_POST['amount']);
        $transaction_id = uniqid('MPESA_');
        
        // In a real app, this would initiate an M-Pesa payment request
        // For demo purposes, we'll simulate it
        
        // Record pending transaction
        $sql = "INSERT INTO mpesa_transactions (user_id, transaction_id, amount, type, status) 
                VALUES ('$user_id', '$transaction_id', $amount, 'deposit', 'pending')";
        mysqli_query($conn, $sql);
        
        echo json_encode(['success' => true, 'transaction_id' => $transaction_id]);
        
    } elseif ($action === 'withdraw') {
        $amount = floatval($_POST['amount']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        
        // Check balance
        $user_sql = "SELECT balance FROM users WHERE id = '$user_id'";
        $user_result = mysqli_query($conn, $user_sql);
        $user = mysqli_fetch_assoc($user_result);
        
        if ($user['balance'] < $amount) {
            echo json_encode(['success' => false, 'message' => 'Insufficient funds']);
            exit;
        }
        
        // In a real app, this would initiate an M-Pesa withdrawal
        // For demo purposes, we'll simulate it
        
        // Update balance immediately for demo
        $new_balance = $user['balance'] - $amount;
        $update_sql = "UPDATE users SET balance = $new_balance WHERE id = '$user_id'";
        mysqli_query($conn, $update_sql);
        
        // Record transaction
        $tx_sql = "INSERT INTO transactions (user_id, type, amount, shares) 
                   VALUES ('$user_id', 'withdrawal', $amount, 0)";
        mysqli_query($conn, $tx_sql);
        
        echo json_encode(['success' => true, 'new_balance' => $new_balance]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $transaction_id = $_GET['transaction_id'] ?? '';
    
    if (empty($transaction_id)) {
        echo json_encode(['status' => 'failed']);
        exit;
    }
    
    // In a real app, this would check with M-Pesa API
    // For demo purposes, we'll simulate a successful payment after a delay
    
    // Check if transaction exists
    $sql = "SELECT * FROM mpesa_transactions WHERE transaction_id = '$transaction_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result)) {
        $tx = mysqli_fetch_assoc($result);
        
        // For demo, mark as completed after 5 seconds
        if (time() - strtotime($tx['created_at']) > 5 && $tx['status'] === 'pending') {
            // Update transaction status
            $update_sql = "UPDATE mpesa_transactions SET status = 'completed' WHERE transaction_id = '$transaction_id'";
            mysqli_query($conn, $update_sql);
            
            // Update user balance
            $user_sql = "UPDATE users SET balance = balance + {$tx['amount']} WHERE id = '$user_id'";
            mysqli_query($conn, $user_sql);
            
            // Record transaction
            $tx_sql = "INSERT INTO transactions (user_id, type, amount, shares) 
                       VALUES ('$user_id', 'deposit', {$tx['amount']}, 0)";
            mysqli_query($conn, $tx_sql);
            
            // Get new balance
            $balance_sql = "SELECT balance FROM users WHERE id = '$user_id'";
            $balance_result = mysqli_query($conn, $balance_sql);
            $balance = mysqli_fetch_assoc($balance_result)['balance'];
            
            echo json_encode(['status' => 'completed', 'amount' => $tx['amount'], 'new_balance' => $balance]);
        } else {
            echo json_encode(['status' => 'pending']);
        }
    } else {
        echo json_encode(['status' => 'failed']);
    }
}
?>
<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = $_GET['type'] ?? '';
    
    if ($type === 'buy' || $type === 'sell') {
        // Get offers of the opposite type (if user wants to buy, show sell offers and vice versa)
        $sql = "SELECT o.*, u.first_name, u.last_name, u.profile_image 
                FROM offers o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.type = '$type' AND o.user_id != '$user_id'";
        $result = mysqli_query($conn, $sql);
        
        $offers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $offers[] = $row;
        }
        
        echo json_encode($offers);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $type = $data['type'];
    $shares = intval($data['shares']);
    $price = floatval($data['price']);
    
    // Check if user has enough shares for sell offers
    if ($type === 'sell') {
        $user_sql = "SELECT shares FROM users WHERE id = '$user_id'";
        $user_result = mysqli_query($conn, $user_sql);
        $user = mysqli_fetch_assoc($user_result);
        
        if ($user['shares'] < $shares) {
            echo json_encode(['success' => false, 'message' => 'Insufficient shares']);
            exit;
        }
    }
    
    // Create new offer
    $sql = "INSERT INTO offers (user_id, type, shares, price) 
            VALUES ('$user_id', '$type', $shares, $price)";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create offer']);
    }
}
?>
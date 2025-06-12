<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'contacts') {
    // Get all users except current user
    $sql = "SELECT id, first_name, last_name, profile_image, 
            (SELECT COUNT(*) FROM messages WHERE receiver_id = '$user_id' AND sender_id = users.id AND read_at IS NULL) as unread_count,
            (SELECT MAX(created_at) FROM messages WHERE (sender_id = users.id AND receiver_id = '$user_id') OR (sender_id = '$user_id' AND receiver_id = users.id)) as last_seen,
            (SELECT COUNT(*) FROM active_sessions WHERE user_id = users.id) > 0 as online
            FROM users WHERE id != '$user_id'";
    $result = mysqli_query($conn, $sql);
    
    $contacts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
    
    echo json_encode($contacts);
    
} elseif ($action === 'chats') {
    // Get chat list with last message
    $sql = "SELECT u.id as user_id, u.first_name, u.last_name, u.profile_image, 
            m.message as last_message, m.created_at as last_message_time,
            (SELECT COUNT(*) FROM messages WHERE receiver_id = '$user_id' AND sender_id = u.id AND read_at IS NULL) as unread_count
            FROM users u
            JOIN messages m ON m.id = (
                SELECT MAX(id) FROM messages 
                WHERE (sender_id = u.id AND receiver_id = '$user_id') OR (sender_id = '$user_id' AND receiver_id = u.id)
            )
            WHERE u.id != '$user_id'
            ORDER BY m.created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $chats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $chats[] = $row;
    }
    
    echo json_encode($chats);
    
} elseif ($action === 'messages') {
    $receiver_id = $_GET['user_id'] ?? '';
    
    if (empty($receiver_id)) {
        echo json_encode([]);
        exit;
    }
    
    // Get messages between two users
    $sql = "SELECT *, sender_id = '$user_id' as sent 
            FROM messages 
            WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') 
               OR (sender_id = '$receiver_id' AND receiver_id = '$user_id')
            ORDER BY created_at ASC";
    $result = mysqli_query($conn, $sql);
    
    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
    
    // Mark messages as read
    $update_sql = "UPDATE messages SET read_at = NOW() 
                   WHERE receiver_id = '$user_id' AND sender_id = '$receiver_id' AND read_at IS NULL";
    mysqli_query($conn, $update_sql);
    
    echo json_encode($messages);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'send') {
    $data = json_decode(file_get_contents('php://input'), true);
    $receiver_id = mysqli_real_escape_string($conn, $data['receiver_id']);
    $message = mysqli_real_escape_string($conn, $data['message']);
    
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) 
            VALUES ('$user_id', '$receiver_id', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
}
?>
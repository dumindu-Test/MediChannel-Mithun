<?php
/**
 * Session Authentication Handler
 * Manages user authentication and session validation
 */

require_once 'config.php';
require_once 'mock-data.php';

// Set CORS headers
setCORSHeaders();

// Initialize session
initializeSessionConfig();
session_start();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $_GET['check_auth'] ?? $_POST['action'] ?? '';

if ($action === '1' || $action === 'check_auth') {
    checkAuthentication();
} else {
    handleAuthRequest();
}

function checkAuthentication() {
    header('Content-Type: application/json');
    
    // Check for valid session from MySQL authentication
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'authenticated' => false,
            'user' => null
        ]);
        return;
    }
    
    // Verify user still exists in database
    try {
        require_once 'database.php';
        $db = Database::getInstance();
        $users = $db->query("SELECT * FROM users WHERE id = ? AND is_active = 1", [$_SESSION['user_id']]);
        
        if (empty($users)) {
            session_destroy();
            echo json_encode([
                'authenticated' => false,
                'user' => null
            ]);
            return;
        }
        
        $user = $users[0];
        echo json_encode([
            'authenticated' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ]
        ]);
    } catch (Exception $e) {
        error_log("Session verification error: " . $e->getMessage());
        echo json_encode([
            'authenticated' => false,
            'user' => null
        ]);
    }
}

function handleAuthRequest() {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleLogin($input);
            break;
        case 'logout':
            handleLogout();
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handleLogin($input) {
    header('Content-Type: application/json');
    
    // Redirect to proper MySQL authentication
    http_response_code(302);
    echo json_encode([
        'error' => 'Please use /php/auth.php for authentication',
        'redirect' => '/php/auth.php'
    ]);
}

function handleLogout() {
    header('Content-Type: application/json');
    
    // Clear session
    session_destroy();
    
    echo json_encode(['success' => true]);
}

function getDashboardUrl($role) {
    switch ($role) {
        case 'admin':
            return 'dashboard-admin.html';
        case 'doctor':
            return 'dashboard-doctor.html';
        case 'patient':
        default:
            return 'dashboard-patient.html';
    }
}
?>
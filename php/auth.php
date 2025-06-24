<?php
/**
 * Authentication API for user login, registration, and session management
 */

require_once 'config.php';
require_once 'database.php';

setCORSHeaders();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();
    
    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'login':
                    handleLogin($db, $input);
                    break;
                case 'register':
                    handleRegister($db, $input);
                    break;
                case 'logout':
                    handleLogout();
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action']);
            }
            break;
            
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'verify') {
                verifySession($db);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log("Error in auth.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Authentication error']);
}

function handleLogin($db, $input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }
    
    // Check user credentials
    $sql = "SELECT u.*, d.id as doctor_id, d.specialty, p.id as patient_id 
            FROM users u 
            LEFT JOIN doctors d ON u.id = d.user_id 
            LEFT JOIN patients p ON u.id = p.user_id 
            WHERE u.email = ? AND u.is_active = 1";
    
    $users = $db->query($sql, [$email]);
    
    if (empty($users)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        return;
    }
    
    $user = $users[0];
    
    // Verify password hash
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        return;
    }
    
    // Create session
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    if ($user['role'] === 'doctor' && $user['doctor_id']) {
        $_SESSION['doctor_id'] = $user['doctor_id'];
        $_SESSION['specialty'] = $user['specialty'];
    }
    
    if ($user['role'] === 'patient' && $user['patient_id']) {
        $_SESSION['patient_id'] = $user['patient_id'];
    }
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'role' => $user['role'],
            'doctor_id' => $user['doctor_id'] ?? null,
            'patient_id' => $user['patient_id'] ?? null,
            'specialty' => $user['specialty'] ?? null
        ]
    ]);
}

function handleRegister($db, $input) {
    $firstName = $input['first_name'] ?? '';
    $lastName = $input['last_name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $phone = $input['phone'] ?? '';
    $role = $input['role'] ?? 'patient';
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'All required fields must be filled']);
        return;
    }
    
    // Check if email already exists
    $existingUsers = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
    if (!empty($existingUsers)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already registered']);
        return;
    }
    
    // Create username from email
    $username = explode('@', $email)[0];
    
    // Hash password securely
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $db->beginTransaction();
        
        // Insert user
        $sql = "INSERT INTO users (username, email, password_hash, role, first_name, last_name, phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $db->execute($sql, [$username, $email, $passwordHash, $role, $firstName, $lastName, $phone]);
        
        $userId = $db->lastInsertId();
        
        // If registering as patient, create patient record
        if ($role === 'patient') {
            $db->execute("INSERT INTO patients (user_id) VALUES (?)", [$userId]);
        }
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $userId
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        error_log("Registration error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed']);
    }
}

function handleLogout() {
    session_start();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function verifySession($db) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['authenticated' => false]);
        return;
    }
    
    // Verify user still exists and is active
    $users = $db->query("SELECT * FROM users WHERE id = ? AND is_active = 1", [$_SESSION['user_id']]);
    
    if (empty($users)) {
        session_destroy();
        http_response_code(401);
        echo json_encode(['authenticated' => false]);
        return;
    }
    
    $user = $users[0];
    
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'role' => $user['role']
        ]
    ]);
}
?>
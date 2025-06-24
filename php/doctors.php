<?php
/**
 * Doctors API - MySQL Database Implementation
 * Manages doctor listings, profiles, and related operations
 */

require_once 'config.php';
require_once 'database.php';

setCORSHeaders();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get specific doctor
                $doctor_id = (int)$_GET['id'];
                $sql = "SELECT d.*, u.first_name, u.last_name, u.email, u.phone, u.city 
                        FROM doctors d 
                        JOIN users u ON d.user_id = u.id 
                        WHERE d.id = ?";
                $doctor = $db->query($sql, [$doctor_id]);
                
                if (empty($doctor)) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Doctor not found']);
                    exit;
                }
                
                $doctor = $doctor[0];
                // Combine names for frontend compatibility
                $doctor['name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
                
                // Parse comma-separated fields
                $doctor['subspecialties'] = !empty($doctor['subspecialties']) ? explode(',', $doctor['subspecialties']) : [];
                $doctor['languages'] = !empty($doctor['languages']) ? explode(',', $doctor['languages']) : [];
                $doctor['certifications'] = !empty($doctor['certifications']) ? explode(',', $doctor['certifications']) : [];
                $doctor['hospital_affiliations'] = !empty($doctor['hospital_affiliations']) ? explode(',', $doctor['hospital_affiliations']) : [];
                $doctor['services'] = !empty($doctor['services']) ? explode(',', $doctor['services']) : [];
                $doctor['location'] = $doctor['hospital_affiliations'][0] ?? $doctor['city'];
                $doctor['available'] = (bool)$doctor['is_available'];
                
                echo json_encode($doctor);
            } else {
                // Get all doctors or filter by specialty
                $specialty = $_GET['specialty'] ?? null;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
                $search = $_GET['search'] ?? null;
                
                $sql = "SELECT d.*, u.first_name, u.last_name, u.email, u.phone, u.city 
                        FROM doctors d 
                        JOIN users u ON d.user_id = u.id 
                        WHERE d.is_available = true";
                $params = [];
                
                if ($specialty) {
                    $sql .= " AND LOWER(d.specialty) = LOWER(?)";
                    $params[] = $specialty;
                }
                
                if ($search) {
                    $sql .= " AND (LOWER(CONCAT(u.first_name, ' ', u.last_name)) LIKE LOWER(?) OR LOWER(d.specialty) LIKE LOWER(?))";
                    $searchTerm = "%$search%";
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                }
                
                if ($limit) {
                    $sql .= " LIMIT ?";
                    $params[] = $limit;
                }
                
                $doctors = $db->query($sql, $params);
                
                // Process each doctor
                foreach ($doctors as &$doctor) {
                    $doctor['name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
                    $doctor['subspecialties'] = !empty($doctor['subspecialties']) ? explode(',', $doctor['subspecialties']) : [];
                    $doctor['languages'] = !empty($doctor['languages']) ? explode(',', $doctor['languages']) : [];
                    $doctor['certifications'] = !empty($doctor['certifications']) ? explode(',', $doctor['certifications']) : [];
                    $doctor['hospital_affiliations'] = !empty($doctor['hospital_affiliations']) ? explode(',', $doctor['hospital_affiliations']) : [];
                    $doctor['services'] = !empty($doctor['services']) ? explode(',', $doctor['services']) : [];
                    $doctor['location'] = $doctor['hospital_affiliations'][0] ?? $doctor['city'];
                    $doctor['available'] = (bool)$doctor['is_available'];
                }
                
                echo json_encode($doctors);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Error in doctors.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Please check MySQL connection.']);
}
?>
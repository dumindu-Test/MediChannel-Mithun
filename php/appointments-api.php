<?php
/**
 * Appointments API - MySQL Implementation
 */

require_once 'config.php';
require_once 'database.php';

setCORSHeaders();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();
    
    switch ($method) {
        case 'GET':
            $sql = "SELECT a.*, 
                           CONCAT(d_user.first_name, ' ', d_user.last_name) as doctor_name,
                           d.specialty as doctor_specialty,
                           CONCAT(p_user.first_name, ' ', p_user.last_name) as patient_name
                    FROM appointments a
                    LEFT JOIN doctors d ON a.doctor_id = d.id
                    LEFT JOIN users d_user ON d.user_id = d_user.id
                    LEFT JOIN patients p ON a.patient_id = p.id
                    LEFT JOIN users p_user ON p.user_id = p_user.id
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC
                    LIMIT 20";
            
            $appointments = $db->query($sql);
            echo json_encode($appointments);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Create a mock appointment for demo
            $newAppointment = [
                'doctor_id' => $input['doctor_id'] ?? 1,
                'appointment_date' => $input['date'] ?? date('Y-m-d'),
                'appointment_time' => $input['time'] ?? '09:00',
                'status' => 'pending',
                'reason_for_visit' => $input['reason'] ?? 'General consultation',
                'booking_reference' => 'REF' . time(),
                'consultation_fee' => $input['fee'] ?? 3000.00
            ];
            
            echo json_encode(['success' => true, 'appointment' => $newAppointment, 'message' => 'Appointment booked successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Error in appointments-api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
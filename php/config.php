<?php
/**
 * Database and Application Configuration
 * HealthCare+ E-Channelling System
 */

// Include performance optimizations
require_once __DIR__ . '/performance-headers.php';

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL Database Configuration
define('MYSQL_HOST', 'sql12.freesqldatabase.com');
define('MYSQL_DATABASE', 'sql12786593');
define('MYSQL_USERNAME', 'sql12786593');
define('MYSQL_PASSWORD', 'jWrQK5p23K');
define('MYSQL_PORT', '3306');

// Application Configuration
define('APP_NAME', 'HealthCare+');
define('APP_VERSION', '1.0.0');
define('APP_URL', isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://0.0.0.0:5000');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// Email Configuration (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@healthcareplus.com');
define('FROM_NAME', 'HealthCare+');

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', 'uploads/');

// Timezone Configuration
date_default_timezone_set('America/New_York');

// CORS Headers for API requests - Secure configuration
function setCORSHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed_origins = [
        'http://localhost:5000',
        'http://0.0.0.0:5000'
    ];
    
    // Allow Replit domains
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.replit.dev') !== false) {
        $allowed_origins[] = 'https://' . $_SERVER['HTTP_HOST'];
    }
    
    if (in_array($origin, $allowed_origins) || empty($origin)) {
        header("Access-Control-Allow-Origin: " . ($origin ?: "*"));
    }
    
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");
}

// Initialize session configuration for better cross-environment compatibility
function initializeSessionConfig() {
    // Configure session settings for better compatibility
    ini_set('session.cookie_httponly', 1);
    
    // Use secure cookies for HTTPS, non-secure for HTTP
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    ini_set('session.cookie_secure', $isSecure ? 1 : 0);
    
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    
    // Don't set domain for localhost compatibility
    ini_set('session.cookie_domain', '');
}

// Mock Data Storage Class (Static Demo Mode)
class MockDataStorage {
    private static $instance = null;
    private $doctors = [];
    private $appointments = [];
    private $users = [];
    private $sessions = [];
    
    private function __construct() {
        $this->initializeMockData();
        $this->initializeUsers();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeMockData() {
        $this->doctors = [
            [
                'id' => 1,
                'name' => 'Sarah Johnson',
                'specialty' => 'Cardiology',
                'subspecialties' => ['Interventional Cardiology', 'Heart Failure'],
                'education' => 'Harvard Medical School',
                'experience' => 15,
                'location' => 'Medical Center, Downtown',
                'phone' => '+1 (555) 123-4567',
                'email' => 'dr.johnson@healthcareplus.com',
                'fee' => 250.00,
                'rating' => 4.8,
                'reviews' => 156,
                'about' => 'Dr. Johnson is a board-certified cardiologist with extensive experience in treating heart conditions.',
                'services' => ['Cardiac Consultation', 'ECG', 'Stress Testing', 'Heart Surgery'],
                'certifications' => ['Board Certified Cardiologist', 'Advanced Cardiac Life Support'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 2500
            ],
            [
                'id' => 2,
                'name' => 'Michael Chen',
                'specialty' => 'Neurology',
                'subspecialties' => ['Stroke Medicine', 'Epilepsy'],
                'education' => 'Johns Hopkins Medical School',
                'experience' => 12,
                'location' => 'Neurological Institute, Uptown',
                'phone' => '+1 (555) 234-5678',
                'email' => 'dr.chen@healthcareplus.com',
                'fee' => 300.00,
                'rating' => 4.9,
                'reviews' => 203,
                'about' => 'Dr. Chen specializes in neurological disorders with a focus on stroke prevention and treatment.',
                'services' => ['Neurological Consultation', 'EEG', 'Brain Imaging', 'Stroke Treatment'],
                'certifications' => ['Board Certified Neurologist', 'Stroke Specialist'],
                'languages' => ['English', 'Mandarin'],
                'available' => true,
                'patients_treated' => 1800
            ],
            [
                'id' => 3,
                'name' => 'Emily Rodriguez',
                'specialty' => 'Pediatrics',
                'subspecialties' => ['Pediatric Emergency Medicine', 'Child Development'],
                'education' => 'Stanford Medical School',
                'experience' => 8,
                'location' => 'Children\'s Hospital, Westside',
                'phone' => '+1 (555) 345-6789',
                'email' => 'dr.rodriguez@healthcareplus.com',
                'fee' => 180.00,
                'rating' => 4.7,
                'reviews' => 89,
                'about' => 'Dr. Rodriguez is dedicated to providing comprehensive pediatric care for children of all ages.',
                'services' => ['Child Health Checkups', 'Vaccinations', 'Developmental Assessment'],
                'certifications' => ['Board Certified Pediatrician', 'Pediatric Advanced Life Support'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 1200
            ],
            [
                'id' => 4,
                'name' => 'David Thompson',
                'specialty' => 'Orthopedics',
                'subspecialties' => ['Sports Medicine', 'Joint Replacement'],
                'education' => 'Mayo Clinic Medical School',
                'experience' => 20,
                'location' => 'Orthopedic Center, Central',
                'phone' => '+1 (555) 456-7890',
                'email' => 'dr.thompson@healthcareplus.com',
                'fee' => 275.00,
                'rating' => 4.6,
                'reviews' => 134,
                'about' => 'Dr. Thompson is an experienced orthopedic surgeon specializing in sports injuries and joint replacement.',
                'services' => ['Joint Surgery', 'Sports Injury Treatment', 'Physical Therapy'],
                'certifications' => ['Board Certified Orthopedic Surgeon', 'Sports Medicine Specialist'],
                'languages' => ['English'],
                'available' => false,
                'patients_treated' => 3200
            ],
            [
                'id' => 5,
                'name' => 'Lisa Park',
                'specialty' => 'Dermatology',
                'subspecialties' => ['Cosmetic Dermatology', 'Skin Cancer'],
                'education' => 'Yale Medical School',
                'experience' => 10,
                'location' => 'Dermatology Clinic, Eastside',
                'phone' => '+1 (555) 567-8901',
                'email' => 'dr.park@healthcareplus.com',
                'fee' => 220.00,
                'rating' => 4.5,
                'reviews' => 76,
                'about' => 'Dr. Park provides comprehensive dermatological care including cosmetic and medical treatments.',
                'services' => ['Skin Examination', 'Acne Treatment', 'Cosmetic Procedures'],
                'certifications' => ['Board Certified Dermatologist', 'Cosmetic Surgery Certified'],
                'languages' => ['English', 'Korean'],
                'available' => true,
                'patients_treated' => 950
            ],
            [
                'id' => 6,
                'name' => 'Robert Williams',
                'specialty' => 'Internal Medicine',
                'subspecialties' => ['Diabetes Management', 'Hypertension'],
                'education' => 'University of Pennsylvania Medical School',
                'experience' => 18,
                'location' => 'Primary Care Center, Midtown',
                'phone' => '+1 (555) 678-9012',
                'email' => 'dr.williams@healthcareplus.com',
                'fee' => 200.00,
                'rating' => 4.7,
                'reviews' => 242,
                'about' => 'Dr. Williams specializes in comprehensive internal medicine with focus on chronic disease management.',
                'services' => ['Annual Physical', 'Diabetes Care', 'Blood Pressure Management', 'Preventive Care'],
                'certifications' => ['Board Certified Internal Medicine', 'Diabetes Educator'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 3800
            ],
            [
                'id' => 7,
                'name' => 'Amanda Foster',
                'specialty' => 'Obstetrics & Gynecology',
                'subspecialties' => ['High-Risk Pregnancy', 'Reproductive Health'],
                'education' => 'Duke University Medical School',
                'experience' => 14,
                'location' => 'Women\'s Health Center, Northside',
                'phone' => '+1 (555) 789-0123',
                'email' => 'dr.foster@healthcareplus.com',
                'fee' => 280.00,
                'rating' => 4.9,
                'reviews' => 187,
                'about' => 'Dr. Foster provides comprehensive women\'s healthcare including pregnancy care and gynecological services.',
                'services' => ['Prenatal Care', 'Annual Exams', 'Family Planning', 'Ultrasound'],
                'certifications' => ['Board Certified OB/GYN', 'Maternal-Fetal Medicine'],
                'languages' => ['English', 'French'],
                'available' => true,
                'patients_treated' => 2200
            ],
            [
                'id' => 8,
                'name' => 'James Martinez',
                'specialty' => 'Psychiatry',
                'subspecialties' => ['Anxiety Disorders', 'Depression Treatment'],
                'education' => 'Columbia University Medical School',
                'experience' => 11,
                'location' => 'Mental Health Center, Downtown',
                'phone' => '+1 (555) 890-1234',
                'email' => 'dr.martinez@healthcareplus.com',
                'fee' => 350.00,
                'rating' => 4.8,
                'reviews' => 156,
                'about' => 'Dr. Martinez specializes in mental health treatment with a focus on anxiety and mood disorders.',
                'services' => ['Individual Therapy', 'Medication Management', 'Cognitive Behavioral Therapy'],
                'certifications' => ['Board Certified Psychiatrist', 'CBT Certified'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 1450
            ],
            [
                'id' => 9,
                'name' => 'Helen Chang',
                'specialty' => 'Ophthalmology',
                'subspecialties' => ['Cataract Surgery', 'Retinal Disorders'],
                'education' => 'University of California Medical School',
                'experience' => 16,
                'location' => 'Eye Care Center, Westside',
                'phone' => '+1 (555) 901-2345',
                'email' => 'dr.chang@healthcareplus.com',
                'fee' => 320.00,
                'rating' => 4.6,
                'reviews' => 198,
                'about' => 'Dr. Chang is an experienced ophthalmologist specializing in comprehensive eye care and surgery.',
                'services' => ['Eye Exams', 'Cataract Surgery', 'Glaucoma Treatment', 'LASIK'],
                'certifications' => ['Board Certified Ophthalmologist', 'Retina Specialist'],
                'languages' => ['English', 'Mandarin'],
                'available' => true,
                'patients_treated' => 2800
            ],
            [
                'id' => 10,
                'name' => 'Thomas Anderson',
                'specialty' => 'Gastroenterology',
                'subspecialties' => ['Inflammatory Bowel Disease', 'Liver Disease'],
                'education' => 'Northwestern University Medical School',
                'experience' => 13,
                'location' => 'Digestive Health Center, Central',
                'phone' => '+1 (555) 012-3456',
                'email' => 'dr.anderson@healthcareplus.com',
                'fee' => 290.00,
                'rating' => 4.7,
                'reviews' => 134,
                'about' => 'Dr. Anderson specializes in digestive health and liver diseases with advanced endoscopic procedures.',
                'services' => ['Colonoscopy', 'Endoscopy', 'Liver Biopsy', 'IBD Treatment'],
                'certifications' => ['Board Certified Gastroenterologist', 'Advanced Endoscopy'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1900
            ],
            [
                'id' => 11,
                'name' => 'Maria Gonzalez',
                'specialty' => 'Endocrinology',
                'subspecialties' => ['Thyroid Disorders', 'Hormone Therapy'],
                'education' => 'Baylor College of Medicine',
                'experience' => 9,
                'location' => 'Hormone Health Clinic, Southside',
                'phone' => '+1 (555) 123-4567',
                'email' => 'dr.gonzalez@healthcareplus.com',
                'fee' => 260.00,
                'rating' => 4.8,
                'reviews' => 112,
                'about' => 'Dr. Gonzalez specializes in endocrine disorders and hormone-related conditions.',
                'services' => ['Diabetes Management', 'Thyroid Treatment', 'Hormone Replacement', 'Metabolic Disorders'],
                'certifications' => ['Board Certified Endocrinologist', 'Diabetes Specialist'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 1300
            ],
            [
                'id' => 12,
                'name' => 'Kevin Lee',
                'specialty' => 'Urology',
                'subspecialties' => ['Kidney Stones', 'Prostate Health'],
                'education' => 'Washington University Medical School',
                'experience' => 17,
                'location' => 'Urology Associates, Eastside',
                'phone' => '+1 (555) 234-5678',
                'email' => 'dr.lee@healthcareplus.com',
                'fee' => 310.00,
                'rating' => 4.5,
                'reviews' => 167,
                'about' => 'Dr. Lee provides comprehensive urological care for both men and women.',
                'services' => ['Prostate Screening', 'Kidney Stone Treatment', 'Bladder Conditions', 'Male Health'],
                'certifications' => ['Board Certified Urologist', 'Minimally Invasive Surgery'],
                'languages' => ['English', 'Korean'],
                'available' => false,
                'patients_treated' => 2600
            ],
            [
                'id' => 13,
                'name' => 'Rachel Kim',
                'specialty' => 'Oncology',
                'subspecialties' => ['Medical Oncology', 'Cancer Research'],
                'education' => 'Memorial Sloan Kettering',
                'experience' => 19,
                'location' => 'Cancer Treatment Center, Downtown',
                'phone' => '+1 (555) 345-6789',
                'email' => 'dr.kim@healthcareplus.com',
                'fee' => 450.00,
                'rating' => 4.9,
                'reviews' => 245,
                'about' => 'Dr. Kim is a leading oncologist specializing in advanced cancer treatments.',
                'services' => ['Cancer Consultation', 'Chemotherapy', 'Clinical Trials'],
                'certifications' => ['Board Certified Oncologist', 'Cancer Research Specialist'],
                'languages' => ['English', 'Korean'],
                'available' => true,
                'patients_treated' => 1800
            ],
            [
                'id' => 14,
                'name' => 'Steven Clark',
                'specialty' => 'Emergency Medicine',
                'subspecialties' => ['Trauma Care', 'Critical Care'],
                'education' => 'University of Michigan Medical School',
                'experience' => 12,
                'location' => 'Emergency Medical Center, Central',
                'phone' => '+1 (555) 456-7890',
                'email' => 'dr.clark@healthcareplus.com',
                'fee' => 320.00,
                'rating' => 4.6,
                'reviews' => 178,
                'about' => 'Dr. Clark specializes in emergency medicine and trauma care.',
                'services' => ['Emergency Care', 'Trauma Treatment', 'Critical Care'],
                'certifications' => ['Board Certified Emergency Medicine', 'Trauma Specialist'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 2200
            ],
            [
                'id' => 15,
                'name' => 'Jessica Wright',
                'specialty' => 'Rheumatology',
                'subspecialties' => ['Arthritis Treatment', 'Autoimmune Disorders'],
                'education' => 'University of Chicago Medical School',
                'experience' => 14,
                'location' => 'Arthritis & Rheumatism Center, Northside',
                'phone' => '+1 (555) 567-8901',
                'email' => 'dr.wright@healthcareplus.com',
                'fee' => 340.00,
                'rating' => 4.7,
                'reviews' => 129,
                'about' => 'Dr. Wright specializes in rheumatic diseases and joint disorders.',
                'services' => ['Arthritis Treatment', 'Joint Injection', 'Rheumatic Disease Care'],
                'certifications' => ['Board Certified Rheumatologist', 'Arthritis Specialist'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1500
            ],
            [
                'id' => 16,
                'name' => 'Mark Davis',
                'specialty' => 'Pulmonology',
                'subspecialties' => ['Respiratory Care', 'Sleep Medicine'],
                'education' => 'University of Pennsylvania Medical School',
                'experience' => 16,
                'location' => 'Respiratory Health Institute, Westside',
                'phone' => '+1 (555) 678-9012',
                'email' => 'dr.davis@healthcareplus.com',
                'fee' => 360.00,
                'rating' => 4.8,
                'reviews' => 165,
                'about' => 'Dr. Davis specializes in lung diseases and respiratory care.',
                'services' => ['Lung Function Tests', 'Sleep Studies', 'Respiratory Treatment'],
                'certifications' => ['Board Certified Pulmonologist', 'Sleep Medicine Specialist'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1900
            ],
            [
                'id' => 17,
                'name' => 'Nina Patel',
                'specialty' => 'Nephrology',
                'subspecialties' => ['Kidney Disease', 'Dialysis'],
                'education' => 'Harvard Medical School',
                'experience' => 13,
                'location' => 'Kidney Care Center, Eastside',
                'phone' => '+1 (555) 789-0123',
                'email' => 'dr.patel@healthcareplus.com',
                'fee' => 380.00,
                'rating' => 4.6,
                'reviews' => 143,
                'about' => 'Dr. Patel specializes in kidney diseases and renal care.',
                'services' => ['Kidney Consultation', 'Dialysis Management', 'Renal Care'],
                'certifications' => ['Board Certified Nephrologist', 'Dialysis Specialist'],
                'languages' => ['English', 'Hindi'],
                'available' => true,
                'patients_treated' => 1600
            ],
            [
                'id' => 18,
                'name' => 'Christopher Wilson',
                'specialty' => 'Infectious Disease',
                'subspecialties' => ['Tropical Medicine', 'HIV/AIDS Care'],
                'education' => 'Johns Hopkins Medical School',
                'experience' => 15,
                'location' => 'Infectious Disease Institute, Midtown',
                'phone' => '+1 (555) 890-1234',
                'email' => 'dr.wilson@healthcareplus.com',
                'fee' => 400.00,
                'rating' => 4.7,
                'reviews' => 156,
                'about' => 'Dr. Wilson specializes in infectious diseases and tropical medicine.',
                'services' => ['Infection Treatment', 'Travel Medicine', 'HIV Care'],
                'certifications' => ['Board Certified Infectious Disease', 'Tropical Medicine'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 1400
            ],
            [
                'id' => 19,
                'name' => 'Samantha Brown',
                'specialty' => 'Plastic Surgery',
                'subspecialties' => ['Reconstructive Surgery', 'Cosmetic Surgery'],
                'education' => 'NYU Medical School',
                'experience' => 18,
                'location' => 'Aesthetic Surgery Center, Downtown',
                'phone' => '+1 (555) 901-2345',
                'email' => 'dr.brown@healthcareplus.com',
                'fee' => 550.00,
                'rating' => 4.9,
                'reviews' => 234,
                'about' => 'Dr. Brown is a skilled plastic surgeon specializing in both reconstructive and cosmetic procedures.',
                'services' => ['Plastic Surgery', 'Reconstructive Surgery', 'Cosmetic Procedures'],
                'certifications' => ['Board Certified Plastic Surgeon', 'Reconstructive Surgery'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1200
            ],
            [
                'id' => 20,
                'name' => 'Daniel Taylor',
                'specialty' => 'Anesthesiology',
                'subspecialties' => ['Surgical Anesthesia', 'Pain Management'],
                'education' => 'Stanford Medical School',
                'experience' => 11,
                'location' => 'Surgical Care Center, Central',
                'phone' => '+1 (555) 012-3456',
                'email' => 'dr.taylor@healthcareplus.com',
                'fee' => 300.00,
                'rating' => 4.5,
                'reviews' => 98,
                'about' => 'Dr. Taylor specializes in anesthesiology and pain management.',
                'services' => ['Surgical Anesthesia', 'Pain Management', 'Sedation'],
                'certifications' => ['Board Certified Anesthesiologist', 'Pain Management'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1100
            ],
            [
                'id' => 21,
                'name' => 'Catherine Miller',
                'specialty' => 'Pathology',
                'subspecialties' => ['Clinical Pathology', 'Laboratory Medicine'],
                'education' => 'University of Washington Medical School',
                'experience' => 17,
                'location' => 'Diagnostic Laboratory, Northside',
                'phone' => '+1 (555) 123-4567',
                'email' => 'dr.miller@healthcareplus.com',
                'fee' => 280.00,
                'rating' => 4.8,
                'reviews' => 187,
                'about' => 'Dr. Miller specializes in pathology and laboratory medicine.',
                'services' => ['Laboratory Testing', 'Pathology Consultation', 'Diagnostic Services'],
                'certifications' => ['Board Certified Pathologist', 'Laboratory Medicine'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 2000
            ],
            [
                'id' => 22,
                'name' => 'Anthony Garcia',
                'specialty' => 'Radiology',
                'subspecialties' => ['Diagnostic Imaging', 'Interventional Radiology'],
                'education' => 'University of California Medical School',
                'experience' => 14,
                'location' => 'Imaging Center, Southside',
                'phone' => '+1 (555) 234-5678',
                'email' => 'dr.garcia@healthcareplus.com',
                'fee' => 320.00,
                'rating' => 4.6,
                'reviews' => 145,
                'about' => 'Dr. Garcia specializes in diagnostic imaging and interventional radiology.',
                'services' => ['MRI', 'CT Scans', 'X-rays', 'Interventional Procedures'],
                'certifications' => ['Board Certified Radiologist', 'Interventional Radiology'],
                'languages' => ['English', 'Spanish'],
                'available' => true,
                'patients_treated' => 1700
            ],
            [
                'id' => 23,
                'name' => 'Laura White',
                'specialty' => 'Sports Medicine',
                'subspecialties' => ['Athletic Injuries', 'Exercise Physiology'],
                'education' => 'University of Michigan Medical School',
                'experience' => 12,
                'location' => 'Sports Medicine Institute, Westside',
                'phone' => '+1 (555) 345-6789',
                'email' => 'dr.white@healthcareplus.com',
                'fee' => 350.00,
                'rating' => 4.7,
                'reviews' => 176,
                'about' => 'Dr. White specializes in sports medicine and athletic performance.',
                'services' => ['Sports Injury Treatment', 'Performance Enhancement', 'Rehabilitation'],
                'certifications' => ['Board Certified Sports Medicine', 'Exercise Physiology'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 1300
            ],
            [
                'id' => 24,
                'name' => 'Richard Moore',
                'specialty' => 'Family Medicine',
                'subspecialties' => ['Primary Care', 'Preventive Medicine'],
                'education' => 'University of Texas Medical School',
                'experience' => 20,
                'location' => 'Family Health Center, Eastside',
                'phone' => '+1 (555) 456-7890',
                'email' => 'dr.moore@healthcareplus.com',
                'fee' => 250.00,
                'rating' => 4.8,
                'reviews' => 298,
                'about' => 'Dr. Moore provides comprehensive family medicine and primary care.',
                'services' => ['Family Medicine', 'Preventive Care', 'Health Screenings'],
                'certifications' => ['Board Certified Family Medicine', 'Preventive Medicine'],
                'languages' => ['English'],
                'available' => true,
                'patients_treated' => 3500
            ]
        ];
    }
    
    private function initializeUsers() {
        $this->users = [
            // Patients
            [
                'id' => 1,
                'username' => 'patient1',
                'email' => 'john.doe@email.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'patient',
                'full_name' => 'John Doe',
                'phone' => '+1 (555) 111-1111',
                'date_of_birth' => '1990-05-15',
                'address' => '123 Main St, New York, NY 10001',
                'emergency_contact' => '+1 (555) 111-2222',
                'insurance_number' => 'INS123456789',
                'created_at' => '2024-01-15 10:30:00',
                'last_login' => '2025-06-10 14:20:00'
            ],
            [
                'id' => 2,
                'username' => 'patient2',
                'email' => 'maria.garcia@email.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'patient',
                'full_name' => 'Maria Garcia',
                'phone' => '+1 (555) 222-2222',
                'date_of_birth' => '1985-08-20',
                'address' => '456 Oak Ave, Los Angeles, CA 90210',
                'emergency_contact' => '+1 (555) 222-3333',
                'insurance_number' => 'INS987654321',
                'created_at' => '2024-02-20 09:15:00',
                'last_login' => '2025-06-09 16:45:00'
            ],
            // Doctors
            [
                'id' => 3,
                'username' => 'dr.johnson',
                'email' => 'dr.johnson@healthcareplus.com',
                'password' => password_hash('doctor123', PASSWORD_DEFAULT),
                'role' => 'doctor',
                'full_name' => 'Dr. Sarah Johnson',
                'phone' => '+1 (555) 123-4567',
                'doctor_id' => 1,
                'license_number' => 'MD123456',
                'department' => 'Cardiology',
                'created_at' => '2023-06-01 08:00:00',
                'last_login' => '2025-06-11 07:30:00'
            ],
            [
                'id' => 4,
                'username' => 'dr.chen',
                'email' => 'dr.chen@healthcareplus.com',
                'password' => password_hash('doctor123', PASSWORD_DEFAULT),
                'role' => 'doctor',
                'full_name' => 'Dr. Michael Chen',
                'phone' => '+1 (555) 234-5678',
                'doctor_id' => 2,
                'license_number' => 'MD234567',
                'department' => 'Neurology',
                'created_at' => '2023-07-15 08:00:00',
                'last_login' => '2025-06-10 18:20:00'
            ],
            [
                'id' => 5,
                'username' => 'dr.rodriguez',
                'email' => 'dr.rodriguez@healthcareplus.com',
                'password' => password_hash('doctor123', PASSWORD_DEFAULT),
                'role' => 'doctor',
                'full_name' => 'Dr. Emily Rodriguez',
                'phone' => '+1 (555) 345-6789',
                'doctor_id' => 3,
                'license_number' => 'MD345678',
                'department' => 'Pediatrics',
                'created_at' => '2023-08-20 08:00:00',
                'last_login' => '2025-06-11 09:15:00'
            ],
            // Admin/Hospital Staff
            [
                'id' => 6,
                'username' => 'admin',
                'email' => 'admin@healthcareplus.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'full_name' => 'Hospital Administrator',
                'phone' => '+1 (555) 999-0000',
                'department' => 'Administration',
                'permissions' => ['manage_doctors', 'manage_appointments', 'view_reports', 'manage_users'],
                'created_at' => '2023-01-01 00:00:00',
                'last_login' => '2025-06-11 08:00:00'
            ],
            [
                'id' => 7,
                'username' => 'staff1',
                'email' => 'reception@healthcareplus.com',
                'password' => password_hash('staff123', PASSWORD_DEFAULT),
                'role' => 'staff',
                'full_name' => 'Reception Staff',
                'phone' => '+1 (555) 888-0000',
                'department' => 'Reception',
                'permissions' => ['manage_appointments', 'view_patients'],
                'created_at' => '2023-03-15 09:00:00',
                'last_login' => '2025-06-10 17:30:00'
            ]
        ];
    }
    
    public function getDoctors() {
        return $this->doctors;
    }
    
    public function getDoctorById($id) {
        foreach ($this->doctors as $doctor) {
            if ($doctor['id'] == $id) {
                return $doctor;
            }
        }
        return null;
    }
    
    public function getAvailableTimeSlots($doctorId, $date) {
        $workingHours = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
            '17:00', '17:30'
        ];
        
        return $workingHours;
    }
    
    public function saveAppointment($data) {
        $bookingId = 'BK' . date('Ymd') . sprintf('%06d', rand(100000, 999999));
        $appointment = array_merge($data, [
            'id' => count($this->appointments) + 1,
            'booking_id' => $bookingId,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->appointments[] = $appointment;
        return $appointment;
    }
    
    public function getUsers() {
        return $this->users;
    }
    
    public function getUserByUsername($username) {
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }
    
    public function getUserByEmail($email) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function getUserById($id) {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
    
    public function authenticateUser($username, $password) {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function createSession($userId) {
        $sessionId = bin2hex(random_bytes(32));
        $sessionData = [
            'user_id' => $userId,
            'created_at' => time(),
            'expires_at' => time() + SESSION_LIFETIME
        ];
        
        // Store session in file for persistence with better path handling
        $tempDir = $this->getSessionDirectory();
        $sessionFile = $tempDir . '/healthcare_session_' . $sessionId;
        
        // Ensure directory exists
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        file_put_contents($sessionFile, json_encode($sessionData));
        
        return $sessionId;
    }
    
    private function getSessionDirectory() {
        // Try different temporary directory approaches for cross-platform compatibility
        $possibleDirs = [
            sys_get_temp_dir(),
            '/tmp',
            './tmp',
            '.'
        ];
        
        foreach ($possibleDirs as $dir) {
            if (is_writable($dir) || (!file_exists($dir) && is_writable(dirname($dir)))) {
                return $dir;
            }
        }
        
        // Fallback to current directory
        return '.';
    }
    
    public function getSession($sessionId) {
        $tempDir = $this->getSessionDirectory();
        $sessionFile = $tempDir . '/healthcare_session_' . $sessionId;
        
        if (file_exists($sessionFile)) {
            $sessionData = json_decode(file_get_contents($sessionFile), true);
            if ($sessionData && $sessionData['expires_at'] > time()) {
                return $sessionData;
            } else {
                // Session expired, clean up
                unlink($sessionFile);
            }
        }
        return null;
    }
    
    public function destroySession($sessionId) {
        $tempDir = $this->getSessionDirectory();
        $sessionFile = $tempDir . '/healthcare_session_' . $sessionId;
        if (file_exists($sessionFile)) {
            unlink($sessionFile);
            return true;
        }
        return false;
    }
    
    public function clearSession($sessionId) {
        return $this->destroySession($sessionId);
    }
    
    public function getAppointments() {
        return $this->appointments;
    }
    
    public function getAppointmentsByPatient($patientId) {
        return array_filter($this->appointments, function($appointment) use ($patientId) {
            return $appointment['patient_id'] == $patientId;
        });
    }
    
    public function getAppointmentsByDoctor($doctorId) {
        return array_filter($this->appointments, function($appointment) use ($doctorId) {
            return $appointment['doctor_id'] == $doctorId;
        });
    }
    
    public function getAppointmentsByDoctorAndDate($doctorId, $date) {
        return array_filter($this->appointments, function($appointment) use ($doctorId, $date) {
            return $appointment['doctor_id'] == $doctorId && $appointment['date'] === $date;
        });
    }
    
    public function updateDoctorStatus($doctorId, $status) {
        foreach ($this->doctors as &$doctor) {
            if ($doctor['id'] == $doctorId) {
                $doctor['available'] = $status;
                return true;
            }
        }
        return false;
    }
    
    public function addUser($userData) {
        $newId = count($this->users) + 1;
        $userData['id'] = $newId;
        $this->users[] = $userData;
        return $newId;
    }
    
    public function updateUser($userId, $updateData) {
        foreach ($this->users as &$user) {
            if ($user['id'] == $userId) {
                foreach ($updateData as $key => $value) {
                    $user[$key] = $value;
                }
                return true;
            }
        }
        return false;
    }

    public function updateDoctor($doctorId, $updateData) {
        foreach ($this->doctors as &$doctor) {
            if ($doctor['id'] == $doctorId) {
                foreach ($updateData as $key => $value) {
                    $doctor[$key] = $value;
                }
                return true;
            }
        }
        return false;
    }

    public function updateDoctorAvailability($doctorId, $availability) {
        // In a real app, this would update a separate availability table
        // For mock purposes, we'll store it in the doctor record
        foreach ($this->doctors as &$doctor) {
            if ($doctor['id'] == $doctorId) {
                $doctor['availability'] = $availability;
                return true;
            }
        }
        return false;
    }

    public function getPatientById($id) {
        if (empty($this->patients)) {
            $this->initializePatients();
        }
        return $this->patients[$id] ?? null;
    }
    
    public function getPatients() {
        if (empty($this->patients)) {
            $this->initializePatients();
        }
        return array_values($this->patients);
    }
    
    private function initializePatients() {
        $this->patients = [
            1 => [
                'id' => 1,
                'full_name' => 'John Doe',
                'email' => 'john.doe@email.com',
                'phone' => '+1 (555) 111-1111',
                'date_of_birth' => '1988-05-15',
                'gender' => 'Male',
                'address' => '123 Main St, City, State 12345',
                'emergency_contact' => '+1 (555) 111-2222',
                'insurance_number' => 'INS123456'
            ],
            2 => [
                'id' => 2,
                'full_name' => 'Maria Garcia',
                'email' => 'maria.garcia@email.com',
                'phone' => '+1 (555) 222-2222',
                'date_of_birth' => '1992-08-22',
                'gender' => 'Female',
                'address' => '456 Oak Ave, City, State 12345',
                'emergency_contact' => '+1 (555) 222-3333',
                'insurance_number' => 'INS789012'
            ],
            3 => [
                'id' => 3,
                'full_name' => 'David Wilson',
                'email' => 'david.wilson@email.com',
                'phone' => '+1 (555) 333-3333',
                'date_of_birth' => '1985-12-10',
                'gender' => 'Male',
                'address' => '789 Pine St, City, State 12345',
                'emergency_contact' => '+1 (555) 333-4444',
                'insurance_number' => 'INS345678'
            ]
        ];
    }

}

// Utility Functions
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

function generateBookingId() {
    return 'HC' . date('Ymd') . rand(1000, 9999);
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    setCORSHeaders();
    echo json_encode($data);
    exit;
}

function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Context: " . json_encode($context);
    }
    error_log($logMessage);
}

// Global authentication function
function checkUserAuth($allowedRoles = []) {
    $sessionId = $_COOKIE['session_id'] ?? '';
    
    if (empty($sessionId)) {
        return null;
    }
    
    $mockStorage = MockDataStorage::getInstance();
    $session = $mockStorage->getSession($sessionId);
    
    if ($session) {
        $user = $mockStorage->getUserById($session['user_id']);
        if ($user && (empty($allowedRoles) || in_array($user['role'], $allowedRoles))) {
            return $user;
        }
    }
    
    return null;
}

// Permission checking function
function hasPermission($user, $permission) {
    if (!$user || !isset($user['role'])) {
        return false;
    }
    
    $permissions = [
        'admin' => [
            'manage_doctors', 'manage_staff', 'manage_patients', 
            'manage_appointments', 'manage_settings', 'view_reports'
        ],
        'staff' => [
            'manage_appointments', 'view_patients', 'manage_staff'
        ],
        'doctor' => [
            'manage_appointments', 'view_patients'
        ],
        'patient' => [
            'view_appointments', 'book_appointments'
        ]
    ];
    
    $userRole = $user['role'];
    return isset($permissions[$userRole]) && in_array($permission, $permissions[$userRole]);
}

// Initialize mock data storage (static demo mode)
try {
    $mockStorage = MockDataStorage::getInstance();
} catch (Exception $e) {
    logError("Mock storage initialization failed: " . $e->getMessage());
}
?>

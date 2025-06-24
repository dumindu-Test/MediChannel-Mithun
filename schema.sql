-- HealthCare+ E-Channelling System
-- MySQL Database Schema - phpMyAdmin Compatible
-- Created: June 24, 2025

-- Drop existing tables if they exist (in correct order to avoid foreign key conflicts)
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `doctor_schedules`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `system_settings`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('patient','doctor','admin','staff') NOT NULL DEFAULT 'patient',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `subspecialties` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` int(11) NOT NULL DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `about` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `languages` text DEFAULT NULL,
  `patients_treated` int(11) DEFAULT 0,
  `license_number` varchar(50) DEFAULT NULL,
  `hospital_affiliations` text DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `specialty` (`specialty`),
  KEY `is_available` (`is_available`),
  KEY `rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `gender` (`gender`),
  KEY `blood_type` (`blood_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','in_progress','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `appointment_type` enum('consultation','follow_up','emergency','routine_checkup') NOT NULL DEFAULT 'consultation',
  `symptoms` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `follow_up_required` tinyint(1) NOT NULL DEFAULT 0,
  `follow_up_date` date DEFAULT NULL,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`),
  KEY `payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_appointments` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `day_of_week` (`day_of_week`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `hire_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `user_id` (`user_id`),
  KEY `department` (`department`),
  KEY `supervisor_id` (`supervisor_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('general','appointment','medical','billing','support') NOT NULL DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `attachment_path` varchar(255) DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `message_type` (`message_type`),
  KEY `priority` (`priority`),
  KEY `is_read` (`is_read`),
  KEY `reply_to` (`reply_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `rating` (`rating`),
  KEY `is_approved` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Add foreign key constraints after all tables are created
--

ALTER TABLE `doctors` ADD CONSTRAINT `doctors_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `patients` ADD CONSTRAINT `patients_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `appointments` ADD CONSTRAINT `appointments_patient_id` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
ALTER TABLE `appointments` ADD CONSTRAINT `appointments_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
ALTER TABLE `doctor_schedules` ADD CONSTRAINT `doctor_schedules_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
ALTER TABLE `staff` ADD CONSTRAINT `staff_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `messages` ADD CONSTRAINT `messages_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `messages` ADD CONSTRAINT `messages_recipient_id` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `reviews_patient_id` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `reviews_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT `reviews_appointment_id` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

-- --------------------------------------------------------

--
-- Sample Data Insertion
--

-- Insert sample users
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `first_name`, `last_name`, `phone`, `created_at`) VALUES
(1, 'admin', 'admin@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator', '+94771234567', NOW()),
(2, 'dr.perera', 'dr.perera@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Saman', 'Perera', '+94771234568', NOW()),
(3, 'dr.silva', 'dr.silva@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Kumari', 'Silva', '+94712345678', NOW()),
(4, 'dr.fernando', 'dr.fernando@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Rohan', 'Fernando', '+94723456789', NOW()),
(5, 'dr.jayawardena', 'dr.jayawardena@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Nimal', 'Jayawardena', '+94734567890', NOW()),
(6, 'dr.wickramasinghe', 'dr.wickramasinghe@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Chamari', 'Wickramasinghe', '+94745678901', NOW()),
(7, 'dr.rajapakse', 'dr.rajapakse@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Ajith', 'Rajapakse', '+94756789012', NOW()),
(8, 'patient1', 'patient@healthcareplus.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'John', 'Doe', '+94767890123', NOW()),
(9, 'patient2', 'mary.smith@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'Mary', 'Smith', '+94778901234', NOW());

-- Insert sample doctors
INSERT INTO `doctors` (`id`, `user_id`, `specialty`, `subspecialties`, `education`, `experience`, `location`, `consultation_fee`, `rating`, `total_reviews`, `about`, `services`, `certifications`, `languages`, `patients_treated`, `license_number`, `created_at`) VALUES
(1, 2, 'Cardiology', 'Interventional Cardiology, Heart Disease Prevention', 'MBBS University of Colombo, MD Cardiology', 15, 'National Hospital of Sri Lanka, Colombo', 5000.00, 4.8, 245, 'Specialized in interventional cardiology and heart disease prevention', 'Cardiac Consultation, Angioplasty, Heart Disease Prevention', 'Board Certified Cardiologist, Interventional Cardiology', 'English, Sinhala, Tamil', 2400, 'SL-CARD-001', NOW()),
(2, 3, 'Dermatology', 'Cosmetic Dermatology, Dermatosurgery', 'MBBS University of Peradeniya, MD Dermatology', 12, 'Teaching Hospital Kandy, Kandy', 3500.00, 4.7, 189, 'Expert in skin disorders, cosmetic dermatology, and dermatosurgery', 'Skin Consultation, Cosmetic Procedures, Dermatosurgery', 'Board Certified Dermatologist, Cosmetic Dermatology', 'English, Sinhala', 1800, 'SL-DERM-001', NOW()),
(3, 4, 'Orthopedic Surgery', 'Joint Replacement, Sports Medicine', 'MBBS University of Kelaniya, MS Orthopedic Surgery', 18, 'Karapitiya Teaching Hospital, Galle', 6000.00, 4.9, 312, 'Specialized in joint replacement and sports medicine', 'Orthopedic Surgery, Joint Replacement, Sports Injury Treatment', 'Board Certified Orthopedic Surgeon, Joint Replacement Specialist', 'English, Sinhala', 2100, 'SL-ORTH-001', NOW()),
(4, 5, 'Pediatrics', 'Child Development, Pediatric Cardiology', 'MBBS University of Colombo, MD Pediatrics', 10, 'Lady Ridgeway Hospital, Colombo', 3000.00, 4.6, 156, 'Dedicated to comprehensive child healthcare and development', 'Pediatric Consultation, Child Development, Vaccination', 'Board Certified Pediatrician, Child Development', 'English, Sinhala, Tamil', 1500, 'SL-PED-001', NOW()),
(5, 6, 'Gynecology', 'Obstetrics, Minimally Invasive Surgery', 'MBBS University of Peradeniya, MD Obstetrics & Gynecology', 14, 'Castle Street Hospital, Colombo', 4500.00, 4.8, 278, 'Comprehensive womens healthcare and obstetric services', 'Gynecological Consultation, Obstetrics, Minimally Invasive Surgery', 'Board Certified Gynecologist, Obstetrics Specialist', 'English, Sinhala', 2200, 'SL-GYN-001', NOW()),
(6, 7, 'General Medicine', 'Internal Medicine, Diabetes Management', 'MBBS University of Colombo, MD Internal Medicine', 16, 'Colombo General Hospital, Colombo', 2500.00, 4.7, 198, 'General medical practice with focus on internal medicine', 'General Consultation, Diabetes Management, Health Screening', 'Board Certified Internal Medicine, Diabetes Specialist', 'English, Sinhala', 3200, 'SL-GEN-001', NOW());

-- Insert sample patients
INSERT INTO `patients` (`id`, `user_id`, `date_of_birth`, `gender`, `blood_type`, `emergency_contact`, `emergency_phone`, `address`, `city`, `state`, `created_at`) VALUES
(1, 8, '1985-05-15', 'male', 'O+', 'Jane Doe', '+94789012345', '123 Main Street', 'Colombo', 'Western', NOW()),
(2, 9, '1990-08-22', 'female', 'A+', 'Robert Smith', '+94790123456', '456 Oak Avenue', 'Kandy', 'Central', NOW());

-- Insert sample appointments
INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `consultation_fee`, `payment_status`, `created_at`) VALUES
(1, 1, 1, '2025-06-25', '10:00:00', 'confirmed', 5000.00, 'paid', NOW()),
(2, 2, 2, '2025-06-25', '14:30:00', 'pending', 3500.00, 'pending', NOW()),
(3, 1, 3, '2025-06-26', '09:00:00', 'confirmed', 6000.00, 'paid', NOW());

-- Insert sample doctor schedules
INSERT INTO `doctor_schedules` (`doctor_id`, `day_of_week`, `start_time`, `end_time`, `max_appointments`, `created_at`) VALUES
(1, 'monday', '09:00:00', '17:00:00', 8, NOW()),
(1, 'tuesday', '09:00:00', '17:00:00', 8, NOW()),
(1, 'wednesday', '09:00:00', '17:00:00', 8, NOW()),
(1, 'thursday', '09:00:00', '17:00:00', 8, NOW()),
(1, 'friday', '09:00:00', '17:00:00', 8, NOW()),
(2, 'monday', '08:30:00', '16:30:00', 10, NOW()),
(2, 'tuesday', '08:30:00', '16:30:00', 10, NOW()),
(2, 'wednesday', '08:30:00', '16:30:00', 10, NOW()),
(2, 'thursday', '08:30:00', '16:30:00', 10, NOW()),
(2, 'friday', '08:30:00', '16:30:00', 10, NOW());

-- Insert system settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`) VALUES
('hospital_name', 'HealthCare+ Medical Center', 'string', 'Main hospital name', 1, NOW()),
('appointment_booking_advance_days', '30', 'integer', 'Maximum days in advance for booking', 1, NOW()),
('default_consultation_fee', '2500.00', 'string', 'Default consultation fee for new doctors', 0, NOW()),
('system_email', 'admin@healthcareplus.lk', 'string', 'System email address', 0, NOW()),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode', 0, NOW());

-- Note: Default password for all test accounts is 'test123'
-- Password hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
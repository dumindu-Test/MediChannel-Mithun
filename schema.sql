-- Healthcare+ MySQL Database Schema
-- Patient and Doctor Management System

-- Users table (for all user types)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('patient', 'doctor', 'admin', 'staff') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50) DEFAULT 'Sri Lanka',
    profile_picture TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Doctors table (extends users for doctor-specific info)
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    specialty VARCHAR(100) NOT NULL,
    subspecialties JSON,
    license_number VARCHAR(50) UNIQUE,
    experience_years INT,
    education TEXT,
    consultation_fee DECIMAL(10,2),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    bio TEXT,
    languages JSON,
    certifications JSON,
    hospital_affiliations JSON,
    services JSON,
    is_available BOOLEAN DEFAULT TRUE,
    patients_treated INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Patients table (extends users for patient-specific info)
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    medical_history TEXT,
    allergies TEXT,
    blood_type VARCHAR(5),
    insurance_provider VARCHAR(100),
    insurance_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration_minutes INT DEFAULT 30,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    reason_for_visit TEXT,
    notes TEXT,
    consultation_fee DECIMAL(10,2),
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    booking_reference VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Doctor availability table
CREATE TABLE doctor_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    day_of_week TINYINT CHECK (day_of_week BETWEEN 0 AND 6),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Appointment slots table (for specific time slots)
CREATE TABLE appointment_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    is_booked BOOLEAN DEFAULT FALSE,
    appointment_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
);

-- Medical records table
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doctor_id INT,
    appointment_id INT,
    diagnosis TEXT,
    prescription TEXT,
    lab_results TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO users (username, email, password_hash, role, first_name, last_name, phone, gender, city, country) VALUES
('dr.perera', 'dr.perera@healthcareplus.lk', '$2y$10$hashedpassword1', 'doctor', 'Dr. Saman', 'Perera', '+94771234567', 'male', 'Colombo', 'Sri Lanka'),
('dr.silva', 'dr.silva@healthcareplus.lk', '$2y$10$hashedpassword2', 'doctor', 'Dr. Kumari', 'Silva', '+94712345678', 'female', 'Kandy', 'Sri Lanka'),
('dr.fernando', 'dr.fernando@healthcareplus.lk', '$2y$10$hashedpassword3', 'doctor', 'Dr. Rohan', 'Fernando', '+94723456789', 'male', 'Galle', 'Sri Lanka'),
('dr.jayawardena', 'dr.jayawardena@healthcareplus.lk', '$2y$10$hashedpassword4', 'doctor', 'Dr. Nimal', 'Jayawardena', '+94734567890', 'male', 'Colombo', 'Sri Lanka'),
('dr.wickramasinghe', 'dr.wickramasinghe@healthcareplus.lk', '$2y$10$hashedpassword5', 'doctor', 'Dr. Chamari', 'Wickramasinghe', '+94745678901', 'female', 'Negombo', 'Sri Lanka'),
('dr.rajapaksha', 'dr.rajapaksha@healthcareplus.lk', '$2y$10$hashedpassword6', 'doctor', 'Dr. Sunil', 'Rajapaksha', '+94756789012', 'male', 'Kandy', 'Sri Lanka');

INSERT INTO doctors (user_id, specialty, subspecialties, license_number, experience_years, education, consultation_fee, rating, total_reviews, bio, languages, certifications, hospital_affiliations, services, is_available, patients_treated) VALUES
(1, 'Cardiology', '["Interventional Cardiology", "Heart Disease Prevention"]', 'SLMC-C-001', 15, 'MBBS University of Colombo, MD Cardiology', 5000.00, 4.8, 245, 'Specialized in interventional cardiology and heart disease prevention', '["English", "Sinhala", "Tamil"]', '["Board Certified Cardiologist", "Interventional Cardiology"]', '["National Hospital of Sri Lanka", "Asiri Medical Hospital"]', '["Cardiac Consultation", "Angioplasty", "Heart Disease Prevention"]', true, 2400),
(2, 'Dermatology', '["Cosmetic Dermatology", "Dermatosurgery"]', 'SLMC-D-002', 12, 'MBBS University of Peradeniya, MD Dermatology', 3500.00, 4.7, 189, 'Expert in skin disorders, cosmetic dermatology, and dermatosurgery', '["English", "Sinhala"]', '["Board Certified Dermatologist", "Cosmetic Dermatology"]', '["Teaching Hospital Kandy", "Durdans Hospital"]', '["Skin Consultation", "Cosmetic Procedures", "Dermatosurgery"]', true, 1800),
(3, 'Orthopedic Surgery', '["Joint Replacement", "Sports Medicine"]', 'SLMC-O-003', 18, 'MBBS University of Kelaniya, MS Orthopedic Surgery', 6000.00, 4.9, 312, 'Specialized in joint replacement and sports medicine', '["English", "Sinhala"]', '["Board Certified Orthopedic Surgeon", "Joint Replacement Specialist"]', '["Karapitiya Teaching Hospital", "Lanka Hospital"]', '["Orthopedic Surgery", "Joint Replacement", "Sports Injury Treatment"]', true, 2100),
(4, 'Pediatrics', '["Child Development", "Pediatric Cardiology"]', 'SLMC-P-004', 10, 'MBBS University of Colombo, MD Pediatrics', 3000.00, 4.6, 156, 'Dedicated to comprehensive child healthcare and development', '["English", "Sinhala", "Tamil"]', '["Board Certified Pediatrician", "Child Development"]', '["Lady Ridgeway Hospital", "Asiri Central Hospital"]', '["Pediatric Consultation", "Child Development", "Vaccination"]', true, 1500),
(5, 'Gynecology', '["Obstetrics", "Minimally Invasive Surgery"]', 'SLMC-G-005', 14, 'MBBS University of Peradeniya, MD Obstetrics & Gynecology', 4500.00, 4.8, 278, 'Expert in womens health, pregnancy care, and minimally invasive surgery', '["English", "Sinhala"]', '["Board Certified Gynecologist", "Laparoscopic Surgery"]', '["Castle Street Hospital", "Nawaloka Hospital"]', '["Gynecological Consultation", "Pregnancy Care", "Laparoscopic Surgery"]', true, 2200),
(6, 'General Medicine', '["Internal Medicine", "Diabetes Care"]', 'SLMC-M-006', 16, 'MBBS University of Kelaniya, MD Internal Medicine', 2500.00, 4.5, 134, 'Comprehensive primary care and internal medicine specialist', '["English", "Sinhala"]', '["Board Certified Internal Medicine", "Primary Care"]', '["Base Hospital Negombo", "Apollo Hospital"]', '["General Consultation", "Diabetes Management", "Health Checkups"]', true, 1900);
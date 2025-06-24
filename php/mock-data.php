<?php
/**
 * Mock Data Provider - Replaces database functionality
 */

class MockData {
    private static $doctors = [
        [
            'id' => 1,
            'name' => 'Dr. Saman Perera',
            'specialty' => 'Cardiology',
            'subspecialties' => ['Interventional Cardiology', 'Heart Disease Prevention'],
            'education' => 'MBBS University of Colombo, MD Cardiology',
            'experience' => 15,
            'location' => 'National Hospital of Sri Lanka, Colombo',
            'phone' => '+94771234567',
            'email' => 'dr.perera@healthcareplus.lk',
            'fee' => 5000.00,
            'rating' => 4.8,
            'reviews' => 245,
            'about' => 'Specialized in interventional cardiology and heart disease prevention',
            'services' => ['Cardiac Consultation', 'Angioplasty', 'Heart Disease Prevention'],
            'certifications' => ['Board Certified Cardiologist', 'Interventional Cardiology'],
            'languages' => ['English', 'Sinhala', 'Tamil'],
            'available' => true,
            'patients_treated' => 2400
        ],
        [
            'id' => 2,
            'name' => 'Dr. Kumari Silva',
            'specialty' => 'Dermatology',
            'subspecialties' => ['Cosmetic Dermatology', 'Dermatosurgery'],
            'education' => 'MBBS University of Peradeniya, MD Dermatology',
            'experience' => 12,
            'location' => 'Teaching Hospital Kandy, Kandy',
            'phone' => '+94712345678',
            'email' => 'dr.silva@healthcareplus.lk',
            'fee' => 3500.00,
            'rating' => 4.7,
            'reviews' => 189,
            'about' => 'Expert in skin disorders, cosmetic dermatology, and dermatosurgery',
            'services' => ['Skin Consultation', 'Cosmetic Procedures', 'Dermatosurgery'],
            'certifications' => ['Board Certified Dermatologist', 'Cosmetic Dermatology'],
            'languages' => ['English', 'Sinhala'],
            'available' => true,
            'patients_treated' => 1800
        ],
        [
            'id' => 3,
            'name' => 'Dr. Rohan Fernando',
            'specialty' => 'Orthopedic Surgery',
            'subspecialties' => ['Joint Replacement', 'Sports Medicine'],
            'education' => 'MBBS University of Kelaniya, MS Orthopedic Surgery',
            'experience' => 18,
            'location' => 'Karapitiya Teaching Hospital, Galle',
            'phone' => '+94723456789',
            'email' => 'dr.fernando@healthcareplus.lk',
            'fee' => 6000.00,
            'rating' => 4.9,
            'reviews' => 312,
            'about' => 'Specialized in joint replacement and sports medicine',
            'services' => ['Orthopedic Surgery', 'Joint Replacement', 'Sports Injury Treatment'],
            'certifications' => ['Board Certified Orthopedic Surgeon', 'Joint Replacement Specialist'],
            'languages' => ['English', 'Sinhala'],
            'available' => true,
            'patients_treated' => 2100
        ],
        [
            'id' => 4,
            'name' => 'Dr. Nimal Jayawardena',
            'specialty' => 'Pediatrics',
            'subspecialties' => ['Child Development', 'Pediatric Cardiology'],
            'education' => 'MBBS University of Colombo, MD Pediatrics',
            'experience' => 10,
            'location' => 'Lady Ridgeway Hospital, Colombo',
            'phone' => '+94734567890',
            'email' => 'dr.jayawardena@healthcareplus.lk',
            'fee' => 3000.00,
            'rating' => 4.6,
            'reviews' => 156,
            'about' => 'Dedicated to comprehensive child healthcare and development',
            'services' => ['Pediatric Consultation', 'Child Development', 'Vaccination'],
            'certifications' => ['Board Certified Pediatrician', 'Child Development'],
            'languages' => ['English', 'Sinhala', 'Tamil'],
            'available' => true,
            'patients_treated' => 1500
        ],
        [
            'id' => 5,
            'name' => 'Dr. Chamari Wickramasinghe',
            'specialty' => 'Gynecology',
            'subspecialties' => ['Obstetrics', 'Minimally Invasive Surgery'],
            'education' => 'MBBS University of Peradeniya, MD Obstetrics & Gynecology',
            'experience' => 14,
            'location' => 'Castle Street Hospital, Colombo',
            'phone' => '+94745678901',
            'email' => 'dr.wickramasinghe@healthcareplus.lk',
            'fee' => 4500.00,
            'rating' => 4.8,
            'reviews' => 278,
            'about' => 'Expert in womens health, pregnancy care, and minimally invasive surgery',
            'services' => ['Gynecological Consultation', 'Pregnancy Care', 'Laparoscopic Surgery'],
            'certifications' => ['Board Certified Gynecologist', 'Laparoscopic Surgery'],
            'languages' => ['English', 'Sinhala'],
            'available' => true,
            'patients_treated' => 2200
        ],
        [
            'id' => 6,
            'name' => 'Dr. Sunil Rajapaksha',
            'specialty' => 'General Medicine',
            'subspecialties' => ['Internal Medicine', 'Diabetes Care'],
            'education' => 'MBBS University of Kelaniya, MD Internal Medicine',
            'experience' => 16,
            'location' => 'Base Hospital Negombo, Negombo',
            'phone' => '+94756789012',
            'email' => 'dr.rajapaksha@healthcareplus.lk',
            'fee' => 2500.00,
            'rating' => 4.5,
            'reviews' => 134,
            'about' => 'Comprehensive primary care and internal medicine specialist',
            'services' => ['General Consultation', 'Diabetes Management', 'Health Checkups'],
            'certifications' => ['Board Certified Internal Medicine', 'Primary Care'],
            'languages' => ['English', 'Sinhala'],
            'available' => true,
            'patients_treated' => 1900
        ]
    ];

    public static function getDoctors($specialty = null, $limit = null) {
        $doctors = self::$doctors;
        
        if ($specialty) {
            $doctors = array_filter($doctors, function($doctor) use ($specialty) {
                return strtolower($doctor['specialty']) === strtolower($specialty);
            });
        }
        
        if ($limit) {
            $doctors = array_slice($doctors, 0, $limit);
        }
        
        return array_values($doctors);
    }
    
    public static function getDoctorById($id) {
        foreach (self::$doctors as $doctor) {
            if ($doctor['id'] == $id) {
                return $doctor;
            }
        }
        return null;
    }
    
    public static function searchDoctors($query) {
        $query = strtolower($query);
        return array_filter(self::$doctors, function($doctor) use ($query) {
            return strpos(strtolower($doctor['name']), $query) !== false ||
                   strpos(strtolower($doctor['specialty']), $query) !== false ||
                   strpos(strtolower(implode(' ', $doctor['subspecialties'])), $query) !== false;
        });
    }
}
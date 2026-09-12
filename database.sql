-- ================================================================
-- DENTAL CLINIC MANAGEMENT SYSTEM - DATABASE
-- Import this file ONCE in phpMyAdmin
-- ================================================================

CREATE DATABASE IF NOT EXISTS dental_clinic_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dental_clinic_db;

-- ----------------------------------------------------------------
-- 1. USERS  (admin, doctor, patient, receptionist)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(120) NOT NULL,
    contact    VARCHAR(30)  NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,              -- stored as a password_hash()
    role       ENUM('admin','doctor','patient','receptionist') NOT NULL DEFAULT 'patient',
    status     ENUM('active','suspended','pending') NOT NULL DEFAULT 'pending',
    
    -- Extra profile fields
    gender     ENUM('male','female','other') NULL,
    dob        DATE NULL,
    address    TEXT NULL,
    
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- 2. DOCTOR SCHEDULE  (Availability)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS schedules (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id  INT NOT NULL,
    day_of_week ENUM('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time   TIME NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- 3. APPOINTMENTS  (Bookings)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    patient_id       INT NOT NULL,
    doctor_id        INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    serial_no        INT NULL,
    status           ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method   ENUM('cash','card','mobile_banking','insurance') NULL,
    notes            TEXT,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- 4. PRESCRIPTIONS  
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS prescriptions (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    doctor_id      INT NOT NULL,
    patient_id     INT NOT NULL,
    diagnosis      TEXT,
    notes          TEXT,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prescription_medicines (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    medicine_name   VARCHAR(100) NOT NULL,
    dosage          VARCHAR(100),
    instructions    TEXT,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- 5. BILLING & INVOICES  
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bills (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    patient_id     INT NOT NULL,
    total_amount   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amount    DECIMAL(10,2) DEFAULT 0.00,
    status         ENUM('unpaid','partial','paid') DEFAULT 'unpaid',
    generated_by   INT,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- 6. EQUIPMENT INVENTORY  
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipment (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    description    TEXT,
    quantity       INT NOT NULL DEFAULT 1,
    status         ENUM('available','in_use','maintenance') DEFAULT 'available',
    assigned_to    INT NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;


-- INSERT DUMMY ADMIN
-- Password is 'admin123'
INSERT INTO users (name, email, contact, username, password, role, status) VALUES 
('System Admin', 'admin@dentalclinic.com', '01700000000', 'admin', '$2y$10$reGUYIK/SYazJmzKQ1VbtOxNE2hn9IHARrckLt5YZ/.5wGHRHyk3a', 'admin', 'active');

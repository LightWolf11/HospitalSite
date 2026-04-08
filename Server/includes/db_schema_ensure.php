<?php
declare(strict_types=1);

function db_ensure_app_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_token_hash (token_hash),
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at),
    CONSTRAINT fk_pwreset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS doctor_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(190) NOT NULL,
    specialty VARCHAR(190) NOT NULL DEFAULT \'\',
    bio TEXT,
    photo_path VARCHAR(512) NULL,
    contact_email VARCHAR(190) NOT NULL DEFAULT \'\',
    contact_phone VARCHAR(64) NOT NULL DEFAULT \'\',
    user_id INT UNSIGNED NULL UNIQUE,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_doctor_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    db_ensure_users_columns($pdo);

    try {
        $pdo->exec(
            'ALTER TABLE users ADD CONSTRAINT fk_users_doctor_profile FOREIGN KEY (doctor_profile_id) REFERENCES doctor_profiles(id) ON DELETE SET NULL'
        );
    } catch (PDOException $e) {
    }

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT,
    image_path VARCHAR(512) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_user_id INT UNSIGNED NOT NULL,
    doctor_profile_id INT UNSIGNED NOT NULL,
    scheduled_at DATETIME NOT NULL,
    status ENUM(\'pending\',\'confirmed\',\'cancelled\',\'completed\') NOT NULL DEFAULT \'pending\',
    patient_note TEXT,
    patient_arrived TINYINT(1) NOT NULL DEFAULT 0,
    doctor_comment TEXT,
    reminder_before_sent TINYINT(1) NOT NULL DEFAULT 0,
    reminder_at_sent TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_scheduled (scheduled_at),
    INDEX idx_patient (patient_user_id),
    INDEX idx_doctor (doctor_profile_id),
    CONSTRAINT fk_appt_patient FOREIGN KEY (patient_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_appt_doctor FOREIGN KEY (doctor_profile_id) REFERENCES doctor_profiles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS feedback_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    name VARCHAR(190) NOT NULL,
    email VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    admin_reply TEXT NULL,
    replied_at DATETIME NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fb_email (email),
    CONSTRAINT fk_fb_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS team_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(190) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(64) NOT NULL DEFAULT \'\',
    position VARCHAR(190) NOT NULL DEFAULT \'\',
    experience TEXT,
    message TEXT,
    cv_path VARCHAR(512) NULL,
    status VARCHAR(32) NOT NULL DEFAULT \'new\',
    admin_reply TEXT NULL,
    replied_at DATETIME NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(48) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    link VARCHAR(512) NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_unread (user_id, read_at),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    $done = true;
}

function db_ensure_users_columns(PDO $pdo): void
{
    $has = static function (string $col) use ($pdo): bool {
        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \'users\' AND COLUMN_NAME = ?'
        );
        $st->execute([$col]);

        return (int) $st->fetchColumn() > 0;
    };

    if (!$has('is_admin')) {
        try {
            $pdo->exec('ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER role');
        } catch (PDOException $e) {
        }
    }
    if (!$has('doctor_profile_id')) {
        try {
            $pdo->exec('ALTER TABLE users ADD COLUMN doctor_profile_id INT UNSIGNED NULL AFTER is_admin');
        } catch (PDOException $e) {
        }
    }
}

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,

    guest_name VARCHAR(100) NULL,
    room_number VARCHAR(20) NULL,

    food ENUM('Good','Average','Bad') NOT NULL,
    pool ENUM('Good','Average','Bad') NOT NULL,
    music ENUM('Good','Average','Bad') NOT NULL,
    wifi ENUM('Good','Average','Bad') NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_created_at (created_at)
);

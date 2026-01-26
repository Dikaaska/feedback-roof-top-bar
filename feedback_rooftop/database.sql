CREATE TABLE feedback_rooftop_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100) NULL,
    room_number VARCHAR(20) NULL,

    swimming TINYINT NOT NULL,
    food     TINYINT NOT NULL,
    beverage TINYINT NOT NULL,
    wifi     TINYINT NOT NULL,
    music    TINYINT NOT NULL,

    average DECIMAL(4,1) NOT NULL,

    note VARCHAR(10000) NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

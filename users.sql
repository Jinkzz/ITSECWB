
CREATE TABLE users (

id INT AUTO_INCREMENT PRIMARY KEY,

full_name VARCHAR(100) NOT NULL,

email VARCHAR(100) UNIQUE NOT NULL,

phone VARCHAR(20) NOT NULL,

password_hash VARCHAR(255) NOT NULL,

profile_photo VARCHAR(255),

role ENUM('user', 'admin') DEFAULT 'user',

login_attempts INT DEFAULT 0,

last_attempt DATETIME

);
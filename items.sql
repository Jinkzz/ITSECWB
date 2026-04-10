CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,      -- Text input 1
    description TEXT,                     -- Text input 2
    price DECIMAL(10, 2) NOT NULL,        -- Numeric input 1
    quantity INT NOT NULL,                -- Numeric input 2
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
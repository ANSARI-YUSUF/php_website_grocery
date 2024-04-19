<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!is_dir(__DIR__ . '/db')) {
    mkdir(__DIR__ . '/db');
}

define('DB_FILE', __DIR__ . '/db/grocery_db.db');

function my_udf_md5($string) {
    return md5($string);
}

class DBConnection extends SQLite3 {
    function __construct() {
        $this->open(DB_FILE);
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        // Create tables and triggers
        $this->createTables();
        $this->createTriggers();
        $this->insertAdminUser();
    }

    function createTables() {
        $this->exec("CREATE TABLE IF NOT EXISTS `admin_list` (
            `admin_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` TEXT NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Create other tables similarly
        // ...
    }

    function createTriggers() {
        $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_cust AFTER UPDATE ON `customer_list`
            BEGIN
                UPDATE `customer_list` SET date_updated = CURRENT_TIMESTAMP WHERE customer_id = OLD.customer_id;
            END
        ");

        // Create other triggers similarly
        // ...
    }

    function insertAdminUser() {
        $stmt = $this->prepare("INSERT OR IGNORE INTO `admin_list` VALUES (1, 'Administrator', 'admin', :password, 1, CURRENT_TIMESTAMP)");
        $password = md5('admin123');
        $stmt->bindParam(':password', $password);
        $stmt->execute();
    }

    function __destruct() {
        $this->close();
    }
}

// Instantiate DBConnection class
$conn = new DBConnection();
?>

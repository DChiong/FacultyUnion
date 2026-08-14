<?php
class Database {
    private $host = "localhost";
    private $db_name = "wmsu_union_faculty";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->ensureSiteSettingsTable();
            $this->ensurePagesTable();
            $this->ensurePostsTable();
            $this->ensureAboutTopicsTable();
            $this->ensureMenuItemsTable();
        } catch(PDOException $exception) {
            // Keep rendering alive when DB is unreachable.
            $this->conn = null;
        }
        return $this->conn;
    }

    private function ensureMenuItemsTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `menu_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `url` varchar(255) NOT NULL,
                `active_check` varchar(255) NOT NULL,
                `display_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

            $countStmt = $this->conn->query("SELECT COUNT(*) FROM menu_items");
            if ($countStmt && (int) $countStmt->fetchColumn() === 0) {
                // Seed default static items
                $default_items = [
                    ['HOME', 'index.php#home', 'index.php', 10],
                    ['ABOUT US', 'index.php#about', '', 20],
                    ['EVENTS', 'index.php#events', '', 30],
                    ['AWARDS', 'index.php#awards', '', 40],
                    ['VIDEOS', 'index.php#videos', '', 50],
                    ['CONTACT', 'index.php#footer', '', 60],
                    ['OFFICERS', 'officers.php', 'officers.php', 70]
                ];
                
                $insertStmt = $this->conn->prepare("INSERT INTO menu_items (title, url, active_check, display_order) VALUES (?, ?, ?, ?)");
                foreach ($default_items as $item) {
                    $insertStmt->execute($item);
                }
            }
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }

    private function ensureSiteSettingsTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
                `id` int(11) NOT NULL,
                `site_name` varchar(255) NOT NULL,
                `logo_path` varchar(255) NOT NULL,
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");

            $countStmt = $this->conn->query("SELECT COUNT(*) FROM site_settings WHERE id = 1");
            if ($countStmt && (int) $countStmt->fetchColumn() === 0) {
                $insertStmt = $this->conn->prepare("INSERT INTO site_settings (id, site_name, logo_path) VALUES (1, ?, ?)");
                $insertStmt->execute([
                    'Faculty Union',
                    'images/facultyunion.png'
                ]);
            }
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }

    private function ensurePagesTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `dynamic_pages` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `content` text NOT NULL,
                `image_path` varchar(255) DEFAULT NULL,
                `display_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }

    private function ensurePostsTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `dynamic_posts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `page_id` int(11) NOT NULL,
                `title` varchar(255) NOT NULL,
                `content` text NOT NULL,
                `image_path` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `dynamic_pages` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }
    private function ensureAboutTopicsTable() {
        if (!$this->conn) {
            return;
        }

        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `about_topics` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `content` text NOT NULL,
                `image_path` varchar(255) DEFAULT NULL,
                `display_order` int(11) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci");
        } catch(PDOException $exception) {
            // Leave the connection usable even if the table cannot be created.
        }
    }
}
?>
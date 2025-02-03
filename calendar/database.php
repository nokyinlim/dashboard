<?php

// stuff for creating db
class Database {
    private $db;


    public function __construct() {
        $this->db = new SQLite3('../database.db');
        $this->createTables();
    }

    private function createTables() {

        
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS periods (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                day_number INTEGER,
                week_number INTEGER,
                period_number INTEGER,
                title TEXT,
                location TEXT,
                professor TEXT,
                class TEXT
            )
        ');
        error_log("[Server] Could not find table 'periods'");
        error_log("[Server] Table 'periods' created successfully");
    }

    public function savePeriod($day, $week, $period, $title, $location, $professor, $class, $do_not_duplicate = true) {
        // Prepare the SQL statement and chekc if duplicate is here

        if ($do_not_duplicate) {
            $stmtDelete = $this->db->prepare('
                DELETE FROM periods 
                WHERE day_number = :day 
                AND week_number = :week 
                AND period_number = :period 
                AND user = :username
            ');

            $stmtDelete->bindValue(':day', $day, SQLITE3_INTEGER);
            $stmtDelete->bindValue(':week', $week, SQLITE3_INTEGER);
            $stmtDelete->bindValue(':period', $period, SQLITE3_INTEGER);
            $stmtDelete->bindValue(':username', $_SESSION['username'], SQLITE3_TEXT);

            $stmtDelete->execute();
        }




        $stmt = $this->db->prepare('
            INSERT OR REPLACE INTO periods (day_number, week_number, period_number, title, location, professor, class, user)
            VALUES (:day, :week, :period, :title, :location, :professor, :class, :username)
        ');

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->lastErrorMsg());
        }
    
        $stmt->bindValue(':day', (int)$day, SQLITE3_INTEGER);
        $stmt->bindValue(':week', (int)$week, SQLITE3_INTEGER);
        $stmt->bindValue(':period', (int)$period, SQLITE3_INTEGER);
        $stmt->bindValue(':title', $title ?: '', SQLITE3_TEXT); 
        $stmt->bindValue(':location', $location ?: '', SQLITE3_TEXT); 
        $stmt->bindValue(':professor', $professor ?: '', SQLITE3_TEXT); 
        $stmt->bindValue(':class', $class ?: '', SQLITE3_TEXT); // Defaults
        $stmt->bindValue(':username', $_SESSION['username'], SQLITE3_TEXT);
        error_log("Executing Command");
        return $stmt->execute();
    }

    public function getPeriod(int $day, int $week, int $period) {

        $stmt = $this->db->prepare('
            SELECT * FROM periods 
            WHERE day_number = :day 
            AND week_number = :week 
            AND period_number = :period
            AND user = :username
        ');
        
        $stmt->bindValue(':day', $day, SQLITE3_INTEGER);
        $stmt->bindValue(':week', $week, SQLITE3_INTEGER);
        $stmt->bindValue(':period', $period, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $_SESSION['username'], SQLITE3_TEXT);
        
        $result = $stmt->execute();

        if ($result) {
            return $result->fetchArray(SQLITE3_ASSOC);
        }
    }
}
?>
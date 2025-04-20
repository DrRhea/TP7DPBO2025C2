<?php
require_once __DIR__ . '/../config/db.php';

class FeedingLog {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllLogs() {
        $stmt = $this->db->query("SELECT fl.*, f.name AS fish_name 
                                  FROM feeding_logs fl 
                                  JOIN fish f ON fl.fish_id = f.id
                                  ORDER BY fl.feed_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchLogs($searchTerm) {
        $stmt = $this->db->prepare("SELECT fl.*, f.name AS fish_name 
                                   FROM feeding_logs fl 
                                   JOIN fish f ON fl.fish_id = f.id
                                   WHERE f.name LIKE ? OR fl.food_type LIKE ?
                                   ORDER BY fl.feed_time DESC");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchLogsByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT fl.*, f.name AS fish_name 
                                   FROM feeding_logs fl 
                                   JOIN fish f ON fl.fish_id = f.id
                                   WHERE DATE(fl.feed_time) BETWEEN ? AND ?
                                   ORDER BY fl.feed_time DESC");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogById($id) {
        $stmt = $this->db->prepare("SELECT fl.*, f.name AS fish_name 
                                   FROM feeding_logs fl 
                                   JOIN fish f ON fl.fish_id = f.id
                                   WHERE fl.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addLog($fish_id, $feeding_time, $food_type) {
        $stmt = $this->db->prepare("INSERT INTO feeding_logs (fish_id, feed_time, food_type) VALUES (?, ?, ?)");
        return $stmt->execute([$fish_id, $feeding_time, $food_type]);
    }

    public function updateLog($id, $fish_id, $feeding_time, $food_type) {
        $stmt = $this->db->prepare("UPDATE feeding_logs SET fish_id = ?, feed_time = ?, food_type = ? WHERE id = ?");
        return $stmt->execute([$fish_id, $feeding_time, $food_type, $id]);
    }

    public function deleteLog($id) {
        $stmt = $this->db->prepare("DELETE FROM feeding_logs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
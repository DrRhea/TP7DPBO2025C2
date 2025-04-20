<?php
require_once __DIR__ . '/../config/db.php';

class WaterQualityLog {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllLogs() {
        $stmt = $this->db->query("SELECT wl.*, a.name AS aquarium_name 
                                 FROM water_quality_logs wl 
                                 JOIN aquariums a ON wl.aquarium_id = a.id
                                 ORDER BY wl.check_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchLogs($searchTerm) {
        $stmt = $this->db->prepare("SELECT wl.*, a.name AS aquarium_name 
                                   FROM water_quality_logs wl 
                                   JOIN aquariums a ON wl.aquarium_id = a.id
                                   WHERE a.name LIKE ?
                                   ORDER BY wl.check_date DESC");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchLogsByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT wl.*, a.name AS aquarium_name 
                                   FROM water_quality_logs wl 
                                   JOIN aquariums a ON wl.aquarium_id = a.id
                                   WHERE DATE(wl.check_date) BETWEEN ? AND ?
                                   ORDER BY wl.check_date DESC");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogById($id) {
        $stmt = $this->db->prepare("SELECT wl.*, a.name AS aquarium_name 
                                   FROM water_quality_logs wl 
                                   JOIN aquariums a ON wl.aquarium_id = a.id
                                   WHERE wl.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addLog($aquarium_id, $check_date, $temperature, $ph, $ammonia) {
        $stmt = $this->db->prepare("INSERT INTO water_quality_logs (aquarium_id, check_date, temperature, ph, ammonia_level) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$aquarium_id, $check_date, $temperature, $ph, $ammonia]);
    }

    public function updateLog($id, $aquarium_id, $check_date, $temperature, $ph, $ammonia) {
        $stmt = $this->db->prepare("UPDATE water_quality_logs SET aquarium_id = ?, check_date = ?, temperature = ?, ph = ?, ammonia_level = ? WHERE id = ?");
        return $stmt->execute([$aquarium_id, $check_date, $temperature, $ph, $ammonia, $id]);
    }

    public function deleteLog($id) {
        $stmt = $this->db->prepare("DELETE FROM water_quality_logs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
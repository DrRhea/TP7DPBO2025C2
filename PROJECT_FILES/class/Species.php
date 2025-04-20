<?php
require_once __DIR__ . '/../config/db.php';

class Species {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllSpecies() {
        $stmt = $this->db->query("SELECT * FROM species");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchSpecies($searchTerm) {
        $stmt = $this->db->prepare("SELECT * FROM species 
                                    WHERE name LIKE ? OR origin LIKE ?");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpeciesById($id) {
        $stmt = $this->db->prepare("SELECT * FROM species WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addSpecies($name, $origin, $care_level) {
        $stmt = $this->db->prepare("INSERT INTO species (name, origin, care_level) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $origin, $care_level]);
    }

    public function updateSpecies($id, $name, $origin, $care_level) {
        $stmt = $this->db->prepare("UPDATE species SET name = ?, origin = ?, care_level = ? WHERE id = ?");
        return $stmt->execute([$name, $origin, $care_level, $id]);
    }

    public function deleteSpecies($id) {
        $stmt = $this->db->prepare("DELETE FROM species WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
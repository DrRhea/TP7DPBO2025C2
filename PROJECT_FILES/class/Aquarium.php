<?php
require_once __DIR__ . '/../config/db.php';

class Aquarium {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllAquariums() {
        $stmt = $this->db->query("SELECT * FROM aquariums");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchAquariums($searchTerm) {
        $stmt = $this->db->prepare("SELECT * FROM aquariums 
                                   WHERE name LIKE ? OR location LIKE ?");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAquariumById($id) {
        $stmt = $this->db->prepare("SELECT * FROM aquariums WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addAquarium($name, $size, $location) {
        $stmt = $this->db->prepare("INSERT INTO aquariums (name, size, location) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $size, $location]);
    }

    public function updateAquarium($id, $name, $size, $location) {
        $stmt = $this->db->prepare("UPDATE aquariums SET name = ?, size = ?, location = ? WHERE id = ?");
        return $stmt->execute([$name, $size, $location, $id]);
    }

    public function deleteAquarium($id) {
        $stmt = $this->db->prepare("DELETE FROM aquariums WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
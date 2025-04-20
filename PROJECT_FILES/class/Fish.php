<?php
require_once __DIR__ . '/../config/db.php';

class Fish {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllFish() {
        $stmt = $this->db->query("SELECT f.*, s.name AS species_name, a.name AS aquarium_name 
                                  FROM fish f 
                                  JOIN species s ON f.species_id = s.id 
                                  JOIN aquariums a ON f.aquarium_id = a.id
                                  ORDER BY f.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchFish($searchTerm) {
        $stmt = $this->db->prepare("SELECT f.*, s.name AS species_name, a.name AS aquarium_name 
                                    FROM fish f 
                                    JOIN species s ON f.species_id = s.id 
                                    JOIN aquariums a ON f.aquarium_id = a.id
                                    WHERE f.name LIKE ? OR s.name LIKE ? OR a.name LIKE ?
                                    ORDER BY f.id DESC");
        $searchPattern = "%$searchTerm%";
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFishById($id) {
        $stmt = $this->db->prepare("SELECT f.*, s.name AS species_name, a.name AS aquarium_name 
                                    FROM fish f 
                                    JOIN species s ON f.species_id = s.id 
                                    JOIN aquariums a ON f.aquarium_id = a.id
                                    WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFish($name, $species_id, $aquarium_id, $age, $gender) {
        $stmt = $this->db->prepare("INSERT INTO fish (name, species_id, aquarium_id, age, gender) 
                                    VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $species_id, $aquarium_id, $age, $gender]);
    }

    public function updateFish($id, $name, $species_id, $aquarium_id, $age, $gender) {
        $stmt = $this->db->prepare("UPDATE fish 
                                    SET name = ?, species_id = ?, aquarium_id = ?, age = ?, gender = ?
                                    WHERE id = ?");
        return $stmt->execute([$name, $species_id, $aquarium_id, $age, $gender, $id]);
    }

    public function deleteFish($id) {
        $stmt = $this->db->prepare("DELETE FROM fish WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
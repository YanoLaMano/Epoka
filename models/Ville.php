<?php
/**
 * Modèle Ville
 */
class Ville {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM ville ORDER BY nom")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ville WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function search($terme) {
        $stmt = $this->db->prepare("SELECT * FROM ville WHERE nom LIKE ? OR code_postal LIKE ? ORDER BY nom LIMIT 50");
        $terme = '%' . $terme . '%';
        $stmt->execute([$terme, $terme]);
        return $stmt->fetchAll();
    }

    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM ville")->fetchColumn();
    }
}

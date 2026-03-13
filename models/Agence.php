<?php
/**
 * Modèle Agence
 */
class Agence {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("
            SELECT a.*, v.nom AS ville_nom, v.code_postal 
            FROM agence a 
            JOIN ville v ON a.id_ville = v.id 
            ORDER BY a.id
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, v.nom AS ville_nom, v.code_postal 
            FROM agence a 
            JOIN ville v ON a.id_ville = v.id 
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($adresse, $idVille) {
        $stmt = $this->db->prepare("INSERT INTO agence (adresse, id_ville) VALUES (?, ?)");
        $stmt->execute([$adresse, $idVille]);
        return $this->db->lastInsertId();
    }

    public function update($id, $adresse, $idVille) {
        $stmt = $this->db->prepare("UPDATE agence SET adresse = ?, id_ville = ? WHERE id = ?");
        return $stmt->execute([$adresse, $idVille, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM agence WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

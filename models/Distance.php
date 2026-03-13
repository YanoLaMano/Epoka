<?php
/**
 * Modèle Distance
 */
class Distance {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("
            SELECT d.*, 
                   vd.nom AS ville_depart_nom, vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM distance d
            JOIN ville vd ON d.id_ville_depart = vd.id
            JOIN ville va ON d.id_ville_arrivee = va.id
            ORDER BY vd.nom, va.nom
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT d.*, 
                   vd.nom AS ville_depart_nom, vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM distance d
            JOIN ville vd ON d.id_ville_depart = vd.id
            JOIN ville va ON d.id_ville_arrivee = va.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getDistance($idVilleDepart, $idVilleArrivee) {
        $stmt = $this->db->prepare("
            SELECT * FROM distance 
            WHERE (id_ville_depart = ? AND id_ville_arrivee = ?)
               OR (id_ville_depart = ? AND id_ville_arrivee = ?)
        ");
        $stmt->execute([$idVilleDepart, $idVilleArrivee, $idVilleArrivee, $idVilleDepart]);
        return $stmt->fetch();
    }

    public function create($idVilleDepart, $idVilleArrivee, $km) {
        $stmt = $this->db->prepare("INSERT INTO distance (id_ville_depart, id_ville_arrivee, km) VALUES (?, ?, ?)");
        $stmt->execute([$idVilleDepart, $idVilleArrivee, $km]);
        return $this->db->lastInsertId();
    }

    public function update($id, $idVilleDepart, $idVilleArrivee, $km) {
        $stmt = $this->db->prepare("UPDATE distance SET id_ville_depart = ?, id_ville_arrivee = ?, km = ? WHERE id = ?");
        return $stmt->execute([$idVilleDepart, $idVilleArrivee, $km, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM distance WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

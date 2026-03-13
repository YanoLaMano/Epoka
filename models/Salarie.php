<?php
/**
 * Modèle Salarié
 */
class Salarie {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("
            SELECT s.*, a.adresse AS agence_adresse, v.nom AS agence_ville 
            FROM salarie s 
            LEFT JOIN agence a ON s.id_agence = a.id 
            LEFT JOIN ville v ON a.id_ville = v.id 
            ORDER BY s.nom, s.prenom
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, a.adresse AS agence_adresse, v.nom AS agence_ville 
            FROM salarie s 
            LEFT JOIN agence a ON s.id_agence = a.id 
            LEFT JOIN ville v ON a.id_ville = v.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($fonction, $nom, $prenom, $motDePasse, $peutValider, $peutPayer, $idAgence) {
        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO salarie (fonction, nom, prenom, mot_de_passe, peut_valider, peut_payer, id_agence) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$fonction, $nom, $prenom, $hash, $peutValider ? 1 : 0, $peutPayer ? 1 : 0, $idAgence]);
        return $this->db->lastInsertId();
    }

    public function update($id, $fonction, $nom, $prenom, $peutValider, $peutPayer, $idAgence, $motDePasse = null) {
        if ($motDePasse) {
            $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE salarie SET fonction = ?, nom = ?, prenom = ?, mot_de_passe = ?, peut_valider = ?, peut_payer = ?, id_agence = ? WHERE id = ?
            ");
            return $stmt->execute([$fonction, $nom, $prenom, $hash, $peutValider ? 1 : 0, $peutPayer ? 1 : 0, $idAgence, $id]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE salarie SET fonction = ?, nom = ?, prenom = ?, peut_valider = ?, peut_payer = ?, id_agence = ? WHERE id = ?
            ");
            return $stmt->execute([$fonction, $nom, $prenom, $peutValider ? 1 : 0, $peutPayer ? 1 : 0, $idAgence, $id]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM salarie WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function authenticate($id, $motDePasse) {
        $stmt = $this->db->prepare("SELECT * FROM salarie WHERE id = ?");
        $stmt->execute([(int)$id]);
        $salarie = $stmt->fetch();
        
        if ($salarie && password_verify($motDePasse, $salarie['mot_de_passe'])) {
            return $salarie;
        }
        return false;
    }
}

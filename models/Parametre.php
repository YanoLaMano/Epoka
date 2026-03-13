<?php
/**
 * Modèle Paramètre
 */
class Parametre {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function get() {
        return $this->db->query("SELECT * FROM parametre LIMIT 1")->fetch();
    }

    public function update($prixKm, $prixHebergement, $prixRepas) {
        $stmt = $this->db->prepare("UPDATE parametre SET prix_km = ?, prix_hebergement = ?, prix_repas = ? WHERE id = 1");
        return $stmt->execute([$prixKm, $prixHebergement, $prixRepas]);
    }
}

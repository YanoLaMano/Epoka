<?php
/**
 * Modèle Mission
 */
class Mission {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("
            SELECT m.*, 
                   s.nom AS salarie_nom, s.prenom AS salarie_prenom,
                   vd.nom AS ville_depart_nom, vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM mission m
            JOIN salarie s ON m.id_salarie = s.id
            JOIN ville vd ON m.id_ville_depart = vd.id
            JOIN ville va ON m.id_ville_arrivee = va.id
            ORDER BY m.date_debut DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   s.nom AS salarie_nom, s.prenom AS salarie_prenom,
                   vd.nom AS ville_depart_nom, vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM mission m
            JOIN salarie s ON m.id_salarie = s.id
            JOIN ville vd ON m.id_ville_depart = vd.id
            JOIN ville va ON m.id_ville_arrivee = va.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getBySalarie($idSalarie) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   vd.nom AS ville_depart_nom, vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM mission m
            JOIN ville vd ON m.id_ville_depart = vd.id
            JOIN ville va ON m.id_ville_arrivee = va.id
            WHERE m.id_salarie = ?
            ORDER BY m.date_debut DESC
        ");
        $stmt->execute([$idSalarie]);
        return $stmt->fetchAll();
    }

    public function create($intitule, $dateDebut, $dateFin, $idVilleDepart, $idVilleArrivee, $idSalarie, $nbRepas = 0) {
        $stmt = $this->db->prepare("
            INSERT INTO mission (intitule, date_debut, date_fin, id_ville_depart, id_ville_arrivee, id_salarie, nb_repas, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'brouillon')
        ");
        $stmt->execute([$intitule, $dateDebut, $dateFin, $idVilleDepart, $idVilleArrivee, $idSalarie, (int)$nbRepas]);
        return $this->db->lastInsertId();
    }

    public function update($id, $intitule, $dateDebut, $dateFin, $idVilleDepart, $idVilleArrivee, $nbRepas = 0) {
        $stmt = $this->db->prepare("
            UPDATE mission SET intitule = ?, date_debut = ?, date_fin = ?, id_ville_depart = ?, id_ville_arrivee = ?, nb_repas = ?
            WHERE id = ? AND statut = 'brouillon'
        ");
        return $stmt->execute([$intitule, $dateDebut, $dateFin, $idVilleDepart, $idVilleArrivee, (int)$nbRepas, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM mission WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function valider($id) {
        $stmt = $this->db->prepare("UPDATE mission SET statut = 'validee' WHERE id = ? AND statut = 'brouillon'");
        return $stmt->execute([$id]);
    }

    public function payer($id) {
        $stmt = $this->db->prepare("UPDATE mission SET statut = 'payee' WHERE id = ? AND statut = 'validee'");
        return $stmt->execute([$id]);
    }

    public function calculerFrais($idVilleDepart, $idVilleArrivee, $dateDebut, $dateFin, $db, $nbRepas = 0) {
        // Récupérer la distance
        $distanceModel = new Distance($db);
        $distance = $distanceModel->getDistance($idVilleDepart, $idVilleArrivee);
        $km = $distance ? $distance['km'] : 0;

        // Récupérer les paramètres
        $parametreModel = new Parametre($db);
        $params = $parametreModel->get();

        // Calculer le nombre de jours
        $jours = 0;
        if ($dateDebut && $dateFin) {
            $d1 = new DateTime($dateDebut);
            $d2 = new DateTime($dateFin);
            $jours = max(0, $d2->diff($d1)->days);
        }

        $fraisKm         = $km * $params['prix_km'];
        $fraisHebergement = $jours * $params['prix_hebergement'];
        $fraisRepas      = (int)$nbRepas * $params['prix_repas'];

        return [
            'km'               => $km,
            'jours'            => $jours,
            'nb_repas'         => (int)$nbRepas,
            'prix_km'          => $params['prix_km'],
            'prix_hebergement' => $params['prix_hebergement'],
            'prix_repas'       => $params['prix_repas'],
            'frais_km'         => $fraisKm,
            'frais_hebergement'=> $fraisHebergement,
            'frais_repas'      => $fraisRepas,
            'total'            => $fraisKm + $fraisHebergement + $fraisRepas
        ];
    }
}

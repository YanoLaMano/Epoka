<?php
/**
 * Contrôleur Paramètre
 */
class ParametreController {
    private $db;
    private $parametreModel;

    public function __construct($db) {
        $this->db = $db;
        $this->parametreModel = new Parametre($db);
    }

    public function index() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('dashboard');
            return;
        }

        $parametre = $this->parametreModel->get();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->parametreModel->update($_POST['prix_km'], $_POST['prix_hebergement'], $_POST['prix_repas']);
            flash('Paramètres mis à jour avec succès.');
            redirect('parametres');
            return;
        }

        require __DIR__ . '/../views/parametres/index.php';
    }
}

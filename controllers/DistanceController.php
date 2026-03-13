<?php
/**
 * Contrôleur Distance
 */
class DistanceController {
    private $db;
    private $distanceModel;
    private $villeModel;

    public function __construct($db) {
        $this->db = $db;
        $this->distanceModel = new Distance($db);
        $this->villeModel = new Ville($db);
    }

    public function index() {
        requireLogin();
        $distances = $this->distanceModel->getAll();
        require __DIR__ . '/../views/distances/index.php';
    }

    public function create() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('distances');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->distanceModel->create(
                $_POST['id_ville_depart'],
                $_POST['id_ville_arrivee'],
                $_POST['km']
            );
            flash('Distance créée avec succès.');
            redirect('distances');
            return;
        }

        require __DIR__ . '/../views/distances/form.php';
    }

    public function edit() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('distances');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $distance = $this->distanceModel->getById($id);
        if (!$distance) {
            flash('Distance non trouvée.', 'error');
            redirect('distances');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->distanceModel->update($id, $_POST['id_ville_depart'], $_POST['id_ville_arrivee'], $_POST['km']);
            flash('Distance mise à jour avec succès.');
            redirect('distances');
            return;
        }

        require __DIR__ . '/../views/distances/form.php';
    }

    public function delete() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('distances');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $this->distanceModel->delete($id);
        flash('Distance supprimée.');
        redirect('distances');
    }
}

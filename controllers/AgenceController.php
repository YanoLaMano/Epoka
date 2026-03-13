<?php
/**
 * Contrôleur Agence
 */
class AgenceController {
    private $db;
    private $agenceModel;
    private $villeModel;

    public function __construct($db) {
        $this->db = $db;
        $this->agenceModel = new Agence($db);
        $this->villeModel = new Ville($db);
    }

    public function index() {
        requireLogin();
        $agences = $this->agenceModel->getAll();
        require __DIR__ . '/../views/agences/index.php';
    }

    public function create() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('agences');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->agenceModel->create($_POST['adresse'], $_POST['id_ville']);
            flash('Agence créée avec succès.');
            redirect('agences');
            return;
        }

        require __DIR__ . '/../views/agences/form.php';
    }

    public function edit() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('agences');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $agence = $this->agenceModel->getById($id);
        if (!$agence) {
            flash('Agence non trouvée.', 'error');
            redirect('agences');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->agenceModel->update($id, $_POST['adresse'], $_POST['id_ville']);
            flash('Agence mise à jour avec succès.');
            redirect('agences');
            return;
        }

        require __DIR__ . '/../views/agences/form.php';
    }

    public function delete() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('agences');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $this->agenceModel->delete($id);
        flash('Agence supprimée.');
        redirect('agences');
    }
}

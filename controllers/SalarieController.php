<?php
/**
 * Contrôleur Salarié
 */
class SalarieController {
    private $db;
    private $salarieModel;
    private $agenceModel;

    public function __construct($db) {
        $this->db = $db;
        $this->salarieModel = new Salarie($db);
        $this->agenceModel = new Agence($db);
    }

    public function index() {
        requireLogin();
        $salaries = $this->salarieModel->getAll();
        require __DIR__ . '/../views/salaries/index.php';
    }

    public function create() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('salaries');
            return;
        }

        $agences = $this->agenceModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->salarieModel->create(
                $_POST['fonction'],
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['mot_de_passe'],
                isset($_POST['peut_valider']) ? 1 : 0,
                isset($_POST['peut_payer']) ? 1 : 0,
                $_POST['id_agence']
            );
            flash('Salarié créé avec succès.');
            redirect('salaries');
            return;
        }

        require __DIR__ . '/../views/salaries/form.php';
    }

    public function edit() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('salaries');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $salarie = $this->salarieModel->getById($id);
        if (!$salarie) {
            flash('Salarié non trouvé.', 'error');
            redirect('salaries');
            return;
        }

        $agences = $this->agenceModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motDePasse = !empty($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : null;
            $this->salarieModel->update(
                $id,
                $_POST['fonction'],
                $_POST['nom'],
                $_POST['prenom'],
                isset($_POST['peut_valider']) ? 1 : 0,
                isset($_POST['peut_payer']) ? 1 : 0,
                $_POST['id_agence'],
                $motDePasse
            );
            flash('Salarié mis à jour avec succès.');
            redirect('salaries');
            return;
        }

        require __DIR__ . '/../views/salaries/form.php';
    }

    public function delete() {
        requireLogin();
        if (!isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('salaries');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $this->salarieModel->delete($id);
        flash('Salarié supprimé.');
        redirect('salaries');
    }
}

<?php
/**
 * Contrôleur Mission
 */
class MissionController
{
    private $db;
    private $missionModel;
    private $villeModel;
    private $salarieModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->missionModel = new Mission($db);
        $this->villeModel = new Ville($db);
        $this->salarieModel = new Salarie($db);
    }

    /** Crée ou met à jour la distance entre deux villes si un km valide est fourni */
    private function saveKm($idVilleDepart, $idVilleArrivee, $km)
    {
        if ($km <= 0) return;
        $distModel = new Distance($this->db);
        $existing  = $distModel->getDistance($idVilleDepart, $idVilleArrivee);
        if (!$existing) {
            $distModel->create($idVilleDepart, $idVilleArrivee, $km);
        } elseif ((int)$existing['km'] !== $km) {
            $distModel->update($existing['id'], $existing['id_ville_depart'], $existing['id_ville_arrivee'], $km);
        }
    }

    public function index()
    {
        requireLogin();
        if (isAdmin() || canValidate() || canPay()) {
            $missions = $this->missionModel->getAll();
        }
        else {
            $missions = $this->missionModel->getBySalarie($_SESSION['salarie_id']);
        }
        require __DIR__ . '/../views/missions/index.php';
    }

    public function create()
    {
        requireLogin();
        $salaries = $this->salarieModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idSalarie = isAdmin() ? $_POST['id_salarie'] : $_SESSION['salarie_id'];
            $id_ville_depart = trim($_POST['id_ville_depart'] ?? '');
            $id_ville_arrivee = trim($_POST['id_ville_arrivee'] ?? '');

            if (empty($id_ville_depart) || empty($id_ville_arrivee)) {
                flash('Erreur : Veuillez sélectionner la ville de départ et d\'arrivée depuis la liste de suggestions.', 'error');
                require __DIR__ . '/../views/missions/form.php';
                return;
            }

            $this->missionModel->create(
                $_POST['intitule'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $id_ville_depart,
                $id_ville_arrivee,
                $idSalarie,
                (int)($_POST['nb_repas'] ?? 0)
            );
            $this->saveKm($id_ville_depart, $id_ville_arrivee, (int)($_POST['km'] ?? 0));
            flash('Mission créée avec succès.');
            redirect('missions');
            return;
        }

        require __DIR__ . '/../views/missions/form.php';
    }

    public function edit()
    {
        requireLogin();
        $id = $_GET['id'] ?? 0;
        $mission = $this->missionModel->getById($id);
        if (!$mission) {
            flash('Mission non trouvée.', 'error');
            redirect('missions');
            return;
        }

        // Seul le salarié concerné ou un admin peut modifier
        if ($mission['id_salarie'] != $_SESSION['salarie_id'] && !isAdmin()) {
            flash('Accès refusé.', 'error');
            redirect('missions');
            return;
        }

        if ($mission['statut'] !== 'brouillon') {
            flash('Impossible de modifier une mission déjà validée ou payée.', 'error');
            redirect('missions');
            return;
        }

        $salaries = $this->salarieModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ville_depart = trim($_POST['id_ville_depart'] ?? '');
            $id_ville_arrivee = trim($_POST['id_ville_arrivee'] ?? '');

            if (empty($id_ville_depart) || empty($id_ville_arrivee)) {
                flash('Erreur : Veuillez sélectionner la ville de départ et d\'arrivée depuis la liste de suggestions.', 'error');
                $isEdit = true;
                $currentKm = null;
                require __DIR__ . '/../views/missions/form.php';
                return;
            }

            $this->missionModel->update(
                $id,
                $_POST['intitule'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $id_ville_depart,
                $id_ville_arrivee,
                (int)($_POST['nb_repas'] ?? 0)
            );
            $this->saveKm($id_ville_depart, $id_ville_arrivee, (int)($_POST['km'] ?? 0));
            flash('Mission mise à jour avec succès.');
            redirect('missions');
            return;
        }

        // Pré-remplir le km existant pour ce trajet
        $distModel = new Distance($this->db);
        $existingDist = $distModel->getDistance($mission['id_ville_depart'], $mission['id_ville_arrivee']);
        $currentKm = $existingDist ? (int)$existingDist['km'] : null;

        require __DIR__ . '/../views/missions/form.php';
    }

    public function delete()
    {
        requireLogin();
        $id = $_GET['id'] ?? 0;
        $mission = $this->missionModel->getById($id);
        if ($mission && ($mission['id_salarie'] == $_SESSION['salarie_id'] || isAdmin())) {
            $this->missionModel->delete($id);
            flash('Mission supprimée.');
        }
        else {
            flash('Accès refusé.', 'error');
        }
        redirect('missions');
    }

    public function valider()
    {
        requireLogin();
        if (!canValidate()) {
            flash('Vous n\'avez pas le droit de valider les missions.', 'error');
            redirect('missions');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $this->missionModel->valider($id);
        flash('Mission validée avec succès.', 'success_confetti');
        redirect('missions');
    }

    public function payer()
    {
        requireLogin();
        if (!canPay()) {
            flash('Vous n\'avez pas le droit de payer les missions.', 'error');
            redirect('missions');
            return;
        }

        $id = $_GET['id'] ?? 0;
        $this->missionModel->payer($id);
        flash('Mission marquée comme payée.');
        redirect('missions');
    }

    public function voir()
    {
        requireLogin();
        $id = $_GET['id'] ?? 0;
        $mission = $this->missionModel->getById($id);
        if (!$mission) {
            flash('Mission non trouvée.', 'error');
            redirect('missions');
            return;
        }

        $frais = $this->missionModel->calculerFrais(
            $mission['id_ville_depart'],
            $mission['id_ville_arrivee'],
            $mission['date_debut'],
            $mission['date_fin'],
            $this->db,
            $mission['nb_repas'] ?? 0
        );

        require __DIR__ . '/../views/missions/detail.php';
    }
}

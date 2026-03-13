<?php
/**
 * Contrôleur d'authentification
 */
class AuthController {
    private $db;
    private $salarieModel;

    public function __construct($db) {
        $this->db = $db;
        $this->salarieModel = new Salarie($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            $salarie = $this->salarieModel->authenticate($id, $motDePasse);
            if ($salarie) {
                $_SESSION['salarie_id'] = $salarie['id'];
                $_SESSION['salarie_nom'] = $salarie['nom'];
                $_SESSION['salarie_prenom'] = $salarie['prenom'];
                $_SESSION['salarie_fonction'] = $salarie['fonction'];
                $_SESSION['peut_valider'] = $salarie['peut_valider'];
                $_SESSION['peut_payer'] = $salarie['peut_payer'];
                flash('Bienvenue ' . escape($salarie['prenom']) . ' !');
                redirect('dashboard');
            } else {
                flash('Identifiants incorrects.', 'error');
            }
        }
        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        session_destroy();
        session_start();
        flash('Vous avez été déconnecté.');
        redirect('login');
    }
}

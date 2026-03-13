<?php
/**
 * Point d'entrée de l'application Epoka
 * Routeur principal
 */
session_start();

// Charger la configuration et les dépendances
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/Ville.php';
require_once __DIR__ . '/models/Agence.php';
require_once __DIR__ . '/models/Salarie.php';
require_once __DIR__ . '/models/Mission.php';
require_once __DIR__ . '/models/Distance.php';
require_once __DIR__ . '/models/Parametre.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/SalarieController.php';
require_once __DIR__ . '/controllers/AgenceController.php';
require_once __DIR__ . '/controllers/MissionController.php';
require_once __DIR__ . '/controllers/DistanceController.php';
require_once __DIR__ . '/controllers/ParametreController.php';

// Connexion à la BDD
$db = getDatabase();

// API upload avatar
if (isset($_GET['api']) && $_GET['api'] === 'upload_avatar') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['salarie_id'])) {
        echo json_encode(['success' => false, 'error' => 'Non connecté']); exit;
    }
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'Fichier invalide']); exit;
    }
    $file = $_FILES['avatar'];
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Fichier trop lourd (max 2 Mo)']); exit;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($allowed[$mime])) {
        echo json_encode(['success' => false, 'error' => 'Format non supporté (jpg/png/webp/gif)']); exit;
    }
    $dir = __DIR__ . '/assets/img/avatars/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    foreach (glob($dir . $_SESSION['salarie_id'] . '.*') as $old) @unlink($old);
    $filename = $_SESSION['salarie_id'] . '.' . $allowed[$mime];
    if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        echo json_encode(['success' => true, 'url' => 'assets/img/avatars/' . $filename . '?v=' . time()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Sauvegarde échouée']);
    }
    exit;
}

// API get_distance — retourne les km entre deux villes
if (isset($_GET['api']) && $_GET['api'] === 'get_distance') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['salarie_id'])) { echo json_encode(['km' => null]); exit; }
    $idDepart  = (int)($_GET['id_depart']  ?? 0);
    $idArrivee = (int)($_GET['id_arrivee'] ?? 0);
    if ($idDepart && $idArrivee) {
        $distModel = new Distance($db);
        $dist = $distModel->getDistance($idDepart, $idArrivee);
        echo json_encode(['km' => $dist ? (int)$dist['km'] : null]);
    } else {
        echo json_encode(['km' => null]);
    }
    exit;
}

// API pour la recherche de villes (AJAX)
if (isset($_GET['api']) && $_GET['api'] === 'search_villes') {
    header('Content-Type: application/json');
    $villeModel = new Ville($db);
    $terme = $_GET['terme'] ?? '';
    if (strlen($terme) >= 2) {
        $villes = $villeModel->search($terme);
    } else {
        $villes = [];
    }
    echo json_encode($villes, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// Routage
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'login':
        $controller = new AuthController($db);
        $controller->login();
        break;

    case 'logout':
        $controller = new AuthController($db);
        $controller->logout();
        break;

    case 'dashboard':
        requireLogin();
        $missionModel = new Mission($db);
        $salarieModel = new Salarie($db);
        $agenceModel = new Agence($db);
        
        if (isAdmin() || canValidate() || canPay()) {
            $missions = $missionModel->getAll();
        } else {
            $missions = $missionModel->getBySalarie($_SESSION['salarie_id']);
        }
        // Missions personnelles de l'utilisateur connecté (tous rôles)
        $missionsDuSalarie = array_values(array_filter($missions, fn($m) => (int)$m['id_salarie'] === (int)$_SESSION['salarie_id']));

        $stats = [
            'total' => count($missions),
            'brouillon' => count(array_filter($missions, fn($m) => $m['statut'] === 'brouillon')),
            'validee' => count(array_filter($missions, fn($m) => $m['statut'] === 'validee')),
            'payee' => count(array_filter($missions, fn($m) => $m['statut'] === 'payee'))
        ];
        $missionsAValider = array_values(array_filter($missions, fn($m) => $m['statut'] === 'brouillon'));
        
        require __DIR__ . '/views/dashboard.php';
        break;

    case 'salaries':
        $controller = new SalarieController($db);
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit(); break;
            case 'delete': $controller->delete(); break;
            default: $controller->index(); break;
        }
        break;

    case 'agences':
        $controller = new AgenceController($db);
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit(); break;
            case 'delete': $controller->delete(); break;
            default: $controller->index(); break;
        }
        break;

    case 'missions':
        $controller = new MissionController($db);
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit(); break;
            case 'delete': $controller->delete(); break;
            case 'valider': $controller->valider(); break;
            case 'payer': $controller->payer(); break;
            case 'voir': $controller->voir(); break;
            default: $controller->index(); break;
        }
        break;

    case 'distances':
        $controller = new DistanceController($db);
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'edit': $controller->edit(); break;
            case 'delete': $controller->delete(); break;
            default: $controller->index(); break;
        }
        break;

    case 'parametres':
        $controller = new ParametreController($db);
        $controller->index();
        break;

    default:
        redirect('login');
        break;
}

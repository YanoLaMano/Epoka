<?php
/**
 * POST /api/auth.php
 * Body JSON : { "id": 1, "mot_de_passe": "admin" }
 * Retourne : { "success": true, "salarie": { ... } }
 */
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Méthode non autorisée'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$id       = $body['id'] ?? null;
$password = $body['mot_de_passe'] ?? null;

if (!$id || !$password) {
    json_out(['error' => 'Identifiant et mot de passe requis'], 400);
}

$db   = getDb();
$stmt = $db->prepare("
    SELECT s.*, a.id_ville AS id_ville_agence
    FROM salarie s
    LEFT JOIN agence a ON s.id_agence = a.id
    WHERE s.id = ?
");
$stmt->execute([$id]);
$salarie = $stmt->fetch();

if (!$salarie || !password_verify($password, $salarie['mot_de_passe'])) {
    json_out(['error' => 'Identifiant ou mot de passe incorrect'], 401);
}

unset($salarie['mot_de_passe']);
json_out(['success' => true, 'salarie' => $salarie]);

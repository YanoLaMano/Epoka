<?php
/**
 * GET  /api/missions.php?id_salarie=X  → liste des missions d'un salarié
 * GET  /api/missions.php               → toutes les missions
 * POST /api/missions.php               → créer une mission
 * DELETE /api/missions.php?id=X        → supprimer une mission brouillon
 */
require_once 'config.php';

$db     = getDb();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $idSalarie = $_GET['id_salarie'] ?? null;

    if ($idSalarie) {
        $stmt = $db->prepare("
            SELECT m.*,
                   vd.nom AS ville_depart_nom,  vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM mission m
            JOIN ville vd ON m.id_ville_depart  = vd.id
            JOIN ville va ON m.id_ville_arrivee = va.id
            WHERE m.id_salarie = ?
            ORDER BY m.date_debut DESC
        ");
        $stmt->execute([$idSalarie]);
    } else {
        $stmt = $db->query("
            SELECT m.*,
                   s.nom AS salarie_nom, s.prenom AS salarie_prenom,
                   vd.nom AS ville_depart_nom,  vd.code_postal AS cp_depart,
                   va.nom AS ville_arrivee_nom, va.code_postal AS cp_arrivee
            FROM mission m
            JOIN salarie s ON m.id_salarie = s.id
            JOIN ville vd ON m.id_ville_depart  = vd.id
            JOIN ville va ON m.id_ville_arrivee = va.id
            ORDER BY m.date_debut DESC
        ");
    }
    json_out($stmt->fetchAll());

} elseif ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    foreach (['date_debut','date_fin','id_ville_depart','id_ville_arrivee','id_salarie'] as $f) {
        if (empty($body[$f])) json_out(['error' => "Champ requis : $f"], 400);
    }
    // Intitulé auto-généré si non fourni (saisie Android simplifiée)
    if (empty($body['intitule'])) {
        $body['intitule'] = "Mission du " . $body['date_debut'];
    }
    if ($body['date_debut'] > $body['date_fin']) {
        json_out(['error' => 'La date de début doit être avant la date de fin'], 400);
    }

    $stmt = $db->prepare("
        INSERT INTO mission (intitule, date_debut, date_fin, id_ville_depart, id_ville_arrivee, id_salarie, nb_repas, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'brouillon')
    ");
    $stmt->execute([
        $body['intitule'],
        $body['date_debut'],
        $body['date_fin'],
        (int)$body['id_ville_depart'],
        (int)$body['id_ville_arrivee'],
        (int)$body['id_salarie'],
        (int)($body['nb_repas'] ?? 0),
    ]);
    json_out(['success' => true, 'id' => (int)$db->lastInsertId()], 201);

} elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) json_out(['error' => 'ID manquant'], 400);

    $stmt = $db->prepare("DELETE FROM mission WHERE id = ? AND statut = 'brouillon'");
    $stmt->execute([$id]);
    json_out(['success' => true, 'deleted' => $stmt->rowCount()]);

} else {
    json_out(['error' => 'Méthode non autorisée'], 405);
}

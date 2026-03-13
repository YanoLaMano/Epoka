<?php
/**
 * GET /api/villes.php → liste de toutes les villes
 */
require_once 'config.php';

$db   = getDb();
$stmt = $db->query("SELECT id, nom, code_postal FROM ville ORDER BY nom ASC");
json_out($stmt->fetchAll());

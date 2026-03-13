<?php
require __DIR__ . '/config/database.php';
$db = getDatabase();

echo "=== Test Epoka DB ===\n";

$count = $db->query("SELECT COUNT(*) FROM ville")->fetchColumn();
echo "Total villes: $count\n";

$stmt = $db->query("SELECT * FROM ville WHERE nom LIKE '%Lyon%' LIMIT 5");
$results = $stmt->fetchAll();
echo "Recherche 'Lyon': " . count($results) . " résultat(s)\n";
foreach ($results as $r) {
    echo "  - #{$r['id']} {$r['nom']} ({$r['code_postal']})\n";
}

$stmt = $db->query("SELECT * FROM ville WHERE nom LIKE '%Paris%' LIMIT 5");
$results = $stmt->fetchAll();
echo "Recherche 'Paris': " . count($results) . " résultat(s)\n";
foreach ($results as $r) {
    echo "  - #{$r['id']} {$r['nom']} ({$r['code_postal']})\n";
}

$count = $db->query("SELECT COUNT(*) FROM salarie")->fetchColumn();
echo "Total salariés: $count\n";

$count = $db->query("SELECT COUNT(*) FROM agence")->fetchColumn();
echo "Total agences: $count\n";

$params = $db->query("SELECT * FROM parametre LIMIT 1")->fetch();
echo "Prix/km: {$params['prix_km']} €, Hébergement: {$params['prix_hebergement']} €/jour\n";

echo "\n=== OK ===\n";

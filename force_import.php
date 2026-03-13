<?php
require_once __DIR__ . '/config/database.php';

echo "Connexion à la base de données...\n";
$db = getDatabase();

echo "Début de l'importation des villes depuis 'Communes de France.txt'...\n";

$fichier = __DIR__ . '/Communes de France.txt';
if (!file_exists($fichier)) {
    die("Erreur : Le fichier '$fichier' est introuvable.\n");
}

$count = $db->query("SELECT COUNT(*) FROM ville")->fetchColumn();
if ($count > 1) {
    echo "Il y a déjà $count villes dans la base de données. L'importation n'est pas nécessaire.\n";
    exit;
}

$db->beginTransaction();
try {
    $handle = fopen($fichier, 'r');
    $header = fgets($handle); // Ignorer la 1ère ligne
    
    $stmt = $db->prepare("INSERT INTO ville (code_postal, nom) VALUES (?, ?)");
    $i = 0;
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
        
        $parts = explode("\t", $line);
        if (count($parts) >= 2) {
            $stmt->execute([trim($parts[0]), trim($parts[1])]);
            $i++;
        }
    }
    fclose($handle);
    $db->commit();
    echo "Succès : $i villes ont été importées dans la base de données.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Erreur lors de l'importation : " . $e->getMessage() . "\n";
}

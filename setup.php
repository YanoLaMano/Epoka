<?php
require_once __DIR__ . '/config/database.php';
$db = getDatabase();

echo "--- GESTION DES ADMINISTRATEURS ---\n";

// 1. Modifier le mot de passe de l'Admin existant
$mdp_admin = password_hash('admin', PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE salarie SET mot_de_passe = ? WHERE nom = 'Admin'");
$stmt->execute([$mdp_admin]);
echo "Le mot de passe de l'utilisateur d'origine (Nom: Admin) a été réinitialisé à 'admin'.\n";

// 2. Créer un NOUVEL admin ("admin2" avec mot de passe "admin2")
$mdp2 = password_hash('admin2', PASSWORD_DEFAULT);
$stmt = $db->prepare("SELECT id FROM salarie WHERE nom = 'admin2'");
$stmt->execute();
if (!$stmt->fetch()) {
    $stmt = $db->prepare("INSERT INTO salarie (fonction, nom, prenom, mot_de_passe, peut_valider, peut_payer, id_agence) VALUES ('Administrateur', 'admin2', 'admin2', ?, 1, 1, 1)");
    $stmt->execute([$mdp2]);
    echo "Nouvel utilisateur administrateur (Nom: admin2, Mot de passe: admin2) créé avec succès.\n";
}
else {
    $stmt = $db->prepare("UPDATE salarie SET mot_de_passe = ? WHERE nom = 'admin2'");
    $stmt->execute([$mdp2]);
    echo "Le mot de passe de 'admin2' a été réinitialisé à 'admin2'.\n";
}


echo "\n--- IMPORTATION DES COMMUNES ---\n";
// Vérifier s'il faut purger les villes existantes (sauf la ville par défaut avec id=1)
// Ensuite on importe depuis le fichier
$db->exec("DELETE FROM ville WHERE id > 1");

$fichier = __DIR__ . '/Communes de France.txt';
if (file_exists($fichier)) {
    $handle = fopen($fichier, 'r');
    $header = fgets($handle); // Ignorer la 1ère ligne

    $stmt = $db->prepare("INSERT INTO ville (code_postal, nom) VALUES (?, ?)");
    $i = 0;

    $db->beginTransaction();
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if (empty($line))
            continue;

        $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
        $parts = explode("\t", $line);
        if (count($parts) >= 2) {
            $stmt->execute([trim($parts[0]), trim($parts[1])]);
            $i++;
        }
    }
    $db->commit();
    fclose($handle);
    echo "$i communes importées avec succès dans la table ville.\n";
}
else {
    echo "Le fichier $fichier n'existe pas.\n";
}

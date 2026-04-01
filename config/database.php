<?php
/**
 * Script de connexion à MySQL pour Epoka via PDO
 */

function getDatabase() {
    static $db = null;
    if ($db === null) {
        $host = '127.0.0.1';
        $dbname = 'epoka';
        $username = 'root'; // Identifiant par défaut de WampServer
        $password = 'root'; // Mot de passe root sous WampServer (Windows)
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $db = new PDO($dsn, $username, $password, $options);
            
            // Vérifier si la base est vide pour insérer les données initiales
            initDatabase($db);
            migrateDatabase($db);
            
        } catch (\PDOException $e) {
            die("<div style='margin: 50px auto; width: 600px; padding: 20px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; font-family: sans-serif;'>
                 <h2 style='color: #b91c1c; margin-top: 0;'>Erreur de connexion à la base de données</h2>
                 <p>Impossible de se connecter à la base MySQL <strong>$dbname</strong>.</p>
                 <p>Message d'erreur : <em>" . htmlspecialchars($e->getMessage()) . "</em></p>
                 <hr style='border: 0; height: 1px; background: #fca5a5; margin: 15px 0;'>
                 <h3>Comment corriger ce problème avec WampServer :</h3>
                 <ol>
                    <li>Vérifiez que WampServer est bien lancé (icône verte).</li>
                    <li>Ouvrez <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a> (utilisateur: <strong>root</strong>, sans mot de passe).</li>
                    <li>Cliquez sur l'onglet <strong>Bases de données</strong>.</li>
                    <li>Dans 'Créer une base de données', tapez <strong>epoka</strong>, choisissez l'interclassement <strong>utf8mb4_unicode_ci</strong>, puis cliquez sur Créer.</li>
                    <li>Rechargez cette page, l'application créera automatiquement les tables !</li>
                 </ol>
                 </div>");
        }
    }
    return $db;
}

function migrateDatabase($db) {
    // Ajouter prix_repas à parametre si absent
    $col = $db->query("SHOW COLUMNS FROM parametre LIKE 'prix_repas'")->rowCount();
    if ($col === 0) {
        $db->exec("ALTER TABLE parametre ADD COLUMN prix_repas DECIMAL(10,2) NOT NULL DEFAULT 15.00");
        $db->exec("UPDATE parametre SET prix_repas = 15.00 WHERE id = 1");
    }
    // Ajouter nb_repas à mission si absent
    $col2 = $db->query("SHOW COLUMNS FROM mission LIKE 'nb_repas'")->rowCount();
    if ($col2 === 0) {
        $db->exec("ALTER TABLE mission ADD COLUMN nb_repas INT NOT NULL DEFAULT 0");
    }
}

function initDatabase($db) {
    // Vérifier si la table parametre existe
    $stmt = $db->query("SHOW TABLES LIKE 'parametre'");
    if ($stmt->rowCount() > 0) {
        // La structure semble déjà en place
        return;
    }

    // --- CRÉATION DE LA STRUCTURE MYSQL ---

    // Table VILLE
    $db->exec("CREATE TABLE IF NOT EXISTS ville (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        code_postal VARCHAR(10) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Table AGENCE
    $db->exec("CREATE TABLE IF NOT EXISTS agence (
        id INT AUTO_INCREMENT PRIMARY KEY,
        adresse VARCHAR(255) NOT NULL,
        id_ville INT NOT NULL,
        FOREIGN KEY (id_ville) REFERENCES ville(id) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Table SALARIE
    $db->exec("CREATE TABLE IF NOT EXISTS salarie (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fonction VARCHAR(100) NOT NULL,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        peut_valider TINYINT(1) NOT NULL DEFAULT 0,
        peut_payer TINYINT(1) NOT NULL DEFAULT 0,
        id_agence INT,
        FOREIGN KEY (id_agence) REFERENCES agence(id) ON UPDATE CASCADE ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Table MISSION
    $db->exec("CREATE TABLE IF NOT EXISTS mission (
        id INT AUTO_INCREMENT PRIMARY KEY,
        intitule VARCHAR(255) NOT NULL,
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        id_ville_depart INT NOT NULL,
        id_ville_arrivee INT NOT NULL,
        id_salarie INT NOT NULL,
        statut ENUM('brouillon', 'validee', 'payee') NOT NULL DEFAULT 'brouillon',
        FOREIGN KEY (id_ville_depart) REFERENCES ville(id) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (id_ville_arrivee) REFERENCES ville(id) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (id_salarie) REFERENCES salarie(id) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Table DISTANCE
    $db->exec("CREATE TABLE IF NOT EXISTS distance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_ville_depart INT NOT NULL,
        id_ville_arrivee INT NOT NULL,
        km INT NOT NULL,
        FOREIGN KEY (id_ville_depart) REFERENCES ville(id) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (id_ville_arrivee) REFERENCES ville(id) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Table PARAMETRE
    $db->exec("CREATE TABLE IF NOT EXISTS parametre (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prix_km DECIMAL(10,2) NOT NULL DEFAULT 0.50,
        prix_hebergement DECIMAL(10,2) NOT NULL DEFAULT 80.00
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // --- INSERTION DES DONNÉES PAR DÉFAUT ---

    $db->exec("INSERT INTO parametre (prix_km, prix_hebergement) VALUES (0.50, 80.00)");
    
    // Créer une ville et une agence par défaut
    $db->exec("INSERT INTO ville (id, nom, code_postal) VALUES (1, 'Paris', '75000')");
    $db->exec("INSERT INTO agence (id, adresse, id_ville) VALUES (1, 'Siège social', 1)");
    
    // Insérer l'admin
    $hash = password_hash('admin', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO salarie (fonction, nom, prenom, mot_de_passe, peut_valider, peut_payer, id_agence) VALUES ('Administrateur', 'Admin', 'Admin', ?, 1, 1, 1)");
    $stmt->execute([$hash]);

    // Lancer l'importation des villes
    importVilles($db);
}

function importVilles($db) {
    // Si on a déjà plus d'une ville (Paris), on ignore
    $count = $db->query("SELECT COUNT(*) FROM ville")->fetchColumn();
    if ($count > 1) return;

    $fichier = __DIR__ . '/../Communes de France.txt';
    if (!file_exists($fichier)) return;

    $db->beginTransaction();
    try {
        $handle = fopen($fichier, 'r');
        $header = fgets($handle); // Ignorer la 1ère ligne
        
        $stmt = $db->prepare("INSERT INTO ville (code_postal, nom) VALUES (?, ?)");
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Convertir Windows-1252 vers UTF-8 pour WampServer
            $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
            
            $parts = explode("\t", $line);
            if (count($parts) >= 2) {
                $stmt->execute([trim($parts[0]), trim($parts[1])]);
            }
        }
        fclose($handle);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
    }
}

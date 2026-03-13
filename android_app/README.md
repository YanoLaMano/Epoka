# Epoka — Application Android

Application Android pour la gestion de missions, reliée à la base de données MySQL du site Epoka.

## Architecture

```
Site web (WAMP)          Android App
     │                        │
     ├── index.php             ├── LoginActivity
     ├── api/                  ├── MainActivity (liste missions)
     │   ├── auth.php  ◄───────┤── CreateMissionActivity
     │   ├── missions.php      └── (Retrofit → OkHttp → JSON)
     │   └── villes.php
     └── BDD MySQL (epoka)
```

## Installation

### 1. API PHP (côté serveur)
Copier le dossier `../api/` dans `C:\wamp64\www\Epoka\`.
Les endpoints seront accessibles à :
- `http://localhost/Epoka/api/auth.php`
- `http://localhost/Epoka/api/missions.php`
- `http://localhost/Epoka/api/villes.php`

### 2. Application Android

**Ouvrir dans Android Studio :**
1. File → Open → sélectionner ce dossier `android_app/`
2. Attendre la synchronisation Gradle

**URL du serveur (dans `app/build.gradle`) :**
```gradle
// Émulateur Android (localhost du PC) :
buildConfigField "String", "BASE_URL", '"http://10.0.2.2/Epoka/api/"'

// Vrai appareil sur le même WiFi :
buildConfigField "String", "BASE_URL", '"http://192.168.X.X/Epoka/api/"'
```
Remplacer `192.168.X.X` par l'IP de votre PC (cmd → `ipconfig`).

### 3. Lancer
- Démarrer WampServer
- Lancer l'app sur émulateur ou appareil réel
- Se connecter : ID `1` / mot de passe `admin`

## Fonctionnalités
- Connexion sécurisée (session persistante)
- Liste des missions du salarié connecté
- Création de mission (intitulé, dates, villes, repas)
- Suppression des missions en brouillon
- Design sombre inspiré du site web Epoka

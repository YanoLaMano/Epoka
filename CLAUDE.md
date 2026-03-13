# Projet Epoka — Contexte pour Claude

## Vue d'ensemble
Application web PHP de gestion de missions professionnelles pour l'entreprise Epoka. Développée dans le cadre d'un PPE (Projet Personnel Encadré) BTS SIO.

**Stack :** PHP 8+ vanilla (MVC maison), SQLite (`epoka.db`), Bootstrap 5.3, Lucide Icons, CSS custom (design system "Liquid Glass" inspiré Apple).

**Serveur local :** WampServer → `http://localhost/Epoka/`

---

## Architecture

```
c:/wamp64/www/Epoka/
├── index.php              # Point d'entrée unique (routeur)
├── config/
│   └── database.php       # Connexion SQLite (getDatabase())
├── includes/
│   └── helpers.php        # Fonctions globales (escape, isAdmin, getFlash, setFlash…)
├── models/                # Modèles de données (PDO SQLite)
│   ├── Salarie.php
│   ├── Mission.php
│   ├── Agence.php
│   ├── Distance.php
│   ├── Parametre.php
│   └── Ville.php
├── controllers/           # Logique métier
│   ├── AuthController.php
│   ├── SalarieController.php
│   ├── AgenceController.php
│   ├── MissionController.php
│   ├── DistanceController.php
│   └── ParametreController.php
├── views/
│   ├── layout.php         # Layout principal (sidebar + main, pages authentifiées)
│   ├── layout_footer.php  # Script JS global (thème, confetti, avatar…)
│   ├── login.php          # Page de connexion (standalone, pas de layout)
│   ├── dashboard.php
│   ├── missions/          # Liste, détail, formulaire de mission
│   ├── salaries/
│   ├── agences/
│   ├── distances/
│   └── parametres/
└── assets/
    ├── css/style.css      # Design system complet (~1800 lignes)
    └── img/
        ├── logo.svg
        └── avatars/       # Photos de profil (nom = salarie_id.ext)
```

---

## Routing
`index.php?page=<nom>` — le routeur inclut le bon controller puis la vue correspondante.

Pages publiques : `login`, `logout`
Pages protégées : `dashboard`, `missions`, `salaries`, `agences`, `distances`, `parametres`

Les pages admin (salaries, agences, distances, parametres) nécessitent `isAdmin()` → redirige sinon.

---

## Base de données (SQLite)
Fichier : `epoka.db`

Tables principales :
- `salarie` — id, nom, prenom, email, mot_de_passe (hashé bcrypt), role (admin/salarie), fonction, agence_id
- `mission` — id, titre, salarie_id, agence_depart_id, agence_arrivee_id, date_debut, date_fin, statut, moyen_transport, description
- `agence` — id, nom, ville, adresse
- `distance` — id, ville_depart, ville_arrivee, km
- `parametre` — clé/valeur (taux_km, forfait_repas, forfait_hotel…)

Compte démo : ID=1 / mot de passe=`admin`

---

## Design System CSS (`assets/css/style.css`)

### Thèmes disponibles (attribut `data-theme` sur `<html>`)
| Thème | Description |
|-------|-------------|
| `liquid` | Dark glassmorphism (défaut) — orbes animés, backdrop-filter |
| `liquid-light` | Light glassmorphism |
| `light` | Clair classique (sans orbes) |

Variables clés : `--accent`, `--bg-body`, `--glass-bg`, `--glass-blur`, `--glass-border`, `--glass-shadow`, `--text-primary`, `--text-secondary`, `--radius-*`, `--transition`

### Composants CSS notables
- `.sidebar` — sidebar fixe glassmorphism 250px, offcanvas sur mobile
- `.card-glass` — cartes avec backdrop-filter
- `.btn-primary/success/danger/outline` — boutons custom
- `.login-card` / `.login-card-outer` — layout de connexion centré (redesigné)
- `.mascot-container` / `.mascot-svg` — mascot interactif SVG (idle/watching/hiding/peeking)
- `.ambient-background` / `.ambient-orb` — orbes flottants animés en arrière-plan
- `.flash-message.flash-success/error` — messages flash
- `.theme-toggle-fab` — bouton flottant bascule thème

---

## Page de connexion (`views/login.php`)
- Layout : carte centrée unique (460px max) sur fond ambiant
- Mascot interactif SVG : suit la souris, couvre les yeux au mot de passe, peeking si mot de passe visible
- Toggle visibilité mot de passe
- Bascule de thème (dark ↔ light) avec `document.startViewTransition`
- Animation d'entrée de la carte : `loginCardIn` (translateY + scale)

---

## Fonctions helpers clés (`includes/helpers.php`)
```php
escape($str)          // htmlspecialchars
isAdmin()             // vérifie $_SESSION['role'] === 'admin'
isLoggedIn()          // vérifie session active
setFlash($type, $msg) // stocke un message flash en session
getFlash()            // consomme et retourne le flash
redirect($url)        // header Location + exit
```

---

## Conventions et notes de développement
- Pas de framework — PHP vanilla MVC maison
- CSS custom en design system complet, Bootstrap utilisé uniquement pour le grid et quelques utilitaires
- Les `!important` dans les inputs du login sont nécessaires pour écraser Bootstrap
- Les classes `.mascot-*` ne s'appliquent que sur la page login
- La sidebar utilise `offcanvas-md` pour être un panneau coulissant sur mobile
- Les sessions contiennent : `salarie_id`, `salarie_nom`, `salarie_prenom`, `salarie_fonction`, `role`
- Le thème préféré est persisté dans `localStorage` sous la clé `epoka-theme`

<?php
/**
 * Template principal (layout)
 * Toutes les pages authentifiées utilisent ce layout
 */
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark" data-theme="liquid">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epoka - Gestion des Missions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <!-- Icones Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Init theme as early as possible to prevent flashing (Dark by default)
        const savedTheme = localStorage.getItem('epoka-theme') || 'liquid';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-bs-theme', savedTheme === 'liquid-light' ? 'light' : 'dark');
    </script>
</head>
<body>
<!-- Theme Toggle FAB -->
<button id="themeToggleBtn" class="theme-toggle-fab" aria-label="Changer de thème">
    <span id="themeToggleIconWrapper"><i data-lucide="sun"></i></span>
</button>

<!-- Orbes animés en arrière-plan -->
<div class="ambient-background">
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>
</div>

<div class="app-layout d-flex w-100">
    <!-- Navbar Mobile (Visible uniquement sur petits écrans) -->
    <!-- On utilise un vrai bouton sur toute la largeur pour que Bootstrap capte le clic partout -->
    <nav class="navbar navbar-dark bg-card border-bottom d-md-none p-0 fixed-top w-100" style="z-index: 1050;">
        <button class="w-100 border-0 bg-transparent px-3 py-2 d-flex align-items-center justify-content-between text-start" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <!-- Logo un peu agrandi, texte retiré -->
            <img src="assets/img/logo.svg" alt="Epoka Logo" height="40">
            <!-- Icône burger conservée visuellement à droite -->
            <span class="navbar-toggler-icon d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i data-lucide="menu" style="color: var(--text-primary);"></i>
            </span>
        </button>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar offcanvas-md offcanvas-start d-md-flex flex-column" tabindex="-1" id="sidebarMenu">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="assets/img/logo.svg" alt="Epoka Logo" style="height: 40px;">
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Navigation</div>
                <a href="index.php?page=dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="layout-dashboard" class="icon-sm"></i></span> Tableau de bord
                </a>
                <a href="index.php?page=missions" class="nav-link <?= $currentPage === 'missions' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="target" class="icon-sm"></i></span> Missions
                </a>
            </div>

            <?php if (isAdmin()): ?>
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="index.php?page=salaries" class="nav-link <?= $currentPage === 'salaries' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="users" class="icon-sm"></i></span> Salariés
                </a>
                <a href="index.php?page=agences" class="nav-link <?= $currentPage === 'agences' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="building" class="icon-sm"></i></span> Agences
                </a>
                <a href="index.php?page=distances" class="nav-link <?= $currentPage === 'distances' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="ruler" class="icon-sm"></i></span> Distances
                </a>
                <a href="index.php?page=parametres" class="nav-link <?= $currentPage === 'parametres' ? 'active' : '' ?>">
                    <span class="nav-icon"><i data-lucide="coins" class="icon-sm"></i></span> Tarifications
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <?php
                $avatarUrl = null;
                $uid = $_SESSION['salarie_id'] ?? 0;
                foreach (['jpg','png','webp','gif'] as $_ext) {
                    $_ap = __DIR__ . '/../assets/img/avatars/' . $uid . '.' . $_ext;
                    if (file_exists($_ap)) {
                        $avatarUrl = 'assets/img/avatars/' . $uid . '.' . $_ext . '?v=' . filemtime($_ap);
                        break;
                    }
                }
                ?>
                <div class="user-avatar" id="userAvatarBtn" title="Changer la photo de profil">
                    <img id="userAvatarImg"
                         src="<?= $avatarUrl ?? '' ?>"
                         alt="Avatar"
                         <?= $avatarUrl ? '' : 'style="display:none"' ?>>
                    <span id="userAvatarInitial" <?= $avatarUrl ? 'style="display:none"' : '' ?>>
                        <?= strtoupper(substr($_SESSION['salarie_prenom'] ?? 'U', 0, 1)) ?>
                    </span>
                    <div class="avatar-overlay">
                        <i data-lucide="camera"></i>
                    </div>
                    <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none">
                </div>
                <div class="user-details">
                    <div class="user-name"><?= escape($_SESSION['salarie_prenom'] ?? '') ?> <?= escape($_SESSION['salarie_nom'] ?? '') ?></div>
                    <div class="user-role"><?= escape($_SESSION['salarie_fonction'] ?? '') ?></div>
                </div>
            </div>
            <a href="index.php?page=logout" class="btn-logout"><i data-lucide="log-out" class="icon-sm"></i> Déconnexion</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content flex-grow-1 w-100">
        <?php
        $flash = getFlash();
        if ($flash):
        ?>
        <div class="flash-message flash-<?= in_array($flash['type'], ['success','success_confetti']) ? 'success' : $flash['type'] ?>">
            <?= $flash['type'] === 'error' ? '<i data-lucide="x-circle" class="icon-sm"></i>' : '<i data-lucide="check-circle" class="icon-sm"></i>' ?>
            <?= escape($flash['message']) ?>
        </div>
        <?php if ($flash['type'] === 'success_confetti'): ?>
        <script>window._fireConfetti = true;</script>
        <?php endif; ?>
        <?php endif; ?>

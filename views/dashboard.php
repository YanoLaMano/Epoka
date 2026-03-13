<?php require __DIR__ . '/layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tableau de bord</h1>
        <p class="page-subtitle">Bienvenue, <?= escape($_SESSION['salarie_prenom']) ?> ! Voici un résumé de vos missions.</p>
    </div>
    <a href="index.php?page=missions&action=create" class="btn btn-primary"><i data-lucide="plus" class="icon-sm"></i> Nouvelle mission</a>
</div>

<style>
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 3rem;
}
@media (max-width: 991.98px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575.98px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
}
</style>

<?php if (canValidate() || canPay() || isAdmin()): ?>
<div class="stats-row">
    <div class="stat-card">
        <span class="stat-icon"><i data-lucide="list"></i></span>
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Total Missions</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon"><i data-lucide="file-edit"></i></span>
        <div class="stat-value"><?= number_format($stats['brouillon']) ?></div>
        <div class="stat-label">Brouillons</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon"><i data-lucide="check-circle"></i></span>
        <div class="stat-value"><?= number_format($stats['validee']) ?></div>
        <div class="stat-label">Validées</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon"><i data-lucide="euro"></i></span>
        <div class="stat-value"><?= number_format($stats['payee']) ?></div>
        <div class="stat-label">Payées</div>
    </div>
</div>
<?php endif; ?>

<?php if (canValidate()): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><i data-lucide="check-square" class="icon-sm"></i> Missions à valider</span>
        <span class="badge"><?= count($missionsAValider) ?> en attente</span>
    </div>
    <div class="card-body">
        <?php if (empty($missionsAValider)): ?>
        <div class="empty-state" style="padding: 2rem 0;">
            <div class="empty-icon"><i data-lucide="check-circle"></i></div>
            <p style="color:var(--text-secondary); margin-top:.75rem;">Aucune mission en attente de validation.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Intitulé</th>
                        <?php if (isAdmin() || canValidate()): ?><th>Salarié</th><?php endif; ?>
                        <th>Dates</th>
                        <th>Trajet</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missionsAValider as $m): ?>
                    <tr>
                        <td><strong><?= escape($m['intitule']) ?></strong></td>
                        <?php if (isAdmin() || canValidate()): ?>
                        <td><?= escape($m['salarie_prenom'] ?? '') ?> <?= escape($m['salarie_nom'] ?? '') ?></td>
                        <?php endif; ?>
                        <td><?= formatDate($m['date_debut']) ?> → <?= formatDate($m['date_fin']) ?></td>
                        <td><?= escape($m['ville_depart_nom'] ?? '') ?> → <?= escape($m['ville_arrivee_nom'] ?? '') ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?page=missions&action=voir&id=<?= $m['id'] ?>"
                                   class="btn btn-sm btn-outline" title="Voir détails">
                                    <i data-lucide="eye" class="icon-sm"></i>
                                </a>
                                <?php if ($m['id_salarie'] == $_SESSION['salarie_id'] || isAdmin()): ?>
                                <a href="index.php?page=missions&action=edit&id=<?= $m['id'] ?>"
                                   class="btn btn-sm btn-primary" title="Modifier">
                                    <i data-lucide="edit-3" class="icon-sm"></i>
                                </a>
                                <a href="index.php?page=missions&action=delete&id=<?= $m['id'] ?>"
                                   class="btn btn-sm btn-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer cette mission ?')">
                                    <i data-lucide="trash-2" class="icon-sm"></i>
                                </a>
                                <?php endif; ?>
                                <a href="index.php?page=missions&action=valider&id=<?= $m['id'] ?>"
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Valider cette mission ?')">
                                    <i data-lucide="check-circle" class="icon-sm"></i> Valider
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── MES MISSIONS (salarié standard sans rôle spécial) ── -->
<?php if (!canValidate() && !canPay()): ?>
<?php
    $todayMs = new DateTime('today');
    $missionsActuelles = array_values(array_filter($missionsDuSalarie, fn($m) => !empty($m['date_fin']) && new DateTime($m['date_fin']) >= $todayMs));
?>

<!-- ── MA PROCHAINE MISSION – STEPPER ── -->
<?php
    $sortedActuelles = $missionsActuelles;
    usort($sortedActuelles, fn($a, $b) => strcmp($a['date_debut'] ?? '', $b['date_debut'] ?? ''));
    $nextM = $sortedActuelles[0] ?? null;
?>
<?php if ($nextM): ?>
<?php
    $stepIdx = match($nextM['statut']) { 'validee' => 1, 'payee' => 2, default => 0 };
    $stepDefs = [
        ['label' => 'Création',   'icon' => 'file-edit'],
        ['label' => 'Validation', 'icon' => 'check-circle'],
        ['label' => 'Payée',      'icon' => 'credit-card'],
    ];
?>
<div class="card ms-card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title"><i data-lucide="compass" class="icon-sm"></i> Ma prochaine mission</span>
        <a href="index.php?page=missions&action=voir&id=<?= $nextM['id'] ?>" class="btn btn-sm btn-outline">
            <i data-lucide="eye" class="icon-sm"></i> Voir
        </a>
    </div>
    <div class="card-body" style="padding:1.75rem 1.5rem 2.5rem;">
        <div class="ms-meta">
            <strong class="ms-title"><?= escape($nextM['intitule']) ?></strong>
            <span class="ms-info"><i data-lucide="calendar" class="icon-sm"></i> <?= formatDate($nextM['date_debut']) ?> → <?= formatDate($nextM['date_fin']) ?></span>
            <span class="ms-info"><i data-lucide="map-pin" class="icon-sm"></i> <?= escape($nextM['ville_depart_nom'] ?? '') ?> → <?= escape($nextM['ville_arrivee_nom'] ?? '') ?></span>
        </div>
        <?php
            /* Arc center (200,215) r=150 → points:
               Création   (50, 215)  → left:12.5% top:86%
               Validation (200, 65)  → left:50%   top:26%
               Payée      (350, 215) → left:87.5%  top:86%
            */
            $cCls = $stepIdx === 0 ? 'ms-active' : 'ms-complete';
            $vCls = $stepIdx === 0 ? '' : ($stepIdx === 1 ? 'ms-active' : 'ms-complete');
            $pCls = $stepIdx >= 2 ? 'ms-complete' : '';
            $cIco = $stepIdx > 0 ? 'check' : 'file-edit';
            $vIco = $stepIdx > 1 ? 'check' : 'check-circle';
        ?>
        <div class="ms-track" id="ms-track">
            <!-- Arc SVG — viewBox 400×250, centre (200,215) r=150
                 height:250px fixe + preserveAspectRatio=none → 1 SVG unit = 1 px vertical
                 Extrémités (50,215) et (350,215) = 86% hauteur = centre des cercles HTML -->
            <!--
                Arc mathématique : centre (200,215) r=150.
                Endpoints TRONQUÉS à 28 SVG-units du centre de chaque cercle
                (= rayon du cercle HTML 56px/2 = 28px, 1 SVG-unit = 1px vertical).

                Calcul des points tronqués (θ en degrés SVG, sens horaire depuis +x) :
                  Création (50,215)  θ=180° → départ seg1  : +10.7° → (53, 187)
                  Validation (200,65) θ=270° → fin seg1     : -10.7° → (172, 68)
                                               départ seg2  : +10.7° → (228, 68)
                  Payée (350,215)    θ=0°   → fin seg2     : -10.7° → (347, 187)
                Longueur de chaque segment tronqué : ≈ 180 SVG units.
            -->
            <svg class="ms-arc-svg" viewBox="0 0 400 250" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <filter id="ms-glow-f" x="-40%" y="-40%" width="180%" height="180%">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="3.5" result="blur"/>
                        <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                    </filter>
                </defs>

                <!-- Fond arc en deux parties (blanc discret) -->
                <path d="M 53,187 A 150,150 0 0 1 172,68" class="ms-arc-bg" id="ms-bg-0"/>
                <path d="M 228,68 A 150,150 0 0 1 347,187" class="ms-arc-bg" id="ms-bg-1"/>

                <!-- Segment 1 tronqué : bord Création → bord Validation -->
                <path d="M 53,187 A 150,150 0 0 1 172,68"
                      class="ms-arc-seg"
                      data-fill="<?= $stepIdx >= 1 ? '1' : '0' ?>"
                      stroke-dasharray="180" stroke-dashoffset="180"/>

                <!-- Segment 2 tronqué : bord Validation → bord Payée -->
                <path d="M 228,68 A 150,150 0 0 1 347,187"
                      class="ms-arc-seg"
                      data-fill="<?= $stepIdx >= 2 ? '1' : '0' ?>"
                      stroke-dasharray="180" stroke-dashoffset="180"/>

                <!-- Particules voyageuses (cachées par défaut) -->
                <circle class="ms-spark" id="ms-spark-0" r="5" style="opacity:0">
                    <animateMotion id="ms-anim-0" dur="0.45s" fill="freeze" begin="indefinite"
                                   path="M 53,187 A 150,150 0 0 1 172,68"/>
                </circle>
                <circle class="ms-spark" id="ms-spark-1" r="5" style="opacity:0">
                    <animateMotion id="ms-anim-1" dur="0.45s" fill="freeze" begin="indefinite"
                                   path="M 228,68 A 150,150 0 0 1 347,187"/>
                </circle>
            </svg>
            <!-- Création — bas gauche -->
            <div class="ms-node ms-pos-bl <?= $cCls ?>" id="ms-node-0">
                <div class="ms-circle"><i data-lucide="<?= $cIco ?>" class="ms-icon"></i></div>
                <span class="ms-label">Création</span>
            </div>
            <!-- Validation — haut centre -->
            <div class="ms-node ms-pos-tc <?= $vCls ?>" id="ms-node-1">
                <span class="ms-label ms-label-top">Validation</span>
                <div class="ms-circle"><i data-lucide="<?= $vIco ?>" class="ms-icon"></i></div>
            </div>
            <!-- Payée — bas droite -->
            <div class="ms-node ms-pos-br <?= $pCls ?>" id="ms-node-2">
                <div class="ms-circle"><i data-lucide="credit-card" class="ms-icon"></i></div>
                <span class="ms-label">Payée</span>
            </div>
        </div>
    </div>
</div>

<style>
/* ── Stepper "Ma prochaine mission" ── */
.ms-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .6rem 1.25rem;
    margin-bottom: 2.25rem;
}
.ms-title {
    font-size: 1.05rem;
    color: var(--text-primary);
}
.ms-info {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .85rem;
    color: var(--text-muted);
}
.ms-track {
    position: relative;
    width: 100%;
    height: 250px; /* fixe → 1 SVG unit = 1 px vertical, aligne parfaitement */
    overflow: visible;
}
.ms-arc-svg {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
}
.ms-node {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .55rem;
    z-index: 2;
}
/*
 * Les offsets translate() compensent la hauteur du label :
 * le CENTRE DU CERCLE (pas du div entier) s'aligne avec le point SVG.
 * Nœuds bas  : label en-dessous → cercle est 12px AU-DESSUS du centre du div
 *              → on pousse le div de +12px vers le bas
 * Nœud haut  : label au-dessus  → cercle est 12px EN-DESSOUS du centre du div
 *              → on pousse le div de -12px vers le haut
 */
.ms-pos-bl {
    left: 12.5%; top: 86%;
    transform: translate(-50%, calc(-50% + 12px));
    animation: ms-pop-bl .55s cubic-bezier(.16,1,.3,1) .08s both;
}
.ms-pos-br {
    left: 87.5%; top: 86%;
    transform: translate(-50%, calc(-50% + 12px));
    animation: ms-pop-bl .55s cubic-bezier(.16,1,.3,1) .36s both;
}
.ms-pos-tc {
    left: 50%; top: 26%;
    transform: translate(-50%, calc(-50% - 12px));
    animation: ms-pop-tc .55s cubic-bezier(.16,1,.3,1) .22s both;
}
@keyframes ms-pop-bl {
    from { opacity: 0; transform: translate(-50%, calc(-50% + 12px)) scale(.55); }
    to   { opacity: 1; transform: translate(-50%, calc(-50% + 12px)) scale(1); }
}
@keyframes ms-pop-tc {
    from { opacity: 0; transform: translate(-50%, calc(-50% - 12px)) scale(.55); }
    to   { opacity: 1; transform: translate(-50%, calc(-50% - 12px)) scale(1); }
}
/* Label au-dessus pour le nœud haut */
.ms-label-top { order: -1; }
.ms-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2.5px solid var(--border);
    background: var(--bg-body); /* fond solide pour couvrir l'arc derrière */
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color .45s ease, background .45s ease,
                box-shadow .45s ease, transform .2s ease;
    position: relative;
}
/* Hover : légère élévation */
.ms-node:hover .ms-circle {
    transform: scale(1.1);
}
.ms-icon {
    width: 22px;
    height: 22px;
    color: var(--text-primary); /* blanc par défaut pour tous les états */
    transition: color .4s ease;
}
/* Inactif (étape future) : cercle et label atténués */
.ms-node:not(.ms-complete):not(.ms-active) .ms-circle,
.ms-node:not(.ms-complete):not(.ms-active) .ms-label {
    opacity: .38;
}
/* Completed */
.ms-node.ms-complete .ms-circle {
    border-color: var(--success);
    background: var(--success-light);
    box-shadow: 0 0 10px rgba(25,135,84,.2), inset 0 1px 0 rgba(255,255,255,.06);
    opacity: 1;
}
.ms-node.ms-complete .ms-icon { color: var(--success); }
/* Active */
.ms-node.ms-active .ms-circle {
    border-color: var(--accent);
    background: var(--accent-glow);
    box-shadow: 0 0 0 5px var(--accent-glow);
    animation: ms-pulse 2.4s ease-in-out infinite;
    opacity: 1;
}
.ms-node.ms-active .ms-icon { color: var(--accent); }
@keyframes ms-pulse {
    0%,100% { box-shadow: 0 0 0 4px var(--accent-glow); }
    50%      { box-shadow: 0 0 0 14px rgba(0,0,0,0); }
}
/* Label */
.ms-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    text-align: center;
    transition: color .4s ease;
    white-space: nowrap;
}
.ms-node.ms-complete .ms-label,
.ms-node.ms-active   .ms-label { color: var(--text-primary); }
/* Arc paths */
.ms-arc-bg {
    fill: none;
    stroke: rgba(255,255,255,.16); /* fond blanc discret, visible sur thèmes sombres et clairs */
    stroke-width: 2.5;
    stroke-linecap: round;
}
.ms-arc-seg {
    fill: none;
    stroke: var(--success);
    stroke-width: 2.5;
    stroke-linecap: round;
    transition: stroke-dashoffset .95s cubic-bezier(.16,1,.3,1);
}
/* Glow qui s'active après que le segment est dessiné */
.ms-arc-seg.ms-lit {
    filter: url(#ms-glow-f);
}
/* Particule voyageuse */
.ms-spark {
    fill: var(--success);
    filter: drop-shadow(0 0 6px var(--success)) drop-shadow(0 0 3px var(--success));
    transition: opacity .15s ease;
}
/* ── Animation d'arrivée sur une bulle ── */
.ms-circle::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid var(--success);
    opacity: 0;
    pointer-events: none;
}
@keyframes ms-arrive-ring {
    0%   { opacity: .4; transform: scale(1); }
    100% { opacity: 0;  transform: scale(1.6); }
}
@keyframes ms-arrive-bounce {
    0%   { transform: scale(1); }
    35%  { transform: scale(1.1); }
    65%  { transform: scale(.97); }
    100% { transform: scale(1); }
}
@keyframes ms-arrive-icon {
    0%   { transform: scale(1); }
    35%  { transform: scale(1.15); }
    100% { transform: scale(1); }
}
.ms-circle.ms-arrive {
    animation: ms-arrive-bounce .65s cubic-bezier(.16,1,.3,1) forwards !important;
}
.ms-circle.ms-arrive::after {
    animation: ms-arrive-ring .65s ease-out forwards !important;
}
.ms-circle.ms-arrive .ms-icon {
    animation: ms-arrive-icon .65s cubic-bezier(.16,1,.3,1) forwards !important;
}
@media (max-width: 560px) {
    .ms-track { height: 180px; }
    .ms-circle { width: 42px; height: 42px; }
    .ms-icon { width: 16px; height: 16px; }
    .ms-label { font-size: .6rem; letter-spacing: .04em; }
}
</style>

<script>
(function () {
    /* ── Paramètres géométriques (doivent correspondre au viewBox 0 0 400 250) ── */
    var arcCx = 200, arcCy = 215, arcR = 150;
    var trim = 33; /* px visuels : rayon du cercle-bulle (28) + marge (5) */

    function computePaths() {
        var track = document.getElementById('ms-track');
        if (!track) return null;
        var W = track.offsetWidth;
        /* Échelle horizontale et verticale (height est fixe 250 px CSS → SVG 250 u) */
        var scaleH = W / 400;
        /* Angle à retrancher à chaque extrémité selon la tangente locale */
        var dt_v = trim / arcR;                /* tangente verticale : Création & Payée */
        var dt_h = trim / scaleH / arcR;       /* tangente horizontale : Validation */

        function pt(theta) {
            return {
                x: +(arcCx + arcR * Math.cos(theta)).toFixed(2),
                y: +(arcCy + arcR * Math.sin(theta)).toFixed(2)
            };
        }
        /* Création  θ = π          → avance de dt_v vers la droite */
        var p1    = pt(Math.PI + dt_v);
        /* Validation θ = 3π/2 (sommet) → reculer de dt_h / avancer de dt_h */
        var p2end = pt(3 * Math.PI / 2 - dt_h);
        var p2st  = pt(3 * Math.PI / 2 + dt_h);
        /* Payée      θ = 2π (= 0)  → reculer de dt_v */
        var p3    = pt(2 * Math.PI - dt_v);

        function arc(a, b) {
            return 'M ' + a.x + ',' + a.y + ' A ' + arcR + ',' + arcR + ' 0 0 1 ' + b.x + ',' + b.y;
        }
        /* Longueur approximative d'un demi-arc moins les deux coupes */
        var arcLen = Math.ceil(arcR * (Math.PI / 2 - dt_v - dt_h));

        return {
            bg0:  arc(p1, p2end),
            bg1:  arc(p2st, p3),
            seg1: arc(p1, p2end),
            seg2: arc(p2st, p3),
            len:  arcLen
        };
    }

    function applyPaths() {
        var svg  = document.querySelector('#ms-track .ms-arc-svg');
        if (!svg) return;
        var p = computePaths();
        if (!p) return;

        var bg0  = document.getElementById('ms-bg-0');
        var bg1  = document.getElementById('ms-bg-1');
        var segs = svg.querySelectorAll('.ms-arc-seg');

        if (bg0) bg0.setAttribute('d', p.bg0);
        if (bg1) bg1.setAttribute('d', p.bg1);

        [segs[0], segs[1]].forEach(function (s, i) {
            if (!s) return;
            var path = (i === 0) ? p.seg1 : p.seg2;
            s.setAttribute('d', path);
            s.setAttribute('stroke-dasharray', p.len);
            /* Ne réinitialiser l'offset que si le segment n'est pas déjà animé */
            if (s.dataset.fill !== '1') s.setAttribute('stroke-dashoffset', p.len);
            var anim = document.getElementById('ms-anim-' + i);
            if (anim) anim.setAttribute('path', path);
        });
    }

    /* Appliquer au chargement et à chaque redimensionnement */
    applyPaths();
    window.addEventListener('resize', applyPaths);

    /* ── Animations séquentielles ── */
    var segs  = document.querySelectorAll('#ms-track .ms-arc-seg');
    var delay = 450;
    segs.forEach(function (s, i) {
        if (s.dataset.fill === '1') {
            (function (seg, idx) {
                setTimeout(function () {
                    seg.style.strokeDashoffset = '0';

                    var spark = document.getElementById('ms-spark-' + idx);
                    if (spark) {
                        spark.style.opacity = '0.55';
                        var anim = document.getElementById('ms-anim-' + idx);
                        if (anim) anim.beginElement();
                        setTimeout(function () { spark.style.opacity = '0'; }, 450);
                    }
                    setTimeout(function () { seg.classList.add('ms-lit'); }, 970);

                    /* Animer la bulle destination quand l'arc arrive */
                    setTimeout(function () {
                        var dest = document.getElementById('ms-node-' + (idx + 1));
                        if (dest) {
                            var circ = dest.querySelector('.ms-circle');
                            if (circ) {
                                circ.classList.remove('ms-arrive');
                                void circ.offsetWidth; /* reflow pour relancer */
                                circ.classList.add('ms-arrive');
                            }
                        }
                    }, 950);
                }, delay);
            })(s, i);
            delay += 1050; /* 950ms (durée transition) + 100ms tampon → 2e segment après la fin du 1er */
        }
    });

    lucide.createIcons();
})();
</script>
<?php endif; ?>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <span class="card-title"><i data-lucide="target" class="icon-sm"></i> Mes missions</span>
        <span class="badge"><?= count($missionsActuelles) ?> mission(s)</span>
    </div>
    <div class="card-body">
        <?php if (empty($missionsActuelles)): ?>
        <div class="empty-state" style="padding:2rem 0;">
            <div class="empty-icon"><i data-lucide="target"></i></div>
            <p>Aucune mission en cours.</p>
            <a href="index.php?page=missions&action=create" class="btn btn-primary">Créer une mission</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Intitulé</th>
                        <th>Dates</th>
                        <th>Trajet</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missionsActuelles as $m): ?>
                    <tr>
                        <td><strong><?= escape($m['intitule']) ?></strong></td>
                        <td><?= formatDate($m['date_debut']) ?> → <?= formatDate($m['date_fin']) ?></td>
                        <td><?= escape($m['ville_depart_nom'] ?? '') ?> → <?= escape($m['ville_arrivee_nom'] ?? '') ?></td>
                        <td>
                            <span class="badge badge-<?= $m['statut'] ?>">
                                <?= $m['statut'] === 'brouillon' ? '<i data-lucide="file-edit" class="icon-sm"></i>' : ($m['statut'] === 'validee' ? '<i data-lucide="check-circle" class="icon-sm"></i>' : '<i data-lucide="credit-card" class="icon-sm"></i>') ?>
                                <?= escape($m['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.4rem;">
                                <a href="index.php?page=missions&action=voir&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline" title="Voir"><i data-lucide="eye" class="icon-sm"></i></a>
                                <?php if ($m['statut'] === 'brouillon'): ?>
                                <a href="index.php?page=missions&action=edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-primary" title="Modifier"><i data-lucide="edit-3" class="icon-sm"></i></a>
                                <?php endif; ?>
                                <a href="index.php?page=missions&action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette mission ?')"><i data-lucide="trash-2" class="icon-sm"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── MES MISSIONS RÉCENTES (tous les rôles, missions personnelles uniquement) ── -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i data-lucide="list" class="icon-sm"></i> Mes missions récentes</span>
        <a href="index.php?page=missions" class="btn btn-sm btn-outline">Voir toutes</a>
    </div>
    <div class="card-body">
        <?php
            $today    = new DateTime('today');
            $passees  = array_values(array_filter($missionsDuSalarie, fn($m) => !empty($m['date_fin']) && new DateTime($m['date_fin']) < $today));
            $recentes = array_slice($passees, 0, 5);
        ?>
        <?php if (empty($recentes)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i data-lucide="history" class="icon-sm"></i></div>
            <p>Aucune mission terminée pour le moment.</p>
            <a href="index.php?page=missions&action=create" class="btn btn-primary">Créer une mission</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Intitulé</th>
                        <th>Dates</th>
                        <th>Trajet</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentes as $m): ?>
                    <tr>
                        <td><strong><?= escape($m['intitule']) ?></strong></td>
                        <td><?= formatDate($m['date_debut']) ?> → <?= formatDate($m['date_fin']) ?></td>
                        <td><?= escape($m['ville_depart_nom'] ?? '') ?> → <?= escape($m['ville_arrivee_nom'] ?? '') ?></td>
                        <td>
                            <span class="badge badge-<?= $m['statut'] ?>">
                                <?= $m['statut'] === 'brouillon' ? '<i data-lucide="file-edit" class="icon-sm"></i>' : ($m['statut'] === 'validee' ? '<i data-lucide="check-circle" class="icon-sm"></i>' : '<i data-lucide="credit-card" class="icon-sm"></i>') ?>
                                <?= escape($m['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?page=missions&action=voir&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>

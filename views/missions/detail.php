<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="target" class="icon-sm"></i> Mission #<?= $mission['id'] ?></h1>
        <p class="page-subtitle"><?= escape($mission['intitule']) ?></p>
    </div>
    <div class="btn-group">
        <?php if ($mission['statut'] === 'brouillon' && canValidate()): ?>
        <a href="index.php?page=missions&action=valider&id=<?= $mission['id'] ?>" class="btn btn-success" onclick="return confirm('Valider cette mission ?')"><i data-lucide="check-circle" class="icon-sm"></i> Valider</a>
        <?php endif; ?>
        <?php if ($mission['statut'] === 'validee' && canPay()): ?>
        <a href="index.php?page=missions&action=payer&id=<?= $mission['id'] ?>" class="btn btn-warning" onclick="return confirm('Marquer comme payée ?')"><i data-lucide="credit-card" class="icon-sm"></i> Payer</a>
        <?php endif; ?>
        <a href="index.php?page=missions" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <span class="card-title"><i data-lucide="list" class="icon-sm"></i> Détails de la mission</span>
        <span class="badge badge-<?= $mission['statut'] ?>">
            <?= $mission['statut'] === 'brouillon' ? '<i data-lucide="file-edit" class="icon-sm"></i>' : ($mission['statut'] === 'validee' ? '<i data-lucide="check-circle" class="icon-sm"></i>' : '<i data-lucide="credit-card" class="icon-sm"></i>') ?>
            <?= escape($mission['statut']) ?>
        </span>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Intitulé</div>
                <div class="detail-value"><?= escape($mission['intitule']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Salarié</div>
                <div class="detail-value"><?= escape($mission['salarie_prenom']) ?> <?= escape($mission['salarie_nom']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Date de début</div>
                <div class="detail-value"><?= formatDate($mission['date_debut']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Date de fin</div>
                <div class="detail-value"><?= formatDate($mission['date_fin']) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ville de départ</div>
                <div class="detail-value"><?= escape($mission['ville_depart_nom']) ?> (<?= escape($mission['cp_depart']) ?>)</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ville d'arrivée</div>
                <div class="detail-value"><?= escape($mission['ville_arrivee_nom']) ?> (<?= escape($mission['cp_arrivee']) ?>)</div>
            </div>
        </div>

        <div class="frais-section">
            <h3><i data-lucide="euro" class="icon-sm"></i> Calcul des frais</h3>
            <div class="frais-line">
                <span>Distance</span>
                <span><strong><?= $frais['km'] ?> km</strong></span>
            </div>
            <div class="frais-line">
                <span>Prix au km</span>
                <span><?= formatMoney($frais['prix_km']) ?></span>
            </div>
            <div class="frais-line">
                <span>Frais kilométriques (<?= $frais['km'] ?> km × <?= formatMoney($frais['prix_km']) ?>)</span>
                <span><strong><?= formatMoney($frais['frais_km']) ?></strong></span>
            </div>
            <div class="frais-line">
                <span>Nombre de jours</span>
                <span><strong><?= $frais['jours'] ?> jour(s)</strong></span>
            </div>
            <div class="frais-line">
                <span>Prix hébergement / jour</span>
                <span><?= formatMoney($frais['prix_hebergement']) ?></span>
            </div>
            <div class="frais-line">
                <span>Frais d'hébergement (<?= $frais['jours'] ?> j × <?= formatMoney($frais['prix_hebergement']) ?>)</span>
                <span><strong><?= formatMoney($frais['frais_hebergement']) ?></strong></span>
            </div>
            <div class="frais-line">
                <span>Nombre de repas</span>
                <span><strong><?= $frais['nb_repas'] ?> repas</strong></span>
            </div>
            <div class="frais-line">
                <span>Prix par repas</span>
                <span><?= formatMoney($frais['prix_repas']) ?></span>
            </div>
            <div class="frais-line">
                <span>Frais de repas (<?= $frais['nb_repas'] ?> × <?= formatMoney($frais['prix_repas']) ?>)</span>
                <span><strong><?= formatMoney($frais['frais_repas']) ?></strong></span>
            </div>
            <div class="frais-line frais-total">
                <span>TOTAL</span>
                <span class="detail-value big"><?= formatMoney($frais['total']) ?></span>
            </div>

            <?php if ($frais['km'] == 0): ?>
            <p style="color: var(--warning); margin-top: 1rem; font-size: 0.85rem;">
                <i data-lucide="alert-triangle" class="icon-sm"></i> La distance entre ces deux villes n'est pas renseignée. 
                <?php if (isAdmin()): ?>
                <a href="index.php?page=distances&action=create">Ajouter une distance</a>
                <?php endif; ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

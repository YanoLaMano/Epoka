<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="coins" class="icon-sm"></i> Tarifications</h1>
        <p class="page-subtitle">Configuration des tarifs de remboursement</p>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <span class="card-title"><i data-lucide="euro" class="icon-sm"></i> Tarifs de remboursement</span>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="prix_km">Prix par kilomètre (€)</label>
                <input type="number" id="prix_km" name="prix_km" class="form-control" 
                       value="<?= $parametre['prix_km'] ?>" required step="0.01" min="0"
                       placeholder="Ex: 0.50">
            </div>

            <div class="form-group">
                <label class="form-label" for="prix_hebergement">Prix hébergement par jour (€)</label>
                <input type="number" id="prix_hebergement" name="prix_hebergement" class="form-control"
                       value="<?= $parametre['prix_hebergement'] ?>" required step="0.01" min="0"
                       placeholder="Ex: 80.00">
            </div>

            <div class="form-group">
                <label class="form-label" for="prix_repas"><i data-lucide="utensils" class="icon-sm"></i> Prix d'un repas (€)</label>
                <input type="number" id="prix_repas" name="prix_repas" class="form-control"
                       value="<?= $parametre['prix_repas'] ?? '15.00' ?>" required step="0.01" min="0"
                       placeholder="Ex: 15.00">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i data-lucide="save" class="icon-sm"></i> Enregistrer les paramètres</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

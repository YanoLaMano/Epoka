<?php 
require __DIR__ . '/../layout.php';
$isEdit = isset($agence);
$title = $isEdit ? 'Modifier l\'agence' : 'Nouvelle agence';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="building" class="icon-sm"></i> <?= $title ?></h1>
    </div>
    <a href="index.php?page=agences" class="btn btn-outline">← Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" class="form-control" 
                       value="<?= escape($agence['adresse'] ?? '') ?>" required
                       placeholder="Ex: 12 rue de la Paix">
            </div>

            <div class="form-group">
                <label class="form-label" for="ville_search">Ville</label>
                <div class="search-wrapper">
                    <input type="text" id="ville_search" class="form-control" 
                           placeholder="Tapez le nom d'une ville..."
                           value="<?= $isEdit ? escape($agence['ville_nom'] . ' (' . $agence['code_postal'] . ')') : '' ?>"
                           autocomplete="off">
                    <input type="hidden" id="id_ville" name="id_ville" 
                           value="<?= escape($agence['id_ville'] ?? '') ?>" required>
                    <div id="ville_results" class="search-results"></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '<i data-lucide="save" class="icon-sm"></i> Enregistrer' : '<i data-lucide="plus" class="icon-sm"></i> Créer l\'agence' ?>
                </button>
                <a href="index.php?page=agences" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

<?php 
require __DIR__ . '/../layout.php';
$isEdit = isset($distance);
$title = $isEdit ? 'Modifier la distance' : 'Nouvelle distance';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="ruler" class="icon-sm"></i> <?= $title ?></h1>
    </div>
    <a href="index.php?page=distances" class="btn btn-outline">← Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ville_depart_search">Ville de départ</label>
                    <div class="search-wrapper">
                        <input type="text" id="ville_depart_search" class="form-control" 
                               placeholder="Tapez le nom d'une ville..."
                               value="<?= $isEdit ? escape($distance['ville_depart_nom'] . ' (' . $distance['cp_depart'] . ')') : '' ?>"
                               autocomplete="off">
                        <input type="hidden" id="id_ville_depart" name="id_ville_depart" 
                               value="<?= escape($distance['id_ville_depart'] ?? '') ?>" required>
                        <div id="ville_depart_results" class="search-results"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="ville_arrivee_search">Ville d'arrivée</label>
                    <div class="search-wrapper">
                        <input type="text" id="ville_arrivee_search" class="form-control" 
                               placeholder="Tapez le nom d'une ville..."
                               value="<?= $isEdit ? escape($distance['ville_arrivee_nom'] . ' (' . $distance['cp_arrivee'] . ')') : '' ?>"
                               autocomplete="off">
                        <input type="hidden" id="id_ville_arrivee" name="id_ville_arrivee" 
                               value="<?= escape($distance['id_ville_arrivee'] ?? '') ?>" required>
                        <div id="ville_arrivee_results" class="search-results"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="km">Distance (km)</label>
                <input type="number" id="km" name="km" class="form-control" 
                       value="<?= escape($distance['km'] ?? '') ?>" required min="1"
                       placeholder="Ex: 450">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '<i data-lucide="save" class="icon-sm"></i> Enregistrer' : '<i data-lucide="plus" class="icon-sm"></i> Ajouter la distance' ?>
                </button>
                <a href="index.php?page=distances" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

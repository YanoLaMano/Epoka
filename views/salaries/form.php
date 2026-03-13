<?php 
require __DIR__ . '/../layout.php';
$isEdit = isset($salarie);
$title = $isEdit ? 'Modifier le salarié' : 'Nouveau salarié';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="user" class="icon-sm"></i> <?= $title ?></h1>
    </div>
    <a href="index.php?page=salaries" class="btn btn-outline">← Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control" 
                           value="<?= escape($salarie['nom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" 
                           value="<?= escape($salarie['prenom'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="fonction">Fonction</label>
                    <input type="text" id="fonction" name="fonction" class="form-control" 
                           value="<?= escape($salarie['fonction'] ?? '') ?>" required
                           placeholder="Ex: Développeur, Chef de projet...">
                </div>
                <div class="form-group">
                    <label class="form-label" for="mot_de_passe">Mot de passe<?= $isEdit ? ' (laisser vide pour ne pas changer)' : '' ?></label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" 
                           <?= !$isEdit ? 'required' : '' ?> placeholder="<?= $isEdit ? 'Laisser vide pour garder l\'actuel' : 'Choisir un mot de passe' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="id_agence">Agence</label>
                <select id="id_agence" name="id_agence" class="form-control" required>
                    <option value="">-- Sélectionner une agence --</option>
                    <?php foreach ($agences as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= (isset($salarie['id_agence']) && $salarie['id_agence'] == $a['id']) ? 'selected' : '' ?>>
                        <?= escape($a['adresse']) ?> - <?= escape($a['ville_nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-check">
                    <input type="checkbox" id="peut_valider" name="peut_valider" 
                           <?= (isset($salarie['peut_valider']) && $salarie['peut_valider']) ? 'checked' : '' ?>>
                    <label for="peut_valider"><i data-lucide="check-circle" class="icon-sm"></i> Peut valider les missions</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="peut_payer" name="peut_payer" 
                           <?= (isset($salarie['peut_payer']) && $salarie['peut_payer']) ? 'checked' : '' ?>>
                    <label for="peut_payer"><i data-lucide="credit-card" class="icon-sm"></i> Peut payer les missions</label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '<i data-lucide="save" class="icon-sm"></i> Enregistrer' : '<i data-lucide="plus" class="icon-sm"></i> Créer le salarié' ?>
                </button>
                <a href="index.php?page=salaries" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

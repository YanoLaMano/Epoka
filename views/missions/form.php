<?php 
require __DIR__ . '/../layout.php';
$isEdit = isset($mission);
$title = $isEdit ? 'Modifier la mission' : 'Nouvelle mission';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="target" class="icon-sm"></i> <?= $title ?></h1>
        <p class="page-subtitle"><?= $isEdit ? 'Modifier les informations de la mission' : 'Remplissez le formulaire pour créer une mission' ?></p>
    </div>
    <a href="index.php?page=missions" class="btn btn-outline">← Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="intitule">Intitulé de la mission</label>
                <input type="text" id="intitule" name="intitule" class="form-control" 
                       value="<?= escape($mission['intitule'] ?? '') ?>" required
                       placeholder="Ex: Réunion client, Formation...">
            </div>

            <?php if (isAdmin() && !$isEdit): ?>
            <div class="form-group">
                <label class="form-label" for="id_salarie">Salarié</label>
                <select id="id_salarie" name="id_salarie" class="form-control" required>
                    <option value="">-- Sélectionner un salarié --</option>
                    <?php foreach ($salaries as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (isset($mission['id_salarie']) && $mission['id_salarie'] == $s['id']) ? 'selected' : '' ?>>
                        <?= escape($s['prenom']) ?> <?= escape($s['nom']) ?> (<?= escape($s['fonction']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="date_debut">Date de début</label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control" 
                           value="<?= escape($mission['date_debut'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control" 
                           value="<?= escape($mission['date_fin'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ville_depart_search">Ville de départ</label>
                    <div class="search-wrapper">
                        <input type="text" id="ville_depart_search" class="form-control" 
                               placeholder="Tapez le nom d'une ville..."
                               value="<?= $isEdit ? escape($mission['ville_depart_nom'] . ' (' . $mission['cp_depart'] . ')') : '' ?>"
                               autocomplete="off">
                        <input type="hidden" id="id_ville_depart" name="id_ville_depart" 
                               value="<?= escape($mission['id_ville_depart'] ?? '') ?>" required>
                        <div id="ville_depart_results" class="search-results"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="ville_arrivee_search">Ville d'arrivée</label>
                    <div class="search-wrapper">
                        <input type="text" id="ville_arrivee_search" class="form-control" 
                               placeholder="Tapez le nom d'une ville..."
                               value="<?= $isEdit ? escape($mission['ville_arrivee_nom'] . ' (' . $mission['cp_arrivee'] . ')') : '' ?>"
                               autocomplete="off">
                        <input type="hidden" id="id_ville_arrivee" name="id_ville_arrivee" 
                               value="<?= escape($mission['id_ville_arrivee'] ?? '') ?>" required>
                        <div id="ville_arrivee_results" class="search-results"></div>
                    </div>
                </div>
            </div>

            <!-- Champ nombre de repas -->
            <div class="form-group">
                <label class="form-label" for="nb_repas">
                    <i data-lucide="utensils" class="icon-sm"></i> Nombre de repas
                </label>
                <div class="km-input-wrapper">
                    <input type="number" id="nb_repas" name="nb_repas" class="form-control"
                           placeholder="Ex: 3" min="0" step="1"
                           value="<?= escape((string)($mission['nb_repas'] ?? 0)) ?>">
                    <span id="repas-unit">repas</span>
                </div>
            </div>

            <!-- Champ kilomètres -->
            <div class="form-group" id="km-group">
                <label class="form-label" for="km">
                    <i data-lucide="route" class="icon-sm"></i> Distance (km)
                    <span id="km-badge" class="km-badge-prefilled" style="display:none">
                        <i data-lucide="database" style="width:11px;height:11px;margin-right:2px;"></i> Pré-rempli
                    </span>
                </label>
                <div class="km-input-wrapper">
                    <input type="number" id="km" name="km" class="form-control"
                           placeholder="Ex: 42"
                           min="1" step="1"
                           value="<?= escape($currentKm ?? '') ?>">
                    <span id="km-unit">km</span>
                </div>
                <p id="km-hint" class="km-hint" style="display:none"></p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '<i data-lucide="save" class="icon-sm"></i> Enregistrer' : '<i data-lucide="plus" class="icon-sm"></i> Créer la mission' ?>
                </button>
                <a href="index.php?page=missions" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<style>
.km-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.km-input-wrapper #km {
    padding-right: 3.5rem;
}
#km-unit, #repas-unit {
    position: absolute;
    right: 1rem;
    color: var(--text-muted);
    font-size: 0.9rem;
    pointer-events: none;
}
.km-badge-prefilled {
    display: inline-flex;
    align-items: center;
    margin-left: 0.5rem;
    padding: 0.15rem 0.5rem;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 600;
    background: var(--success-light);
    color: var(--success);
    vertical-align: middle;
}
.km-hint {
    margin-top: 0.4rem;
    font-size: 0.82rem;
}
.km-hint.known   { color: var(--success); }
.km-hint.unknown { color: var(--text-muted); }
</style>

<script>
(function () {
    const hiddenDepart  = document.getElementById('id_ville_depart');
    const hiddenArrivee = document.getElementById('id_ville_arrivee');
    const kmInput       = document.getElementById('km');
    const kmBadge       = document.getElementById('km-badge');
    const kmHint        = document.getElementById('km-hint');

    if (!hiddenDepart || !hiddenArrivee || !kmInput) return;

    async function checkDistance() {
        const idD = hiddenDepart.value;
        const idA = hiddenArrivee.value;
        if (!idD || !idA) {
            kmBadge.style.display = 'none';
            kmHint.style.display  = 'none';
            return;
        }
        try {
            const res  = await fetch('index.php?api=get_distance&id_depart=' + idD + '&id_arrivee=' + idA);
            const data = await res.json();
            if (data.km !== null) {
                kmInput.value = data.km;
                kmBadge.style.display = 'inline-flex';
                kmHint.style.display  = 'none';
            } else {
                if (!kmInput.value) {
                    kmBadge.style.display = 'none';
                    kmHint.className = 'km-hint unknown';
                    kmHint.textContent = 'Distance inconnue — saisissez les kilomètres pour l\'enregistrer.';
                    kmHint.style.display = 'block';
                }
            }
            lucide.createIcons();
        } catch (e) { /* silencieux */ }
    }

    hiddenDepart.addEventListener('ville-selected',  checkDistance);
    hiddenArrivee.addEventListener('ville-selected', checkDistance);

    // Déclenchement immédiat si les deux villes sont déjà remplies (mode édition)
    if (hiddenDepart.value && hiddenArrivee.value) checkDistance();
})();
</script>

<?php require __DIR__ . '/../layout_footer.php'; ?>

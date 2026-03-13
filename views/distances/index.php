<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="ruler" class="icon-sm"></i> Distances</h1>
        <p class="page-subtitle">Distances entre les villes</p>
    </div>
    <?php if (isAdmin()): ?>
    <a href="index.php?page=distances&action=create" class="btn btn-primary"><i data-lucide="plus" class="icon-sm"></i> Nouvelle distance</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($distances)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i data-lucide="ruler" class="icon-sm"></i></div>
            <p>Aucune distance enregistrée.</p>
            <?php if (isAdmin()): ?>
            <a href="index.php?page=distances&action=create" class="btn btn-primary">Ajouter une distance</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ville de départ</th>
                        <th>Ville d'arrivée</th>
                        <th>Distance (km)</th>
                        <?php if (isAdmin()): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distances as $d): ?>
                    <tr>
                        <td>#<?= $d['id'] ?></td>
                        <td><strong><?= escape($d['ville_depart_nom']) ?></strong> (<?= escape($d['cp_depart']) ?>)</td>
                        <td><strong><?= escape($d['ville_arrivee_nom']) ?></strong> (<?= escape($d['cp_arrivee']) ?>)</td>
                        <td><strong><?= $d['km'] ?> km</strong></td>
                        <?php if (isAdmin()): ?>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?page=distances&action=edit&id=<?= $d['id'] ?>" class="btn btn-sm btn-primary"><i data-lucide="edit-3" class="icon-sm"></i></a>
                                <a href="index.php?page=distances&action=delete&id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette distance ?')"><i data-lucide="trash-2" class="icon-sm"></i></a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>

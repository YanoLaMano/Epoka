<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="building" class="icon-sm"></i> Agences</h1>
        <p class="page-subtitle">Gestion des agences</p>
    </div>
    <?php if (isAdmin()): ?>
    <a href="index.php?page=agences&action=create" class="btn btn-primary"><i data-lucide="plus" class="icon-sm"></i> Nouvelle agence</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($agences)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i data-lucide="building" class="icon-sm"></i></div>
            <p>Aucune agence enregistrée.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Adresse</th>
                        <th>Ville</th>
                        <th>Code Postal</th>
                        <?php if (isAdmin()): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agences as $a): ?>
                    <tr>
                        <td>#<?= $a['id'] ?></td>
                        <td><strong><?= escape($a['adresse']) ?></strong></td>
                        <td><?= escape($a['ville_nom']) ?></td>
                        <td><?= escape($a['code_postal']) ?></td>
                        <?php if (isAdmin()): ?>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?page=agences&action=edit&id=<?= $a['id'] ?>" class="btn btn-sm btn-primary"><i data-lucide="edit-3" class="icon-sm"></i></a>
                                <a href="index.php?page=agences&action=delete&id=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette agence ?')"><i data-lucide="trash-2" class="icon-sm"></i></a>
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

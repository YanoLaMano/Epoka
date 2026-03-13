<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="users" class="icon-sm"></i> Salariés</h1>
        <p class="page-subtitle">Gestion des salariés de l'entreprise</p>
    </div>
    <?php if (isAdmin()): ?>
    <a href="index.php?page=salaries&action=create" class="btn btn-primary"><i data-lucide="plus" class="icon-sm"></i> Nouveau salarié</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($salaries)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i data-lucide="users" class="icon-sm"></i></div>
            <p>Aucun salarié enregistré.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Fonction</th>
                        <th>Agence</th>
                        <th>Droits</th>
                        <?php if (isAdmin()): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): ?>
                    <tr>
                        <td>#<?= $s['id'] ?></td>
                        <td><strong><?= escape($s['nom']) ?></strong></td>
                        <td><?= escape($s['prenom']) ?></td>
                        <td><?= escape($s['fonction']) ?></td>
                        <td><?= escape($s['agence_ville'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($s['peut_valider'] && $s['peut_payer']): ?>
                                <span class="badge badge-admin"><i data-lucide="shield" class="icon-sm"></i> Admin</span>
                            <?php elseif ($s['peut_valider']): ?>
                                <span class="badge badge-valideur"><i data-lucide="check-circle" class="icon-sm"></i> Valideur</span>
                            <?php elseif ($s['peut_payer']): ?>
                                <span class="badge badge-payeur"><i data-lucide="credit-card" class="icon-sm"></i> Payeur</span>
                            <?php else: ?>
                                <span class="badge badge-brouillon"><i data-lucide="user" class="icon-sm"></i> Salarié</span>
                            <?php endif; ?>
                        </td>
                        <?php if (isAdmin()): ?>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?page=salaries&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-primary"><i data-lucide="edit-3" class="icon-sm"></i></a>
                                <a href="index.php?page=salaries&action=delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce salarié ?')"><i data-lucide="trash-2" class="icon-sm"></i></a>
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

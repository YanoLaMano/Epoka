<?php require __DIR__ . '/../layout.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="target" class="icon-sm"></i> Missions</h1>
        <p class="page-subtitle">Liste de toutes les missions</p>
    </div>
    <a href="index.php?page=missions&action=create" class="btn btn-primary"><i data-lucide="plus" class="icon-sm"></i> Nouvelle mission</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($missions)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i data-lucide="target" class="icon-sm"></i></div>
            <p>Aucune mission enregistrée.</p>
            <a href="index.php?page=missions&action=create" class="btn btn-primary">Créer une mission</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Intitulé</th>
                        <?php if (isAdmin() || canValidate() || canPay()): ?>
                        <th>Salarié</th>
                        <?php endif; ?>
                        <th>Dates</th>
                        <th>Trajet</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missions as $m): ?>
                    <tr>
                        <td>#<?= $m['id'] ?></td>
                        <td><strong><?= escape($m['intitule']) ?></strong></td>
                        <?php if (isAdmin() || canValidate() || canPay()): ?>
                        <td><?= escape($m['salarie_prenom'] ?? '') ?> <?= escape($m['salarie_nom'] ?? '') ?></td>
                        <?php endif; ?>
                        <td><?= formatDate($m['date_debut']) ?> → <?= formatDate($m['date_fin']) ?></td>
                        <td><?= escape($m['ville_depart_nom']) ?> → <?= escape($m['ville_arrivee_nom']) ?></td>
                        <td>
                            <span class="badge badge-<?= $m['statut'] ?>">
                                <?= $m['statut'] === 'brouillon' ? '<i data-lucide="file-edit" class="icon-sm"></i>' : ($m['statut'] === 'validee' ? '<i data-lucide="check-circle" class="icon-sm"></i>' : '<i data-lucide="credit-card" class="icon-sm"></i>') ?>
                                <?= escape($m['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;justify-content:space-between;min-width:160px;">
                                <div class="btn-group">
                                    <a href="index.php?page=missions&action=voir&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline"><i data-lucide="eye" class="icon-sm"></i></a>
                                    <?php if ($m['statut'] === 'brouillon'): ?>
                                        <?php if ($m['id_salarie'] == $_SESSION['salarie_id'] || isAdmin()): ?>
                                        <a href="index.php?page=missions&action=edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-primary"><i data-lucide="edit-3" class="icon-sm"></i></a>
                                        <?php endif; ?>
                                        <?php if (canValidate()): ?>
                                        <a href="index.php?page=missions&action=valider&id=<?= $m['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Valider cette mission ?')"><i data-lucide="check-circle" class="icon-sm"></i> Valider</a>
                                        <?php endif; ?>
                                    <?php elseif ($m['statut'] === 'validee' && canPay()): ?>
                                        <a href="index.php?page=missions&action=payer&id=<?= $m['id'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Marquer comme payée ?')"><i data-lucide="credit-card" class="icon-sm"></i> Payer</a>
                                    <?php endif; ?>
                                </div>
                                <?php if ($m['id_salarie'] == $_SESSION['salarie_id'] || isAdmin()): ?>
                                <a href="index.php?page=missions&action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette mission ?')" title="Supprimer"><i data-lucide="trash-2" class="icon-sm"></i></a>
                                <?php else: ?>
                                <span style="width:2rem;"></span>
                                <?php endif; ?>
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

<?php require __DIR__ . '/../layout_footer.php'; ?>

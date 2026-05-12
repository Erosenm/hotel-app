<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Gestión de Usuarios</h4>
    <a href="<?= url('admin/usuarios/crear') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Usuario
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>CI</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <span class="badge bg-primary me-2"><?= strtoupper(substr($u['nombre'], 0, 1)) ?></span>
                        <?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['ci']) ?></td>
                    <td><?= htmlspecialchars($u['telefono'] ?? '-') ?></td>
                    <td><span class="badge bg-secondary"><?= $u['rol'] ?? 'Sin rol' ?></span></td>
                    <td>
                        <?php $color = $u['estado'] === 'Activo' ? 'success' : ($u['estado'] === 'Suspendido' ? 'warning' : 'danger') ?>
                        <span class="badge bg-<?= $color ?>"><?= $u['estado'] ?></span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/usuarios/editar?id=' . $u['idUsuario']) ?>" 
                           class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($u['estado'] !== 'Suspendido'): ?>
                        <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Suspendido') ?>"
                           class="btn btn-sm btn-outline-warning" title="Suspender">
                            <i class="fas fa-ban"></i>
                        </a>
                        <?php else: ?>
                        <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Activo') ?>"
                           class="btn btn-sm btn-outline-success" title="Activar">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
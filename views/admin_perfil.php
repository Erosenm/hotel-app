<!-- HEADER -->
 <link rel="stylesheet" href="<?= asset('css/styleAdmin.css') ?>">
<div class="page-header d-flex justify-content-between align-items-center mb-5">
    <div class="header-content">
        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>
            Gestión de Usuarios
        </h1>
        <p class="page-subtitle text-muted">Administra usuarios, roles y estados</p>
    </div>

    <a href="<?= url('register') ?>" class="btn btn-primary btn-lg shadow-sm">
        <i class="fas fa-plus me-2"></i>
        Nuevo Usuario
    </a>
</div>

<!-- SEARCH & FILTERS -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="search-box">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" 
                       placeholder="Buscar usuarios...">
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select form-select-lg">
            <option>Todos los roles</option>
            <option>Administrador</option>
            <option>Usuario</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select form-select-lg">
            <option>Todos los estados</option>
            <option>Activo</option>
            <option>Inactivo</option>
        </select>
    </div>
</div>

<!-- MAIN CARD -->
<div class="card shadow-xl border-0">
    <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="fas fa-list me-2 text-primary"></i>
                Lista de Usuarios
            </h5>
            <div class="stats-info d-flex gap-3">
                <span class="badge bg-primary fs-6">
                    <?= count($usuarios) ?> Total
                </span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user text-muted"></i>
                                <span>Nombre</span>
                            </div>
                        </th>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-envelope text-muted"></i>
                                <span>Correo</span>
                            </div>
                        </th>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-id-card text-muted"></i>
                                <span>CI</span>
                            </div>
                        </th>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-phone text-muted"></i>
                                <span>Teléfono</span>
                            </div>
                        </th>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-tag text-muted"></i>
                                <span>Rol</span>
                            </div>
                        </th>
                        <th class="border-0 py-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-circle-check text-muted"></i>
                                <span>Estado</span>
                            </div>
                        </th>
                        <th class="border-0 py-4 text-end">
                            <div class="d-flex align-items-center gap-2 justify-content-end">
                                <i class="fas fa-cogs text-muted"></i>
                                <span>Acciones</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr class="table-row">
                        <td class="fw-semibold text-dark">
                            <div class="user-info">
                                <div class="user-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3">
                                    <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                </div>
                                <?= $u['nombre'] . ' ' . $u['paterno'] ?>
                            </div>
                        </td>
                        <td>
                            <div class="email-cell">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <?= $u['email'] ?>
                            </div>
                        </td>
                        <td><strong><?= $u['ci'] ?></strong></td>
                        <td>
                            <div class="phone-cell">
                                <i class="fas fa-phone text-success me-2"></i>
                                <?= $u['telefono'] ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-role-lg">
                                <?= $u['rol'] ?? 'Sin rol' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['estado'] == 'Activo'): ?>
                                <span class="badge badge-success-lg">
                                    <i class="fas fa-circle-check me-1"></i>Activo
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger-lg">
                                    <i class="fas fa-circle-xmark me-1"></i><?= $u['estado'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="action-buttons">
                                <a href="<?= url('editarUsuario?id=' . $u['idUsuario']) ?>" 
                                   class="btn btn-icon btn-outline-primary me-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= url('suspenderUsuario?id=' . $u['idUsuario']) ?>" 
                                   class="btn btn-icon btn-outline-warning me-1" title="Suspender">
                                    <i class="fas fa-pause"></i>
                                </a>
                                <a href="<?= url('activarUsuario?id=' . $u['idUsuario']) ?>" 
                                   class="btn btn-icon btn-outline-success" title="Activar">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 pt-0">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Mostrando <?= count($usuarios) ?> usuarios</small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
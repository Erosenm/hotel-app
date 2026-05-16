<link rel="stylesheet" href="<?= url('public/css/cssAdmin/styleUsuarios/styleUsuarioindex.css') ?>">
<?php
$usuarios = $usuarios ?? [];
?>
<!-- ═════════ HEADER ═════════ -->
<div class="ui-page-header">

    <h4 class="ui-page-title">
        <i class="fas fa-users"></i>
        Gestión de Usuarios
    </h4>

    <a href="<?= url('admin/usuarios/crear') ?>" class="ui-btn-primary">
        <i class="fas fa-plus"></i>
        Nuevo Usuario
    </a>

</div>

<!-- ═════════ FILTROS ═════════ -->
<div class="ui-filters-bar">

    <!-- Buscar -->
    <div class="ui-search-wrap">
        <i class="fas fa-search"></i>

        <input type="text"
               id="uiSearch"
               placeholder="Buscar por nombre, email o CI...">
    </div>

    <!-- Filtros -->
    <div class="ui-filter-group">

        <select id="filterRol" class="ui-select">
            <option value="">Todos los roles</option>

            <?php
            $roles = [
                'Administrador',
                'Recepcionista',
                'Cliente',
                'Gerente',
                'Contador',
                'Limpieza',
                'Mantenimiento'
            ];

            foreach ($roles as $rol): ?>

                <option value="<?= strtolower($rol) ?>">
                    <?= $rol ?>
                </option>

            <?php endforeach; ?>

        </select>

        <select id="filterEstado" class="ui-select">
            <option value="">Todos los estados</option>
            <option value="activo">Activo</option>
            <option value="suspendido">Suspendido</option>
            <option value="inactivo">Inactivo</option>
        </select>

    </div>

    <!-- Contador -->
    <div class="ui-results-count" id="uiCount">
        0 usuarios
    </div>

</div>

<!-- ═════════ TABLA ═════════ -->
<div class="ui-table-card">

    <div class="table-responsive">

        <table class="ui-table">

            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>CI</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody id="uiTbody">

            <?php foreach ($usuarios as $u): ?>

                <?php
                    $rolClass = [
                        'Administrador' => 'danger',
                        'Recepcionista' => 'primary',
                        'Cliente'       => 'success',
                        'Gerente'       => 'warning',
                        'Contador'      => 'info',
                        'Limpieza'      => 'secondary',
                        'Mantenimiento' => 'dark',
                    ];

                    $estadoClass = match($u['estado']) {
                        'Activo'      => 'success',
                        'Suspendido'  => 'warning',
                        default       => 'danger'
                    };

                    $avatar = strtoupper(substr($u['nombre'], 0, 1));
                ?>

                <tr
                    data-nombre="<?= strtolower($u['nombre'] . ' ' . $u['paterno']) ?>"
                    data-email="<?= strtolower($u['email']) ?>"
                    data-ci="<?= strtolower($u['ci']) ?>"
                    data-rol="<?= strtolower($u['rol'] ?? '') ?>"
                    data-estado="<?= strtolower($u['estado']) ?>"
                >

                    <!-- NOMBRE -->
                    <td>

                        <div class="ui-user-cell">

                            <div class="ui-avatar">
                                <?= $avatar ?>
                            </div>

                            <div class="ui-user-name">
                                <?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?>
                            </div>

                        </div>

                    </td>

                    <!-- EMAIL -->
                    <td>

                        <span class="ui-email">
                            <?= htmlspecialchars($u['email']) ?>
                        </span>

                    </td>

                    <!-- CI -->
                    <td>

                        <span class="ui-ci">
                            <?= htmlspecialchars($u['ci']) ?>
                        </span>

                    </td>

                    <!-- TEL -->
                    <td>

                        <span class="ui-phone">
                            <?= htmlspecialchars($u['telefono'] ?? '-') ?>
                        </span>

                    </td>

                    <!-- ROL -->
                    <td>

                        <span class="ui-badge-rol">
                            <?= htmlspecialchars($u['rol'] ?? 'Sin rol') ?>
                        </span>

                    </td>

                    <!-- ESTADO -->
                    <td>

                        <span class="ui-badge-estado <?= $estadoClass ?>">
                            <?= htmlspecialchars($u['estado']) ?>
                        </span>

                    </td>

                    <!-- ACCIONES -->
                    <td>

                        <div class="ui-actions">

                            <!-- Editar -->
                            <a href="<?= url('admin/usuarios/editar?id=' . $u['idUsuario']) ?>"
                               class="ui-action-btn edit"
                               title="Editar">

                                <i class="fas fa-edit"></i>

                            </a>

                            <!-- Suspender / Activar -->
                            <?php if ($u['estado'] !== 'Suspendido'): ?>

                                <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Suspendido') ?>"
                                   class="ui-action-btn suspend"
                                   title="Suspender">

                                    <i class="fas fa-ban"></i>

                                </a>

                            <?php else: ?>

                                <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Activo') ?>"
                                   class="ui-action-btn activate"
                                   title="Activar">

                                    <i class="fas fa-check"></i>

                                </a>

                            <?php endif; ?>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<!-- ═════════ EMPTY STATE ═════════ -->
<div class="ui-empty" id="uiEmpty" style="display:none;">

    <i class="fas fa-users-slash"></i>

    <p>No se encontraron usuarios</p>

</div>

<!-- ═════════ PAGINACIÓN ═════════ -->
<div class="ui-pagination-wrap">

    <!-- Por página -->
    <div class="ui-per-page">

        Mostrar

        <select id="uiPerPage" class="ui-select-sm">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>

        registros

    </div>

    <!-- Botones -->
    <div class="ui-pagination" id="uiPagination"></div>

</div>

<!-- ═════════ SCRIPT ═════════ -->
<script>
(function () {

    var tbody      = document.getElementById('uiTbody');
    var searchInp  = document.getElementById('uiSearch');
    var filterRol  = document.getElementById('filterRol');
    var filterEst  = document.getElementById('filterEstado');
    var perPageSel = document.getElementById('uiPerPage');
    var pagination = document.getElementById('uiPagination');
    var countEl    = document.getElementById('uiCount');
    var emptyEl    = document.getElementById('uiEmpty');
    var tableCard  = document.querySelector('.ui-table-card');

    var allRows     = Array.from(tbody.querySelectorAll('tr'));
    var filtered    = allRows.slice();
    var perPage     = 10;
    var currentPage = 1;

    function applyFilters() {

        var q   = searchInp.value.trim().toLowerCase();
        var rol = filterRol.value.toLowerCase();
        var est = filterEst.value.toLowerCase();

        filtered = allRows.filter(function(row) {

            var nombre = row.dataset.nombre || '';
            var email  = row.dataset.email || '';
            var ci     = row.dataset.ci || '';
            var rRol   = row.dataset.rol || '';
            var rEst   = row.dataset.estado || '';

            var matchQ =
                !q ||
                nombre.includes(q) ||
                email.includes(q) ||
                ci.includes(q);

            var matchRol = !rol || rRol === rol;
            var matchEst = !est || rEst === est;

            return matchQ && matchRol && matchEst;
        });

        currentPage = 1;

        render();
    }

    function render() {

        var total = filtered.length;

        var pages = Math.max(
            1,
            Math.ceil(total / perPage)
        );

        if (currentPage > pages) {
            currentPage = pages;
        }

        var start = (currentPage - 1) * perPage;
        var end   = start + perPage;

        allRows.forEach(function(r) {
            r.style.display = 'none';
        });

        filtered.slice(start, end).forEach(function(r) {
            r.style.display = '';
        });

        countEl.textContent =
            total + (total === 1 ? ' usuario' : ' usuarios');

        emptyEl.style.display =
            total === 0 ? 'flex' : 'none';

        tableCard.style.display =
            total === 0 ? 'none' : '';

        renderPagination(pages);
    }

    function renderPagination(pages) {

        pagination.innerHTML = '';

        if (pages <= 1) return;

        function btn(label, page, disabled, active) {

            var a = document.createElement('button');

            a.innerHTML = label;

            a.className =
                'ui-page-btn' +
                (active ? ' active' : '') +
                (disabled ? ' disabled' : '');

            if (!disabled && !active) {

                a.addEventListener('click', function () {

                    currentPage = page;

                    render();

                });

            }

            return a;
        }

        pagination.appendChild(
            btn('&laquo;', 1, currentPage === 1)
        );

        pagination.appendChild(
            btn('&lsaquo;', currentPage - 1, currentPage === 1)
        );

        var range = [];

        for (var i = 1; i <= pages; i++) {

            if (
                i === 1 ||
                i === pages ||
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                range.push(i);
            }
        }

        var prev = null;

        range.forEach(function (p) {

            if (prev !== null && p - prev > 1) {

                var dots = document.createElement('span');

                dots.className = 'ui-page-dots';

                dots.textContent = '…';

                pagination.appendChild(dots);
            }

            pagination.appendChild(
                btn(p, p, false, p === currentPage)
            );

            prev = p;
        });

        pagination.appendChild(
            btn('&rsaquo;', currentPage + 1, currentPage === pages)
        );

        pagination.appendChild(
            btn('&raquo;', pages, currentPage === pages)
        );
    }

    searchInp.addEventListener('input', applyFilters);

    filterRol.addEventListener('change', applyFilters);

    filterEst.addEventListener('change', applyFilters);

    perPageSel.addEventListener('change', function () {

        perPage = parseInt(this.value);

        currentPage = 1;

        render();

    });

    render();

})();
</script>
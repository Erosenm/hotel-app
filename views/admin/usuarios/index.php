<link rel="stylesheet" href="<?= url('public/css/cssAdmin/styleUsuarios/styleUsuarioindex.css') ?>">
<!-- ══ Header ══ -->
<div class="ui-page-header">
    <div class="ui-page-title">
        <i class="fas fa-users"></i>
        <span>Gestión de Usuarios</span>
    </div>
    <a href="<?= url('admin/usuarios/crear') ?>" class="ui-btn-primary">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </a>
</div>

<!-- ══ Filtros y búsqueda ══ -->
<div class="ui-filters-bar">
    <div class="ui-search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="uiSearch" placeholder="Buscar por nombre, email o CI..." autocomplete="off">
    </div>
    <div class="ui-filter-group">
        <select id="filterRol" class="ui-select">
            <option value="">Todos los roles</option>
            <option value="Administrador">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
            <option value="Cliente">Cliente</option>
        </select>
        <select id="filterEstado" class="ui-select">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Suspendido">Suspendido</option>
            <option value="Inactivo">Inactivo</option>
        </select>
    </div>
    <div class="ui-results-count">
        <span id="uiCount">— usuarios</span>
    </div>
</div>

<!-- ══ Tabla ══ -->
<div class="ui-table-card">
    <table class="ui-table" id="uiTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>CI</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Estado</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody id="uiTbody">
        <?php foreach ($usuarios as $u): ?>
            <?php
                $colorEstado = $u['estado'] === 'Activo' ? 'success' : ($u['estado'] === 'Suspendido' ? 'warning' : 'danger');
            ?>
            <tr data-nombre="<?= strtolower(htmlspecialchars($u['nombre'] . ' ' . $u['paterno'])) ?>"
                data-email="<?= strtolower(htmlspecialchars($u['email'])) ?>"
                data-ci="<?= htmlspecialchars($u['ci']) ?>"
                data-rol="<?= htmlspecialchars($u['rol'] ?? '') ?>"
                data-estado="<?= htmlspecialchars($u['estado']) ?>">
                <td>
                    <div class="ui-user-cell">
                        <span class="ui-avatar"><?= strtoupper(substr($u['nombre'], 0, 1)) ?></span>
                        <div>
                            <div class="ui-user-name"><?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?></div>
                        </div>
                    </div>
                </td>
                <td><span class="ui-email"><?= htmlspecialchars($u['email']) ?></span></td>
                <td><span class="ui-ci"><?= htmlspecialchars($u['ci']) ?></span></td>
                <td><span class="ui-phone"><?= htmlspecialchars($u['telefono'] ?? '—') ?></span></td>
                <td><span class="ui-badge-rol"><?= htmlspecialchars($u['rol'] ?? 'Sin rol') ?></span></td>
                <td>
                    <span class="ui-badge-estado <?= $colorEstado ?>">
                        <?= htmlspecialchars($u['estado']) ?>
                    </span>
                </td>
                <td>
                    <div class="ui-actions">
                        <a href="<?= url('admin/usuarios/editar?id=' . $u['idUsuario']) ?>"
                           class="ui-action-btn edit" title="Editar usuario">
                            <i class="fas fa-pen"></i>
                        </a>
                        <?php if ($u['estado'] !== 'Suspendido'): ?>
                        <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Suspendido') ?>"
                           class="ui-action-btn suspend" title="Suspender usuario">
                            <i class="fas fa-ban"></i>
                        </a>
                        <?php else: ?>
                        <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Activo') ?>"
                           class="ui-action-btn activate" title="Activar usuario">
                            <i class="fas fa-check"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Empty state -->
    <div class="ui-empty" id="uiEmpty" style="display:none">
        <i class="fas fa-user-slash"></i>
        <p>No se encontraron usuarios con esos filtros.</p>
    </div>
</div>

<!-- ══ Paginación ══ -->
<div class="ui-pagination-wrap">
    <div class="ui-per-page">
        Mostrar
        <select id="uiPerPage" class="ui-select-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        por página
    </div>
    <div class="ui-pagination" id="uiPagination"></div>
</div>

<!-- ══ Script filtros + paginación (solo frontend) ══ -->
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
    var tableCard  = document.querySelector('.ui-table-card table');

    var allRows    = Array.from(tbody.querySelectorAll('tr'));
    var filtered   = allRows.slice();
    var perPage    = 10;
    var currentPage = 1;

    function applyFilters() {
        var q   = searchInp.value.trim().toLowerCase();
        var rol = filterRol.value.toLowerCase();
        var est = filterEst.value.toLowerCase();

        filtered = allRows.filter(function(row) {
            var nombre = row.dataset.nombre || '';
            var email  = row.dataset.email  || '';
            var ci     = row.dataset.ci     || '';
            var rRol   = (row.dataset.rol   || '').toLowerCase();
            var rEst   = (row.dataset.estado|| '').toLowerCase();

            var matchQ   = !q   || nombre.includes(q) || email.includes(q) || ci.includes(q);
            var matchRol = !rol || rRol === rol;
            var matchEst = !est || rEst === est;
            return matchQ && matchRol && matchEst;
        });

        currentPage = 1;
        render();
    }

    function render() {
        var total = filtered.length;
        var pages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > pages) currentPage = pages;

        var start = (currentPage - 1) * perPage;
        var end   = start + perPage;

        /* Ocultar todas las filas */
        allRows.forEach(function(r){ r.style.display = 'none'; });

        /* Mostrar solo las de esta página */
        filtered.slice(start, end).forEach(function(r){ r.style.display = ''; });

        /* Contador */
        countEl.textContent = total + (total === 1 ? ' usuario' : ' usuarios');

        /* Empty state */
        emptyEl.style.display  = total === 0 ? 'flex' : 'none';
        tableCard.style.display = total === 0 ? 'none' : '';

        renderPagination(pages);
    }

    function renderPagination(pages) {
        pagination.innerHTML = '';
        if (pages <= 1) return;

        function btn(label, page, disabled, active) {
            var a = document.createElement('button');
            a.innerHTML = label;
            a.className = 'ui-page-btn' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
            if (!disabled && !active) {
                a.addEventListener('click', function(){ currentPage = page; render(); });
            }
            return a;
        }

        pagination.appendChild(btn('&laquo;', 1, currentPage === 1));
        pagination.appendChild(btn('&lsaquo;', currentPage - 1, currentPage === 1));

        var range = [];
        for (var i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                range.push(i);
            }
        }
        var prev = null;
        range.forEach(function(p) {
            if (prev !== null && p - prev > 1) {
                var dots = document.createElement('span');
                dots.className = 'ui-page-dots';
                dots.textContent = '…';
                pagination.appendChild(dots);
            }
            pagination.appendChild(btn(p, p, false, p === currentPage));
            prev = p;
        });

        pagination.appendChild(btn('&rsaquo;', currentPage + 1, currentPage === pages));
        pagination.appendChild(btn('&raquo;', pages, currentPage === pages));
    }

    searchInp.addEventListener('input', applyFilters);
    filterRol.addEventListener('change', applyFilters);
    filterEst.addEventListener('change', applyFilters);
    perPageSel.addEventListener('change', function(){ perPage = parseInt(this.value); currentPage = 1; render(); });

    /* Render inicial */
    render();
})();
</script>
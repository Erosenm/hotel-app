<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylebitacora/index.css') ?>">

<div class="bit-container">
    <!-- Header -->
    <div class="bit-header">
        <h4>
            <i class="fas fa-clipboard-list"></i>
            Bitácora del Sistema
        </h4>
    </div>

    <!-- STATS PREMIUM CON BARRA SUPERIOR -->
    <div class="bit-stats-row">
        <div class="bit-stat-card total">
            <div class="bit-stat-number"><?= number_format($stats['total']) ?></div>
            <div class="bit-stat-label">Total registros</div>
        </div>
        <div class="bit-stat-card hoy">
            <div class="bit-stat-number"><?= number_format($stats['hoy']) ?></div>
            <div class="bit-stat-label">Registros hoy</div>
        </div>
        <div class="bit-stat-card semana">
            <div class="bit-stat-number"><?= number_format($stats['semana']) ?></div>
            <div class="bit-stat-label">Últimos 7 días</div>
        </div>
    </div>

    <!-- FILTROS (sin rol) -->
    <div class="bit-filtro-card">
        <div class="bit-filtro-body">
            <form method="GET" action="<?= url('admin/bitacora') ?>" id="filtroForm">
                <div class="bit-filtro-row">
                    <div class="bit-filtro-group" style="flex: 2;">
                        <div class="bit-filtro-label">
                            <i class="fas fa-user"></i> Buscar usuario
                        </div>
                        <input type="text" name="usuario" class="bit-filtro-input"
                               placeholder="Nombre, apellido o email..."
                               value="<?= htmlspecialchars($filtroUsuario) ?>">
                    </div>
                    
                    <div class="bit-filtro-group">
                        <div class="bit-filtro-label">
                            <i class="fas fa-calendar-alt"></i> Fecha
                        </div>
                        <input type="date" name="fecha" class="bit-filtro-input"
                               value="<?= htmlspecialchars($filtroFecha) ?>">
                    </div>
                    
                    <div class="bit-filtro-buttons">
                        <button type="submit" class="bit-btn-primary">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="<?= url('admin/bitacora') ?>" class="bit-btn-secondary">
                            <i class="fas fa-undo-alt"></i> Limpiar
                        </a>
                        <button type="button" class="bit-btn-export" onclick="exportarBitacora()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Filtros activos visibles -->
            <?php if (!empty($filtroUsuario) || !empty($filtroFecha)): ?>
            <div class="bit-filtros-activos">
                <span style="font-size:0.7rem; color:var(--bit-muted);">Filtros activos:</span>
                <?php if (!empty($filtroUsuario)): ?>
                <span class="bit-filtro-activo">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($filtroUsuario) ?>
                    <i class="fas fa-times-circle" onclick="removerFiltro('usuario')"></i>
                </span>
                <?php endif; ?>
                <?php if (!empty($filtroFecha)): ?>
                <span class="bit-filtro-activo">
                    <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($filtroFecha)) ?>
                    <i class="fas fa-times-circle" onclick="removerFiltro('fecha')"></i>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TABLA DE REGISTROS -->
    <div class="bit-table-card">
        <div class="table-responsive">
            <table class="bit-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Usuario</th>
                        <th style="width: 110px;">Rol</th>
                        <th>Acción</th>
                        <th style="width: 160px;">Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="5">
                            <div class="bit-empty">
                                <i class="fas fa-clipboard-list"></i>
                                <p>No hay registros en la bitácora</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $b): 
                        // Limpiar la acción: eliminar "ID: X" y patrones similares
                        $accionLimpia = $b['accion'];
                        $accionLimpia = preg_replace('/\s*ID\s*:\s*\d+/i', '', $accionLimpia);
                        $accionLimpia = preg_replace('/\s*id\s*\d+/i', '', $accionLimpia);
                        $accionLimpia = preg_replace('/\s*#\d+/i', '', $accionLimpia);
                        $accionLimpia = trim($accionLimpia);
                    ?>
                    <tr>
                        <td class="bit-id">#<?= $b['idBitacora'] ?></td>
                        <td>
                            <div class="bit-user">
                                <div class="bit-user-initial">
                                    <?= strtoupper(substr($b['usuario_nombre'] ?? '?', 0, 1)) ?>
                                </div>
                                <div class="bit-user-info">
                                    <div class="bit-user-name">
                                        <?= htmlspecialchars($b['usuario_nombre'] . ' ' . ($b['usuario_paterno'] ?? '')) ?>
                                    </div>
                                    <div class="bit-user-email">
                                        <?= htmlspecialchars($b['usuario_email'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $rolClass = match($b['rol']) {
                                    'Administrador' => 'admin',
                                    'Recepcionista' => 'recep',
                                    'Cliente' => 'cliente',
                                    default => ''
                                };
                            ?>
                            <span class="bit-role-badge <?= $rolClass ?>">
                                <?= htmlspecialchars($b['rol'] ?? 'Sin rol') ?>
                            </span>
                        </td>
                        <td>
                            <span class="bit-accion">
                                <?= htmlspecialchars($accionLimpia) ?>
                            </span>
                        </td>
                        <td class="bit-fecha">
                            <i class="far fa-calendar-alt"></i>
                            <?= date('d/m/Y H:i:s', strtotime($b['fechaHora'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="bit-footer">
            <span>
                <i class="fas fa-chart-line"></i>
                Mostrando <strong><?= count($registros) ?></strong> de <strong><?= number_format($stats['total']) ?></strong> registros
            </span>
            <?php if (!empty($filtroUsuario) || !empty($filtroFecha)): ?>
                <span class="text-warning">
                    <i class="fas fa-filter"></i> Filtros aplicados
                </span>
            <?php else: ?>
                <span>
                    <i class="fas fa-history"></i> Últimos registros
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Función para exportar a Excel
function exportarBitacora() {
    const params = new URLSearchParams(window.location.search);
    params.set('exportar', 'excel');
    window.location.href = '<?= url('admin/bitacora') ?>?' + params.toString();
}

// Función para remover filtros individuales
function removerFiltro(tipo) {
    const url = new URL(window.location.href);
    if (tipo === 'usuario') url.searchParams.delete('usuario');
    if (tipo === 'fecha') url.searchParams.delete('fecha');
    window.location.href = url.toString();
}

// Atajo de teclado Ctrl+F para enfocar buscador
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const buscador = document.querySelector('input[name="usuario"]');
        if (buscador) buscador.focus();
    }
});
</script>
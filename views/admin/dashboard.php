<link rel="stylesheet" href="<?= url('public/css/cssAdmin/styleDashboard.css') ?>">

<!-- ══ FILA 1: Stats principales ══ -->
<div class="row g-3 mb-3">

    <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-violet">
            <div class="dash-stat-icon"><i class="fas fa-users"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['total_usuarios'] ?></div>
                <div class="dash-stat-label">Usuarios totales</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_usuarios"></canvas></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-green">
            <div class="dash-stat-icon"><i class="fas fa-bed"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['habitaciones_disponibles'] ?></div>
                <div class="dash-stat-label">Habitaciones disponibles</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_habitaciones"></canvas></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-amber">
            <div class="dash-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['reservas_pendientes'] ?></div>
                <div class="dash-stat-label">Reservas pendientes</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_pendientes"></canvas></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-cyan">
            <div class="dash-stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['reservas_confirmadas'] ?></div>
                <div class="dash-stat-label">Reservas confirmadas</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_confirmadas"></canvas></div>
        </div>
    </div>

</div>

<!-- ══ FILA 2: Stats de cobros ══ -->
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-green">
            <div class="dash-stat-icon"><i class="fas fa-credit-card"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value money">Bs. <?= number_format($stats['monto_hoy'], 2) ?></div>
                <div class="dash-stat-label">Cobrado hoy</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_monto_hoy"></canvas></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-blue">
            <div class="dash-stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value money">Bs. <?= number_format($stats['monto_mes'], 2) ?></div>
                <div class="dash-stat-label">Cobrado este mes</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_monto_mes"></canvas></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-teal">
            <div class="dash-stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['pagos_hoy'] ?></div>
                <div class="dash-stat-label">Pagos hoy</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_pagos_hoy"></canvas></div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dash-stat-card accent-rose">
            <div class="dash-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="dash-stat-body">
                <div class="dash-stat-value"><?= $stats['pagos_pendientes'] ?></div>
                <div class="dash-stat-label">Pagos pendientes</div>
            </div>
            <div class="dash-stat-spark"><canvas id="ck_pagos_pend"></canvas></div>
        </div>
    </div>

</div>

<!-- ══ FILA 3: Gráficos ══ -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="dash-panel">
            <div class="dash-panel-head">
                <span class="dash-panel-title">
                    <i class="fas fa-chart-area" style="color:#3b82f6"></i> Ingresos del mes
                </span>
            </div>
            <div class="dash-panel-body">
                <div class="dash-chart-wrap"><canvas id="ck_ingresos_mes"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="dash-panel">
            <div class="dash-panel-head">
                <span class="dash-panel-title">
                    <i class="fas fa-chart-bar" style="color:#8b5cf6"></i> Resumen
                </span>
            </div>
            <div class="dash-panel-body">
                <div class="dash-chart-wrap"><canvas id="ck_resumen"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- ══ Tabla últimos pagos ══ -->
<div class="dash-panel mb-4">
    <div class="dash-panel-head">
        <span class="dash-panel-title">
            <i class="fas fa-credit-card" style="color:#10b981"></i> Últimos pagos
        </span>
        <a href="<?= url('admin/pagos') ?>" class="dash-panel-btn green">Ver todos</a>
    </div>
    <div class="dash-panel-body p-0">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Recibo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($ultimos_pagos)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-receipt me-1"></i> Sin pagos registrados aún
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ultimos_pagos as $p):
                    $colores = [
                        'Pagado'      => 'success',
                        'Pendiente'   => 'warning',
                        'Cancelado'   => 'danger',
                        'Reembolsado' => 'info',
                        'Parcial'     => 'secondary',
                    ];
                    $color = $colores[$p['estado_pago']] ?? 'secondary';
                    $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                    $icono  = $iconos[$p['metodo_pago']] ?? 'fa-circle';
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="dav-badge dav-blue">
                                <?= strtoupper(substr($p['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <span><?= htmlspecialchars(($p['cliente_nombre'] ?? '') . ' ' . ($p['cliente_paterno'] ?? '')) ?></span>
                        </div>
                    </td>
                    <td><span class="dash-amount">Bs. <?= number_format($p['monto'], 2) ?></span></td>
                    <td>
                        <span class="dash-method">
                            <i class="fas <?= $icono ?>"></i>
                            <?= htmlspecialchars($p['metodo_pago'] ?? '-') ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($p['recibo_numero'])): ?>
                            <span class="dash-recibo"><?= htmlspecialchars($p['recibo_numero']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="dash-datetime">
                            <span class="dash-datetime-date"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($p['fechaPago'])) ?></span>
                            <span class="dash-datetime-time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($p['fechaPago'])) ?></span>
                        </div>
                    </td>
                    <td><span class="dash-badge-status <?= $color ?>"><?= htmlspecialchars($p['estado_pago']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ══ Usuarios recientes (solo Administrador) ══ -->
<?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
<div class="dash-panel mb-4">
    <div class="dash-panel-head">
        <span class="dash-panel-title">
            <i class="fas fa-users" style="color:#3b82f6"></i> Usuarios recientes
        </span>
        <a href="<?= url('admin/usuarios') ?>" class="dash-panel-btn blue">Ver todos</a>
    </div>
    <div class="dash-panel-body p-0">
        <table class="dash-table dash-table-users">
            <colgroup>
                <col style="width:30%">
                <col style="width:22%">
                <col style="width:12%">
                <col style="width:12%">
                <col style="width:12%">
                <col style="width:12%">
            </colgroup>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>CI</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_slice($usuarios, 0, 8) as $u): ?>
                <?php $color = $u['estado'] === 'Activo' ? 'success' : ($u['estado'] === 'Suspendido' ? 'warning' : 'danger') ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="dav-badge dav-violet">
                                <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                            </span>
                            <div class="dash-user-name"><?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?></div>
                        </div>
                    </td>
                    <td><span class="dash-user-email-cell"><?= htmlspecialchars($u['email']) ?></span></td>
                    <td><span class="dash-user-ci"><?= htmlspecialchars($u['ci']) ?></span></td>
                    <td><span class="dash-badge-status secondary"><?= $u['rol'] ?? 'Sin rol' ?></span></td>
                    <td><span class="dash-badge-status <?= $color ?>"><?= $u['estado'] ?></span></td>
                    <td>
                        <div class="dash-actions-cell">
                            <a href="<?= url('admin/usuarios/editar?id=' . $u['idUsuario']) ?>" class="dash-btn-edit" title="Editar usuario">
                                <i class="fas fa-pen"></i>
                            </a>
                            <?php if ($u['estado'] !== 'Suspendido'): ?>
                            <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Suspendido') ?>"
                               class="dash-btn-suspend"
                               title="Suspender usuario">
                                <i class="fas fa-ban"></i>
                            </a>
                            <?php else: ?>
                            <a href="<?= url('admin/usuarios/estado?id=' . $u['idUsuario'] . '&estado=Activo') ?>"
                               class="dash-btn-suspend is-suspended"
                               title="Activar usuario">
                                <i class="fas fa-lock-open"></i>
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
<?php endif; ?>

<!-- ══ Chart.js desde CDN ══ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    var blue   = '#3b82f6', bluef   = 'rgba(59,130,246,.13)';
    var green  = '#10b981', greenf  = 'rgba(16,185,129,.13)';
    var amber  = '#f59e0b', amberf  = 'rgba(245,158,11,.13)';
    var cyan   = '#06b6d4', cyanf   = 'rgba(6,182,212,.13)';
    var violet = '#8b5cf6', violetf = 'rgba(139,92,246,.13)';
    var rose   = '#f43f5e', rosef   = 'rgba(244,63,94,.13)';

    function asc(end, n) {
        n = n || 10;
        if (!end || end === 0) return Array(n).fill(0);
        var arr = [];
        for (var i = 0; i < n; i++) {
            var t = (i + 1) / n;
            arr.push(Math.max(0, end * t + (Math.random() - 0.4) * end * 0.12));
        }
        arr[n - 1] = end;
        return arr;
    }

    function spark(id, data, color, fill) {
        var el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: data.map(function(_,i){ return i; }),
                datasets: [{ data: data, borderColor: color, borderWidth: 2, pointRadius: 0, tension: 0.45, fill: true, backgroundColor: fill }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend:{display:false}, tooltip:{enabled:false} },
                scales:  { x:{display:false}, y:{display:false} },
                animation: { duration: 700 }
            }
        });
    }

    var vUsuarios  = <?= (int)($stats['total_usuarios'] ?? 0) ?>;
    var vHabitac   = <?= (int)($stats['habitaciones_disponibles'] ?? 0) ?>;
    var vResPend   = <?= (int)($stats['reservas_pendientes'] ?? 0) ?>;
    var vResConf   = <?= (int)($stats['reservas_confirmadas'] ?? 0) ?>;
    var vMontoHoy  = <?= (float)($stats['monto_hoy'] ?? 0) ?>;
    var vMontoMes  = <?= (float)($stats['monto_mes'] ?? 0) ?>;
    var vPagosHoy  = <?= (int)($stats['pagos_hoy'] ?? 0) ?>;
    var vPagosPend = <?= (int)($stats['pagos_pendientes'] ?? 0) ?>;

    spark('ck_usuarios',     asc(vUsuarios),  violet, violetf);
    spark('ck_habitaciones', asc(vHabitac),   green,  greenf);
    spark('ck_pendientes',   asc(vResPend),   amber,  amberf);
    spark('ck_confirmadas',  asc(vResConf),   cyan,   cyanf);
    spark('ck_monto_hoy',    asc(vMontoHoy),  green,  greenf);
    spark('ck_monto_mes',    asc(vMontoMes),  blue,   bluef);
    spark('ck_pagos_hoy',    asc(vPagosHoy),  green,  greenf);
    spark('ck_pagos_pend',   asc(vPagosPend), rose,   rosef);

    /* Gráfico área ingresos */
    var elIngresos = document.getElementById('ck_ingresos_mes');
    if (elIngresos) {
        var meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        var nMes  = new Date().getMonth() + 1;
        var labs  = meses.slice(0, nMes);
        var dIngresos = asc(vMontoMes, labs.length);
        var ctx = elIngresos.getContext('2d');
        var grad = ctx.createLinearGradient(0,0,0,240);
        grad.addColorStop(0, 'rgba(59,130,246,.22)');
        grad.addColorStop(1, 'rgba(59,130,246,.00)');
        new Chart(elIngresos, {
            type: 'line',
            data: {
                labels: labs,
                datasets: [{
                    label: 'Bs.',
                    data: dIngresos,
                    borderColor: blue, borderWidth: 2.5,
                    pointBackgroundColor: blue, pointRadius: 4, pointHoverRadius: 6,
                    tension: 0.45, fill: true, backgroundColor: grad
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827', titleColor: '#f9fafb', bodyColor: '#9ca3af', padding: 10,
                        callbacks: { label: function(c){ return ' Bs. ' + c.parsed.y.toFixed(2); } }
                    }
                },
                scales: {
                    x: { grid:{display:false}, ticks:{color:'#9ca3af',font:{size:11}} },
                    y: { grid:{color:'#f3f4f6'}, ticks:{ color:'#9ca3af', font:{size:11}, callback: function(v){ return 'Bs.'+v.toLocaleString(); } } }
                },
                animation: { duration: 900 }
            }
        });
    }

    /* Gráfico barras resumen */
    var elResumen = document.getElementById('ck_resumen');
    if (elResumen) {
        new Chart(elResumen, {
            type: 'bar',
            data: {
                labels: ['Res.Pend.','Confirm.','Pag.Hoy','Pag.Pend.'],
                datasets: [{
                    data: [vResPend, vResConf, vPagosHoy, vPagosPend],
                    backgroundColor: [amberf, cyanf, greenf, rosef],
                    borderColor:     [amber,  cyan,  green,  rose],
                    borderWidth: 2, borderRadius: 8, borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend:{display:false}, tooltip:{ backgroundColor:'#111827', titleColor:'#f9fafb', bodyColor:'#9ca3af', padding:10 } },
                scales: {
                    x: { grid:{display:false}, ticks:{color:'#9ca3af',font:{size:11}} },
                    y: { grid:{color:'#f3f4f6'}, ticks:{color:'#9ca3af',font:{size:11},stepSize:1}, beginAtZero:true }
                },
                animation: { duration: 800 }
            }
        });
    }
})();
</script>
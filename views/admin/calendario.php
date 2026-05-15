<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-calendar-alt me-2 text-primary"></i>Calendario de Disponibilidad
    </h4>
    <a href="<?= url('admin/reservas/crear') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Nueva reserva
    </a>
</div>

<!-- Leyenda -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 d-flex gap-3 flex-wrap align-items-center">
        <span class="small fw-semibold text-muted">Leyenda:</span>
        <span class="badge" style="background:#10b981;">Confirmada</span>
        <span class="badge" style="background:#f59e0b;">Pendiente</span>
        <span class="badge" style="background:#6366f1;">Completada</span>
    </div>
</div>

<!-- FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>

<!-- Vista por habitación -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-semibold mb-0"><i class="fas fa-bed me-2 text-primary"></i>Estado actual de habitaciones</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>N°</th><th>Piso</th><th>Tipo</th><th>Estado</th><th>Próxima reserva</th></tr>
                </thead>
                <tbody>
                <?php foreach ($habitaciones as $h):
                    $col = ['Disponible'=>'success','Ocupada'=>'danger','Reservada'=>'warning','Limpieza'=>'info','Mantenimiento'=>'secondary'];
                    $color = $col[$h['estado_hab']] ?? 'secondary';

                    // Próxima reserva de esta habitación
                    $proxima = null;
                    foreach ($reservas as $r) {
                        if ($r['habitacion_numero'] === $h['numero']) {
                            $proxima = $r; break;
                        }
                    }
                ?>
                <tr>
                    <td class="fw-bold">N° <?= htmlspecialchars($h['numero']) ?></td>
                    <td class="text-muted">Piso <?= $h['piso'] ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($h['tipo']) ?></span></td>
                    <td><span class="badge bg-<?= $color ?>"><?= htmlspecialchars($h['estado_hab']) ?></span></td>
                    <td>
                        <?php if ($proxima): ?>
                            <small>
                                <?= htmlspecialchars($proxima['cliente_nombre'] . ' ' . $proxima['cliente_paterno']) ?>
                                <br>
                                <span class="text-muted">
                                    <?= date('d/m/Y', strtotime($proxima['fechaInicio'])) ?> →
                                    <?= date('d/m/Y', strtotime($proxima['fechaFin'])) ?>
                                </span>
                            </small>
                        <?php else: ?>
                            <span class="text-muted small">Sin reservas próximas</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal detalle reserva -->
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-calendar-check me-2"></i>Detalle de reserva</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalEventoBody"></div>
        </div>
    </div>
</div>

<script>
const reservasData = <?= json_encode(array_map(function($r) {
    $colores = ['Confirmada' => '#10b981', 'Pendiente' => '#f59e0b', 'Completada' => '#6366f1'];
    return [
        'id'    => $r['idReserva'],
        'title' => 'Hab.' . $r['habitacion_numero'] . ' — ' . $r['cliente_nombre'] . ' ' . $r['cliente_paterno'],
        'start' => $r['fechaInicio'],
        'end'   => date('Y-m-d', strtotime($r['fechaFin'] . ' +1 day')),
        'color' => $colores[$r['estado']] ?? '#9ca3af',
        'extendedProps' => $r,
    ];
}, $reservas)) ?>;

document.addEventListener('DOMContentLoaded', function() {
    const cal = new FullCalendar.Calendar(document.getElementById('calendario'), {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listWeek'
        },
        events: reservasData,
        eventClick: function(info) {
            const p = info.event.extendedProps;
            document.getElementById('modalEventoBody').innerHTML = `
                <div class="row g-2">
                    <div class="col-6"><div class="text-muted small">Cliente</div><div class="fw-semibold">${p.cliente_nombre} ${p.cliente_paterno}</div></div>
                    <div class="col-6"><div class="text-muted small">Habitación</div><div class="fw-semibold">N° ${p.habitacion_numero} — Piso ${p.piso}</div></div>
                    <div class="col-6"><div class="text-muted small">Tipo</div><div class="fw-semibold">${p.tipo}</div></div>
                    <div class="col-6"><div class="text-muted small">Estado</div><span class="badge bg-primary">${p.estado}</span></div>
                    <div class="col-6"><div class="text-muted small">Entrada</div><div class="fw-semibold">${p.fechaInicio}</div></div>
                    <div class="col-6"><div class="text-muted small">Salida</div><div class="fw-semibold">${p.fechaFin}</div></div>
                    <div class="col-12"><div class="text-muted small">Total</div><div class="fw-bold text-primary fs-5">Bs. ${parseFloat(p.precioTotal).toFixed(2)}</div></div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('modalEvento')).show();
        },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' }
    });
    cal.render();
});
</script>
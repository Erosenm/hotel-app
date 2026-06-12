<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylecalendario.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<div class="cal-container">
    <!-- Header -->
    <div class="cal-header">
        <h4>
            <i class="fas fa-calendar-alt"></i>
            Calendario de Disponibilidad
        </h4>
        <a href="<?= url('admin/reservas/crear') ?>" class="cal-btn-primary">
            <i class="fas fa-plus"></i> Nueva reserva
        </a>
    </div>

    <!-- Leyenda mejorada -->
    <div class="cal-legend">
        <span class="cal-legend-label">Estado de reservas:</span>
        <div class="cal-legend-item">
            <div class="cal-legend-color" style="background: #10b981;"></div>
            <span>Confirmada</span>
        </div>
        <div class="cal-legend-item">
            <div class="cal-legend-color" style="background: #f59e0b;"></div>
            <span>Pendiente</span>
        </div>
        <div class="cal-legend-item">
            <div class="cal-legend-color" style="background: #8b5cf6;"></div>
            <span>Completada</span>
        </div>
    </div>

    <!-- Calendario -->
    <div class="cal-card">
        <div class="cal-card-body">
            <div id="calendario"></div>
        </div>
    </div>

    <!-- Estado actual de habitaciones -->
    <div class="cal-table-card">
        <div class="cal-table-header">
            <h6>
                <i class="fas fa-bed"></i> Estado actual de habitaciones
            </h6>
        </div>
        <div class="table-responsive">
            <table class="cal-table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Piso</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Próxima reserva</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($habitaciones as $h):
                    $colores = [
                        'Disponible' => 'success',
                        'Ocupada' => 'danger',
                        'Reservada' => 'warning',
                        'Limpieza' => 'info',
                        'Mantenimiento' => 'secondary'
                    ];
                    $color = $colores[$h['estado_hab']] ?? 'secondary';

                    // Próxima reserva de esta habitación
                    $proxima = null;
                    foreach ($reservas as $r) {
                        if ($r['habitacion_numero'] === $h['numero']) {
                            $proxima = $r;
                            break;
                        }
                    }
                ?>
                <tr>
                    <td>
                        <div class="cal-room-number">N° <?= htmlspecialchars($h['numero']) ?></div>
                    </td>
                    <td>
                        <div class="cal-room-floor">Piso <?= $h['piso'] ?></div>
                    </td>
                    <td>
                        <span class="cal-room-type"><?= htmlspecialchars($h['tipo']) ?></span>
                    </td>
                    <td>
                        <span class="cal-status-badge <?= $color ?>"><?= htmlspecialchars($h['estado_hab']) ?></span>
                    </td>
                    <td>
                        <?php if ($proxima): ?>
                            <div class="cal-proxima">
                                <div class="cal-proxima-cliente">
                                    <?= htmlspecialchars($proxima['cliente_nombre'] . ' ' . $proxima['cliente_paterno']) ?>
                                </div>
                                <div class="cal-proxima-fecha">
                                    <?= date('d/m/Y', strtotime($proxima['fechaInicio'])) ?> →
                                    <?= date('d/m/Y', strtotime($proxima['fechaFin'])) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="cal-no-reserva">Sin reservas próximas</span>
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
<div class="modal fade cal-modal" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-calendar-check me-2" style="color: var(--cal-accent);"></i>
                    Detalle de reserva
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalEventoBody"></div>
            <div class="cal-modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
const reservasData = <?= json_encode(array_map(function($r) {
    $colores = ['Confirmada' => '#10b981', 'Pendiente' => '#f59e0b', 'Completada' => '#8b5cf6'];
    return [
        'id'    => $r['idReserva'],
        'title' => $r['habitacion_numero'] . ' - ' . $r['cliente_nombre'] . ' ' . $r['cliente_paterno'],
        'start' => $r['fechaInicio'],
        'end'   => date('Y-m-d', strtotime($r['fechaFin'] . ' +1 day')),
        'color' => $colores[$r['estado']] ?? '#9ca3af',
        'textColor' => '#ffffff',
        'extendedProps' => $r,
    ];
}, $reservas)) ?>;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendario');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: reservasData,
        eventClick: function(info) {
            const p = info.event.extendedProps;
            const estadoBadge = p.estado === 'Confirmada' ? 'success' : (p.estado === 'Pendiente' ? 'warning' : 'info');
            document.getElementById('modalEventoBody').innerHTML = `
                <div class="cal-detail-row">
                    <div>
                        <div class="cal-detail-label">Cliente</div>
                        <div class="cal-detail-value">${p.cliente_nombre} ${p.cliente_paterno}</div>
                    </div>
                    <div>
                        <div class="cal-detail-label">Habitación</div>
                        <div class="cal-detail-value">N° ${p.habitacion_numero} — Piso ${p.piso}</div>
                    </div>
                    <div>
                        <div class="cal-detail-label">Tipo</div>
                        <div class="cal-detail-value">${p.tipo}</div>
                    </div>
                    <div>
                        <div class="cal-detail-label">Estado</div>
                        <div class="cal-detail-value"><span class="cal-status-badge ${estadoBadge}">${p.estado}</span></div>
                    </div>
                    <div>
                        <div class="cal-detail-label">Fecha entrada</div>
                        <div class="cal-detail-value">${p.fechaInicio}</div>
                    </div>
                    <div>
                        <div class="cal-detail-label">Fecha salida</div>
                        <div class="cal-detail-value">${p.fechaFin}</div>
                    </div>
                </div>
                <div class="cal-detail-total">
                    <span>Total de la reserva</span>
                    <span>Bs. ${parseFloat(p.precioTotal).toFixed(2)}</span>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('modalEvento')).show();
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            list: 'Lista'
        },
        firstDay: 1,
        weekNumbers: false,
        dayMaxEvents: true,
        views: {
            listWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: 'numeric' }
            }
        }
    });
    calendar.render();
});
</script>
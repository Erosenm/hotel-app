<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylereportes/index.css') ?>">

<div class="rep-header">
    <div class="rep-title">
        <i class="fas fa-chart-line"></i>
        <h4>Reportes</h4>
    </div>
    <button onclick="window.print()" class="rep-btn-outline">
        <i class="fas fa-print"></i> Imprimir
    </button>
</div>

<!-- FILTRO DE FECHAS MEJORADO -->
<div class="rep-filtro-card">
    <div class="rep-filtro-body">
        <form method="GET" id="filtroForm">
            <div class="rep-fechas-group">
                <div class="rep-fecha-item">
                    <div class="rep-fecha-label">
                        <i class="far fa-calendar-alt"></i> Desde
                    </div>
                    <input type="date" name="desde" class="rep-fecha-input" 
                           value="<?= htmlspecialchars($desde) ?>">
                </div>
                <span class="rep-fecha-separator">→</span>
                <div class="rep-fecha-item">
                    <div class="rep-fecha-label">
                        <i class="far fa-calendar-alt"></i> Hasta
                    </div>
                    <input type="date" name="hasta" class="rep-fecha-input" 
                           value="<?= htmlspecialchars($hasta) ?>">
                </div>
                <button type="submit" class="rep-btn-filter">
                    <i class="fas fa-sliders-h"></i> Aplicar filtro
                </button>
            </div>
            
            <!-- Filtros rápidos de fechas -->
            <div class="rep-filtros-rapidos">
                <span class="rep-rapido-label">Atajos:</span>
                <button type="button" class="rep-rapido-btn" data-rango="hoy">
                    <i class="far fa-sun"></i> Hoy
                </button>
                <button type="button" class="rep-rapido-btn" data-rango="semana">
                    <i class="far fa-calendar-week"></i> Esta semana
                </button>
                <button type="button" class="rep-rapido-btn" data-rango="mes">
                    <i class="far fa-calendar-month"></i> Este mes
                </button>
                <button type="button" class="rep-rapido-btn" data-rango="trimestre">
                    <i class="fas fa-chart-simple"></i> Este trimestre
                </button>
                <button type="button" class="rep-rapido-btn" data-rango="anio">
                    <i class="far fa-calendar"></i> Este año
                </button>
            </div>
        </form>
    </div>
</div>

<!-- BUSCADOR DE PAGOS -->
<div class="rep-buscador-card">
    <div class="rep-buscador-input-group">
        <i class="fas fa-search rep-buscador-icon"></i>
        <input type="text" id="buscadorPagos" class="rep-buscador-input" 
               placeholder="Buscar por cliente, número de recibo o método de pago...">
        <button id="limpiarBuscador" class="rep-buscador-clear">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <small class="text-muted" style="font-size: 0.65rem; margin-top: 0.4rem; display: block;">
        <i class="fas fa-info-circle"></i> El buscador filtra en tiempo real los pagos de la tabla
    </small>
</div>

<!-- EXPORTAR MEJORADO -->
<div class="rep-exportar-card">
    <div class="rep-exportar-label">
        <i class="fas fa-download"></i> Exportar reportes
    </div>
    <div class="rep-exportar-buttons">
        <a href="<?= url('admin/reportes/exportar') ?>?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>&tipo=pagos"
           class="rep-btn-export pagos">
            <i class="fas fa-file-invoice-dollar"></i> Pagos
        </a>
        <a href="<?= url('admin/reportes/exportar') ?>?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>&tipo=reservas"
           class="rep-btn-export reservas">
            <i class="fas fa-calendar-check"></i> Reservas
        </a>
        <a href="<?= url('admin/reportes/exportar') ?>?tipo=inventario"
           class="rep-btn-export stock">
            <i class="fas fa-boxes"></i> Inventario
        </a>
    </div>
</div>

<!-- TOTAL INGRESOS DESTACADO -->
<div class="rep-ingresos-card">
    <div class="rep-ingresos-label">
        <i class="fas fa-coins"></i> Total ingresos del período
    </div>
    <div class="rep-ingresos-monto">Bs. <?= number_format($totalIngresos, 2) ?></div>
</div>

<!-- MÉTODOS DE PAGO -->
<div class="rep-metodos-grid">
    <?php foreach ($ingresos as $ing): ?>
    <div class="rep-metodo-card">
        <div class="rep-metodo-nombre">
            <?php 
                $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                $icono = $iconos[$ing['metodo']] ?? 'fa-receipt';
            ?>
            <i class="fas <?= $icono ?>"></i> <?= htmlspecialchars($ing['metodo']) ?>
        </div>
        <div class="rep-metodo-monto">Bs. <?= number_format($ing['total'], 2) ?></div>
        <div class="rep-metodo-cantidad"><?= $ing['cantidad'] ?> pago(s)</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- RESERVAS POR ESTADO + TOP HABITACIONES -->
<div class="rep-two-columns">
    <!-- Reservas por estado -->
    <div class="rep-table-card">
        <div class="rep-table-header">
            <h6>
                <i class="fas fa-chart-pie"></i> Reservas por estado
            </h6>
            <span class="rep-badge"><?= count($reservasPeriodo) ?> estados</span>
        </div>
        <div class="rep-table-responsive">
            <table class="rep-table">
                <thead>
                    <tr><th>Estado</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                    <?php $colores = ['Pendiente'=>'pendiente','Confirmada'=>'confirmada','Cancelada'=>'cancelada','Completada'=>'completada']; ?>
                    <?php foreach ($reservasPeriodo as $r): ?>
                    <tr>
                        <td><span class="rep-estado-badge <?= $colores[$r['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
                        <td class="text-end fw-bold"><?= $r['total'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top habitaciones -->
    <div class="rep-table-card">
        <div class="rep-table-header">
            <h6>
                <i class="fas fa-trophy"></i> Top habitaciones más reservadas
            </h6>
            <span class="rep-badge">Ranking</span>
        </div>
        <div class="rep-table-responsive">
            <table class="rep-table">
                <thead>
                    <tr><th>Habitación</th><th>Tipo</th><th class="text-end">Reservas</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($habitacionesTop as $h): ?>
                    <tr>
                        <td class="fw-semibold"><span class="rep-hab-badge"><i class="fas fa-bed"></i> N° <?= htmlspecialchars($h['numero']) ?></span></td>
                        <td><?= htmlspecialchars($h['tipo']) ?></td>
                        <td class="text-end fw-bold text-primary"><?= $h['total_reservas'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DETALLE DE PAGOS CON BUSCADOR -->
<div class="rep-table-card">
    <div class="rep-table-header">
        <h6>
            <i class="fas fa-list-ul"></i> Detalle de pagos
        </h6>
        <span class="rep-badge"><?= date('d/m/Y', strtotime($desde)) ?> → <?= date('d/m/Y', strtotime($hasta)) ?></span>
    </div>
    <div class="rep-table-responsive">
        <table class="rep-table" id="tablaPagos">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Fechas reserva</th>
                    <th>Método</th>
                    <th>Recibo</th>
                    <th>Fecha pago</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
            <tbody id="tablaPagosBody">
                <?php if (empty($pagosDetalle)): ?>
                <tr>
                    <td colspan="6">
                        <div class="rep-empty">
                            <i class="fas fa-receipt"></i>
                            <p>No hay pagos en este período</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pagosDetalle as $p): ?>
                <tr class="fila-pago"
                    data-cliente="<?= htmlspecialchars(strtolower($p['cliente_nombre'] . ' ' . $p['cliente_paterno'])) ?>"
                    data-recibo="<?= htmlspecialchars(strtolower($p['recibo'] ?? '')) ?>"
                    data-metodo="<?= htmlspecialchars(strtolower($p['metodo'])) ?>">
                    <td class="fw-semibold"><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?></td>
                    <td><small><?= date('d/m/Y', strtotime($p['fechaInicio'])) ?> → <?= date('d/m/Y', strtotime($p['fechaFin'])) ?></small></td>
                    <td><?= htmlspecialchars($p['metodo']) ?></td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['recibo'] ?? '—') ?></small></td>
                    <td><small><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></small></td>
                    <td class="text-end fw-bold text-primary">Bs. <?= number_format($p['monto'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="fw-bold text-end">TOTAL</td>
                    <td class="text-end fw-bold text-primary fs-6">Bs. <?= number_format($totalIngresos, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
// Filtros rápidos de fechas
document.querySelectorAll('.rep-rapido-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const rango = this.dataset.rango;
        const hoy = new Date();
        let desde = new Date();
        let hasta = new Date();
        
        switch(rango) {
            case 'hoy':
                desde = hoy;
                hasta = hoy;
                break;
            case 'semana':
                desde = new Date(hoy);
                desde.setDate(hoy.getDate() - hoy.getDay());
                hasta = new Date(desde);
                hasta.setDate(desde.getDate() + 6);
                break;
            case 'mes':
                desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                hasta = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
                break;
            case 'trimestre':
                let trimestre = Math.floor(hoy.getMonth() / 3);
                desde = new Date(hoy.getFullYear(), trimestre * 3, 1);
                hasta = new Date(hoy.getFullYear(), trimestre * 3 + 3, 0);
                break;
            case 'anio':
                desde = new Date(hoy.getFullYear(), 0, 1);
                hasta = new Date(hoy.getFullYear(), 11, 31);
                break;
        }
        
        document.querySelector('input[name="desde"]').value = desde.toISOString().split('T')[0];
        document.querySelector('input[name="hasta"]').value = hasta.toISOString().split('T')[0];
        document.getElementById('filtroForm').submit();
    });
});

// Buscador en tiempo real
const buscador = document.getElementById('buscadorPagos');
const limpiarBtn = document.getElementById('limpiarBuscador');
const filas = document.querySelectorAll('.fila-pago');

function filtrarPagos() {
    const termino = buscador.value.toLowerCase();
    let visibles = 0;
    
    filas.forEach(fila => {
        const cliente = fila.dataset.cliente || '';
        const recibo = fila.dataset.recibo || '';
        const metodo = fila.dataset.metodo || '';
        
        if (cliente.includes(termino) || recibo.includes(termino) || metodo.includes(termino)) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const tbody = document.getElementById('tablaPagosBody');
    const emptyMsg = tbody.querySelector('.rep-empty-message');
    
    if (visibles === 0 && filas.length > 0) {
        if (!emptyMsg) {
            const row = document.createElement('tr');
            row.className = 'rep-empty-message';
            row.innerHTML = `<td colspan="6"><div class="rep-empty"><i class="fas fa-search"></i><p>No se encontraron pagos con "${termino}"</p></div></td>`;
            tbody.appendChild(row);
        }
    } else if (emptyMsg) {
        emptyMsg.remove();
    }
}

buscador.addEventListener('input', function() {
    if (this.value.length > 0) {
        limpiarBtn.style.display = 'block';
    } else {
        limpiarBtn.style.display = 'none';
    }
    filtrarPagos();
});

limpiarBtn.addEventListener('click', function() {
    buscador.value = '';
    this.style.display = 'none';
    filtrarPagos();
});

// Ctrl+F para enfocar buscador
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        buscador.focus();
    }
});
</script>

<style>
@media print {
    .rep-filtro-card, .rep-buscador-card, .rep-exportar-card, 
    .rep-filtros-rapidos, .rep-btn-outline, .rep-btn-export,
    .rep-rapido-btn, .rep-btn-filter, .rep-buscador-clear,
    .btn, button, form .rep-btn, .rep-fechas-group button {
        display: none !important;
    }
    
    .rep-table-card, .rep-ingresos-card, .rep-metodo-card {
        break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    
    body {
        background: white;
        padding: 1rem;
    }
}
</style>
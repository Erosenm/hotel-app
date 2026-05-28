<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo <?= htmlspecialchars($recibo['numero']) ?> — Hotel Real Plaza</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            font-size: 14px;
        }
        .toolbar {
            background: #1a1a2e;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .toolbar a { color: #a0b4d0; text-decoration: none; font-size: .9rem; }
        .toolbar a:hover { color: #fff; }
        .toolbar-actions { display: flex; gap: 10px; }
        .btn-print {
            background: #c8a96e; color: #1a1a2e; border: none;
            padding: 8px 20px; border-radius: 6px; font-weight: 700;
            cursor: pointer; font-size: .9rem;
        }
        .btn-print:hover { background: #b8915a; }
        .btn-back {
            background: transparent; color: #a0b4d0;
            border: 1px solid rgba(255,255,255,.2);
            padding: 8px 20px; border-radius: 6px;
            text-decoration: none; font-size: .9rem;
        }
        .btn-back:hover { color: #fff; border-color: rgba(255,255,255,.5); }

        .page {
            max-width: 760px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* HEADER */
        .receipt-header {
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            color: #fff;
            padding: 36px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .hotel-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #c8a96e;
            letter-spacing: 1px;
        }
        .hotel-sub { color: #a0b4d0; font-size: .85rem; margin-top: 4px; }
        .receipt-number { text-align: right; }
        .receipt-number .label { color: #a0b4d0; font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; }
        .receipt-number .value { font-size: 1.4rem; font-weight: 700; font-family: monospace; color: #c8a96e; }
        .receipt-date { color: #a0b4d0; font-size: .85rem; margin-top: 4px; }

        /* BODY */
        .receipt-body { padding: 36px 40px; }

        /* Cliente y reserva */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .info-box { background: #f9f9f9; border-radius: 10px; padding: 18px; }
        .info-box h6 {
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; color: #9ca3af; margin-bottom: 10px;
        }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .info-row .lbl { color: #6b7280; font-size: .85rem; }
        .info-row .val { font-weight: 600; font-size: .85rem; }

        /* Tabla de detalle */
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table thead tr { background: #f3f4f6; }
        .detail-table th {
            padding: 10px 14px; text-align: left;
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .4px; color: #6b7280;
        }
        .detail-table th:last-child { text-align: right; }
        .detail-table tbody td {
            padding: 12px 14px; border-bottom: 1px solid #f3f4f6;
            font-size: .88rem;
        }
        .detail-table tbody td:last-child { text-align: right; font-weight: 600; }
        .detail-table tfoot td {
            padding: 10px 14px; font-size: .88rem;
        }
        .detail-table tfoot tr.subtotal td { color: #6b7280; }
        .detail-table tfoot tr.total-row td {
            font-size: 1.1rem; font-weight: 800;
            border-top: 2px solid #1a1a2e;
            padding-top: 14px;
        }
        .detail-table tfoot tr.total-row td:last-child { color: #0f3460; text-align: right; }

        /* Badge estado */
        .badge-estado {
            display: inline-block; padding: 4px 12px;
            border-radius: 20px; font-size: .75rem; font-weight: 700;
        }
        .badge-pagado { background: #ecfdf5; color: #059669; }
        .badge-pendiente { background: #fffbeb; color: #d97706; }

        /* Footer */
        .receipt-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-note { font-size: .78rem; color: #9ca3af; }
        .footer-logo { font-size: .85rem; font-weight: 700; color: #c8a96e; }

        @media print {
            .toolbar { display: none !important; }
            body { background: #fff; }
            .page { box-shadow: none; margin: 0; border-radius: 0; }
        }
    </style>
</head>
<body>

<!-- Barra de acciones -->
<div class="toolbar">
    <?php
    $rol = $_SESSION['usuario']['rol'] ?? '';
    if (in_array($rol, ['Administrador','Recepcionista','Gerente','Contador'])) {
        $back = url('admin/pagos');
    } else {
        $back = url('cliente/reservas');
    }
    ?>
    <a href="<?= $back ?>" class="btn-back">← Volver</a>
    <div class="toolbar-actions">
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimir / Guardar PDF
        </button>
    </div>
</div>

<div class="page">

    <!-- Header -->
    <div class="receipt-header">
        <div>
            <div class="hotel-name">🏨 HOTEL REAL PLAZA</div>
            <div class="hotel-sub">Recibo oficial de pago</div>
            <div class="hotel-sub" style="margin-top:8px;">
                Estado:
                <span class="badge-estado <?= $recibo['estado_pago'] === 'Pagado' ? 'badge-pagado' : 'badge-pendiente' ?>">
                    <?= htmlspecialchars($recibo['estado_pago']) ?>
                </span>
            </div>
        </div>
        <div class="receipt-number">
            <div class="label">N° de Recibo</div>
            <div class="value"><?= htmlspecialchars($recibo['numero']) ?></div>
            <div class="receipt-date"><?= date('d/m/Y H:i', strtotime($recibo['fecha'])) ?></div>
        </div>
    </div>

    <!-- Body -->
    <div class="receipt-body">

        <!-- Info cliente y reserva -->
        <div class="info-grid">
            <div class="info-box">
                <h6>Cliente</h6>
                <div class="info-row">
                    <span class="lbl">Nombre</span>
                    <span class="val"><?= htmlspecialchars($recibo['cliente_nombre'] . ' ' . $recibo['cliente_paterno']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">CI</span>
                    <span class="val"><?= htmlspecialchars($recibo['cliente_ci']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">Email</span>
                    <span class="val" style="font-size:.8rem;"><?= htmlspecialchars($recibo['cliente_email']) ?></span>
                </div>
                <?php if ($recibo['cliente_telefono']): ?>
                <div class="info-row">
                    <span class="lbl">Teléfono</span>
                    <span class="val"><?= htmlspecialchars($recibo['cliente_telefono']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="info-box">
                <h6>Reserva</h6>
                <div class="info-row">
                    <span class="lbl">Habitación</span>
                    <span class="val">N° <?= htmlspecialchars($recibo['hab_numero']) ?> — Piso <?= $recibo['piso'] ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">Tipo</span>
                    <span class="val"><?= htmlspecialchars($recibo['tipo_habitacion']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">Check-in</span>
                    <span class="val"><?= date('d/m/Y', strtotime($recibo['fechaInicio'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">Check-out</span>
                    <span class="val"><?= date('d/m/Y', strtotime($recibo['fechaFin'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl">Método de pago</span>
                    <span class="val"><?= htmlspecialchars($recibo['metodo_pago']) ?></span>
                </div>
            </div>
        </div>

        <!-- Detalle de cobro -->
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <!-- Habitación -->
                <tr>
                    <td>
                        <strong>Habitación <?= htmlspecialchars($recibo['tipo_habitacion']) ?></strong>
                        <br><small style="color:#9ca3af;">N° <?= $recibo['hab_numero'] ?> — Piso <?= $recibo['piso'] ?></small>
                    </td>
                    <td><?= $noches ?> noche(s)</td>
                    <td>Bs. <?= number_format($recibo['precioBase'], 2) ?></td>
                    <td>Bs. <?= number_format($subtotalHab, 2) ?></td>
                </tr>
                <!-- Servicios adicionales -->
                <?php foreach ($servicios as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nombre']) ?></td>
                    <td><?= $s['cantidad'] ?></td>
                    <td>Bs. <?= number_format($s['precioUnitario'], 2) ?></td>
                    <td>Bs. <?= number_format($s['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php if ($totalServicios > 0): ?>
                <tr class="subtotal">
                    <td colspan="3" style="text-align:right;">Subtotal habitación</td>
                    <td style="text-align:right;">Bs. <?= number_format($subtotalHab, 2) ?></td>
                </tr>
                <tr class="subtotal">
                    <td colspan="3" style="text-align:right;">Subtotal servicios</td>
                    <td style="text-align:right;">Bs. <?= number_format($totalServicios, 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;font-weight:800;">TOTAL PAGADO</td>
                    <td>Bs. <?= number_format($recibo['total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

    </div>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="footer-note">
            Este recibo es un comprobante oficial de pago.<br>
            Consérvalo para cualquier consulta o reclamo.
        </div>
        <div class="footer-logo">Hotel Real Plaza © <?= date('Y') ?></div>
    </div>

</div>
</body>
</html>
<style>
.ia-stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.ia-stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
}
.ia-stat-icon.blue { background: #4f46e5; }
.ia-stat-icon.green { background: #10b981; }
.ia-stat-icon.orange { background: #f59e0b; }
.ia-stat-number { font-size: 2rem; font-weight: 700; color: #1f2937; }
.ia-stat-label { font-size: 0.8rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
.ia-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}
.ia-card h5 { font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem; }
.ia-table { width: 100%; border-collapse: collapse; }
.ia-table th { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; padding: 0.5rem; text-align: left; border-bottom: 1px solid #f3f4f6; }
.ia-table td { padding: 0.75rem 0.5rem; font-size: 0.875rem; color: #374151; border-bottom: 1px solid #f9fafb; vertical-align: top; }
.ia-table tr:last-child td { border-bottom: none; }
.msg-usuario { background: #f3f4f6; padding: 0.4rem 0.7rem; border-radius: 8px; margin-bottom: 0.3rem; font-size: 0.8rem; }
.msg-bot { background: #eff6ff; padding: 0.4rem 0.7rem; border-radius: 8px; font-size: 0.8rem; color: #1d4ed8; }
.badge-rank { background: #4f46e5; color: #fff; border-radius: 99px; padding: 2px 10px; font-size: 0.75rem; font-weight: 600; }
</style>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-robot me-2" style="font-size:1.5rem; color:#4f46e5;"></i>
        <h4 class="mb-0" style="font-weight:700;">Dashboard IA Chat</h4>
    </div>

    <div class="row mb-2">
        <div class="col-md-4">
            <div class="ia-stat-card">
                <div class="ia-stat-icon blue"><i class="fas fa-comments"></i></div>
                <div>
                    <div class="ia-stat-number"><?= $totalMensajes ?></div>
                    <div class="ia-stat-label">Total Mensajes</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ia-stat-card">
                <div class="ia-stat-icon green"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <div class="ia-stat-number"><?= $mensajesHoy ?></div>
                    <div class="ia-stat-label">Mensajes Hoy</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ia-stat-card">
                <div class="ia-stat-icon orange"><i class="fas fa-users"></i></div>
                <div>
                    <div class="ia-stat-number"><?= count($usuariosActivos) ?></div>
                    <div class="ia-stat-label">Usuarios Activos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="ia-card">
                <h5><i class="fas fa-chart-bar me-2"></i>Mensajes por día (últimos 7 días)</h5>
                <?php if (empty($mensajesPorDia)): ?>
                    <p class="text-muted text-center py-3">Sin datos aún</p>
                <?php else: ?>
                    <table class="ia-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Mensajes</th>
                                <th>Gráfico</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $maxTotal = max(array_column($mensajesPorDia, 'total'));
                            foreach ($mensajesPorDia as $row): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['dia'])) ?></td>
                                <td><strong><?= $row['total'] ?></strong></td>
                                <td>
                                    <div style="background:#e0e7ff; border-radius:4px; height:8px; width:100%;">
                                        <div style="background:#4f46e5; border-radius:4px; height:8px; width:<?= ($row['total'] / $maxTotal) * 100 ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="ia-card">
                <h5><i class="fas fa-trophy me-2"></i>Usuarios más activos</h5>
                <?php if (empty($usuariosActivos)): ?>
                    <p class="text-muted text-center py-3">Sin datos aún</p>
                <?php else: ?>
                    <table class="ia-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Mensajes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuariosActivos as $i => $u): ?>
                            <tr>
                                <td><span class="badge-rank"><?= $i + 1 ?></span></td>
                                <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?></td>
                                <td><strong><?= $u['total'] ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ia-card">
        <h5><i class="fas fa-history me-2"></i>Últimas conversaciones</h5>
        <?php if (empty($ultimasConversaciones)): ?>
            <p class="text-muted text-center py-3">Sin conversaciones aún</p>
        <?php else: ?>
            <table class="ia-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Mensaje</th>
                        <th>Respuesta IA</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimasConversaciones as $conv): ?>
                    <tr>
                        <td><?= $conv['nombre'] ? htmlspecialchars($conv['nombre'] . ' ' . $conv['paterno']) : 'Visitante' ?></td>
                        <td><div class="msg-usuario"><?= htmlspecialchars(mb_substr($conv['mensajeUsuario'], 0, 80)) ?>...</div></td>
                        <td><div class="msg-bot"><?= htmlspecialchars(mb_substr($conv['respuestaIA'], 0, 100)) ?>...</div></td>
                        <td style="white-space:nowrap;"><?= date('d/m H:i', strtotime($conv['fecha'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
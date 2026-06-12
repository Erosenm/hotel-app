<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleiachat/index.css') ?>">

<div class="ia-container">
    <!-- Header -->
    <div class="ia-header">
        <div class="ia-header-icon">
            <i class="fas fa-robot"></i>
        </div>
        <h4>
            Dashboard IA Chat
            <span>Asistente virtual</span>
        </h4>
    </div>

    <!-- Stats Cards -->
    <div class="ia-stats-row">
        <div class="ia-stat-card">
            <div class="ia-stat-icon purple">
                <i class="fas fa-comments"></i>
            </div>
            <div class="ia-stat-info">
                <div class="ia-stat-number"><?= number_format($totalMensajes) ?></div>
                <div class="ia-stat-label">Total mensajes</div>
            </div>
        </div>
        
        <div class="ia-stat-card">
            <div class="ia-stat-icon green">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="ia-stat-info">
                <div class="ia-stat-number"><?= number_format($mensajesHoy) ?></div>
                <div class="ia-stat-label">Mensajes hoy</div>
            </div>
        </div>
        
        <div class="ia-stat-card">
            <div class="ia-stat-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <div class="ia-stat-info">
                <div class="ia-stat-number"><?= count($usuariosActivos) ?></div>
                <div class="ia-stat-label">Usuarios activos</div>
            </div>
        </div>
    </div>

    <!-- Mensajes por día + Usuarios activos -->
    <div class="ia-two-columns">
        <!-- Mensajes por día -->
        <div class="ia-card">
            <div class="ia-card-header">
                <h5>
                    <i class="fas fa-chart-bar"></i> Mensajes por día
                </h5>
                <span class="ia-card-badge">Últimos 7 días</span>
            </div>
            <div class="ia-card-body">
                <?php if (empty($mensajesPorDia)): ?>
                    <div class="ia-empty">
                        <i class="fas fa-chart-line"></i>
                        <p>Sin datos aún</p>
                    </div>
                <?php else: ?>
                    <table class="ia-table">
                        <thead>
                            <tr><th>Fecha</th><th>Mensajes</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            $maxTotal = max(array_column($mensajesPorDia, 'total'));
                            foreach ($mensajesPorDia as $row): ?>
                            <tr>
                                <td style="width: 100px;"><?= date('d/m/Y', strtotime($row['dia'])) ?></td>
                                <td style="width: 70px;"><strong><?= $row['total'] ?></strong></td>
                                <td>
                                    <div class="ia-progress">
                                        <div class="ia-progress-bar" style="width: <?= ($row['total'] / max($maxTotal,1)) * 100 ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Usuarios más activos -->
        <div class="ia-card">
            <div class="ia-card-header">
                <h5>
                    <i class="fas fa-trophy"></i> Usuarios más activos
                </h5>
                <span class="ia-card-badge">Ranking</span>
            </div>
            <div class="ia-card-body">
                <?php if (empty($usuariosActivos)): ?>
                    <div class="ia-empty">
                        <i class="fas fa-users-slash"></i>
                        <p>Sin datos aún</p>
                    </div>
                <?php else: ?>
                    <table class="ia-table">
                        <thead>
                            <tr><th>#</th><th>Usuario</th><th>Mensajes</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuariosActivos as $i => $u): ?>
                            <tr>
                                <td style="width: 50px;">
                                    <span class="ia-rank-badge <?= $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')) ?>">
                                        <?= $i + 1 ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?></td>
                                <td class="text-end fw-bold"><?= $u['total'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimas conversaciones -->
    <div class="ia-card">
        <div class="ia-card-header">
            <h5>
                <i class="fas fa-history"></i> Últimas conversaciones
            </h5>
            <span class="ia-card-badge">En tiempo real</span>
        </div>
        <div class="ia-card-body">
            <?php if (empty($ultimasConversaciones)): ?>
                <div class="ia-empty">
                    <i class="fas fa-comment-dots"></i>
                    <p>Sin conversaciones aún</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
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
                                <td style="white-space: nowrap;">
                                    <i class="fas fa-user-circle me-1" style="color: var(--ia-accent);"></i>
                                    <?= isset($conv['nombre']) ? htmlspecialchars($conv['nombre'] . ' ' . $conv['paterno']) : 'Visitante' ?>
                                </td>
                                <td>
                                    <div class="ia-msg-user">
                                        <i class="fas fa-user me-1"></i>
                                        <?= htmlspecialchars(mb_substr($conv['mensajeUsuario'], 0, 60)) ?>...
                                    </div>
                                </td>
                                <td>
                                    <div class="ia-msg-bot">
                                        <i class="fas fa-robot me-1"></i>
                                        <?= htmlspecialchars(mb_substr($conv['respuestaIA'], 0, 80)) ?>...
                                    </div>
                                </td>
                                <td style="white-space: nowrap; font-size: 0.7rem;">
                                    <i class="far fa-clock me-1"></i>
                                    <?= date('d/m H:i', strtotime($conv['fecha'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="ia-footer-note">
        <i class="fas fa-microchip"></i> IA entrenada para responder preguntas sobre el hotel, reservas y servicios
    </div>
</div>
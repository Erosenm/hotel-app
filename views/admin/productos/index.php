<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleproductos/index.css') ?>">

<div class="prod-header">
    <div class="prod-title">
        <i class="fas fa-box"></i>
        <span>Inventario de Productos</span>
    </div>
    <a href="<?= url('admin/productos/crear') ?>" class="prod-btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="prod-alert prod-alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="prod-alert prod-alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Alerta stock bajo -->
<?php if ($stats['bajo_stock'] > 0): ?>
<div class="prod-alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
        <strong><?= $stats['bajo_stock'] ?> producto(s) con stock bajo o agotado.</strong>
        Revisa el inventario y realiza un ajuste.
    </div>
</div>
<?php endif; ?>

<!-- STATS PREMIUM -->
<div class="prod-stats-row">
    <?php
    $statsConfig = [
        'total' => ['label' => 'Total Productos', 'icon' => 'fa-boxes', 'emoji' => '', 'color' => 'total', 'value' => $stats['total']],
        'activos' => ['label' => 'Activos', 'icon' => 'fa-check-circle', 'emoji' => '', 'color' => 'activos', 'value' => $stats['activos']],
        'bajo_stock' => ['label' => 'Stock Bajo', 'icon' => 'fa-exclamation-triangle', 'emoji' => '', 'color' => 'bajo_stock', 'value' => $stats['bajo_stock']],
        'sin_stock' => ['label' => 'Sin Stock', 'icon' => 'fa-times-circle', 'emoji' => '', 'color' => 'sin_stock', 'value' => $stats['sin_stock']]
    ];
    
    foreach ($statsConfig as $key => $cfg):
    ?>
    <div class="prod-stat-card <?= $cfg['color'] ?>">
        <div class="prod-stat-top">
            <div class="prod-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
            <span class="prod-stat-emoji"><?= $cfg['emoji'] ?></span>
        </div>
        <div class="prod-stat-value"><?= $cfg['value'] ?></div>
        <div class="prod-stat-label"><?= $cfg['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- BUSCADOR EN TIEMPO REAL -->
<div class="prod-buscador-card">
    <div class="prod-buscador-input-group">
        <i class="fas fa-search prod-buscador-icon"></i>
        <input type="text" id="buscadorProducto" class="prod-buscador-input"
            placeholder="Buscar por nombre, descripción o categoría...">
        <button id="limpiarBuscador" class="prod-buscador-clear">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <small class="text-muted mt-2 d-block">
        <i class="fas fa-info-circle"></i> Busca por nombre, descripción o categoría
    </small>
</div>

<!-- ORDENAMIENTO Y FILTROS -->
<div class="prod-ordenamiento-card">
    <div class="prod-ordenamiento-group">
        <span class="prod-ordenamiento-label">
            <i class="fas fa-sort-amount-down-alt me-1"></i>Ordenar por:
        </span>
        <select id="ordenarProductos" class="prod-ordenamiento-select">
            <option value="nombre_asc"> Nombre (A-Z)</option>
            <option value="nombre_desc"> Nombre (Z-A)</option>
            <option value="precio_asc"> Precio (menor a mayor)</option>
            <option value="precio_desc"> Precio (mayor a menor)</option>
            <option value="stock_asc"> Stock (menor a mayor)</option>
            <option value="stock_desc"> Stock (mayor a menor)</option>
        </select>
    </div>

    <div class="prod-stock-filtros">
        <span class="prod-ordenamiento-label">
            <i class="fas fa-filter me-1"></i>Stock:
        </span>
        <button class="prod-stock-filtro todos active" data-stock-filtro="todos">
            Todos
        </button>
        <button class="prod-stock-filtro normal" data-stock-filtro="normal">
            ✅ Stock normal
        </button>
        <button class="prod-stock-filtro bajo" data-stock-filtro="bajo">
            ⚠️ Stock bajo
        </button>
        <button class="prod-stock-filtro agotado" data-stock-filtro="agotado">
            ❌ Agotados
        </button>
    </div>
</div>

<!-- Filtro por categoría -->
<div class="prod-filtro-card">
    <div class="prod-filtro-buttons">
        <button class="prod-filtro-btn active" data-cat="todos">
            <i class="fas fa-th-large me-1"></i>Todos
        </button>
        <?php foreach ($categorias as $cat): ?>
        <button class="prod-filtro-btn" data-cat="<?= $cat['idCategoria'] ?>">
            <i class="fas fa-tag me-1"></i><?= htmlspecialchars($cat['nombre']) ?>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- TABLA PREMIUM -->
<div class="prod-table-card">
    <div class="table-responsive">
        <table class="prod-table" id="tablaProductos">
            <thead>
                <tr>
                    <th data-order="nombre">Producto <i class="fas fa-sort"></i></th>
                    <th data-order="categoria">Categoría <i class="fas fa-sort"></i></th>
                    <th data-order="precio">Precio <i class="fas fa-sort"></i></th>
                    <th data-order="stock">Stock <i class="fas fa-sort"></i></th>
                    <th data-order="unidad">Unidad <i class="fas fa-sort"></i></th>
                    <th data-order="estado">Estado <i class="fas fa-sort"></i></th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaProductosBody">
                <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="7">
                        <div class="prod-empty">
                            <i class="fas fa-box-open"></i>
                            <p>No hay productos registrados</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($productos as $p):
                    $stockClase = '';
                    $stockIcono = '';
                    $stockTipo = '';
                    if ($p['stock'] == 0) {
                        $stockClase = 'danger';
                        $stockIcono = '<i class="fas fa-times-circle"></i>';
                        $stockTipo = 'agotado';
                    } elseif ($p['stock'] <= $p['stockMinimo']) {
                        $stockClase = 'warning';
                        $stockIcono = '<i class="fas fa-exclamation-triangle"></i>';
                        $stockTipo = 'bajo';
                    } else {
                        $stockClase = 'success';
                        $stockTipo = 'normal';
                    }
                ?>
                <tr data-cat="<?= $p['idCategoria_FK'] ?>" data-stock-tipo="<?= $stockTipo ?>"
                    data-nombre="<?= htmlspecialchars(strtolower($p['nombre'])) ?>"
                    data-descripcion="<?= htmlspecialchars(strtolower($p['descripcion'] ?? '')) ?>"
                    data-categoria-nombre="<?= htmlspecialchars(strtolower($p['categoria'] ?? '')) ?>"
                    data-precio="<?= $p['precio'] ?>" data-stock-cantidad="<?= $p['stock'] ?>"
                    data-unidad="<?= htmlspecialchars(strtolower($p['unidad'])) ?>" data-estado="<?= $p['estado'] ?>">
                    <td>
                        <div class="prod-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                        <?php if ($p['descripcion']): ?>
                        <small class="prod-descripcion">
                            <?= htmlspecialchars(substr($p['descripcion'], 0, 50)) ?>...
                        </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="prod-categoria-badge">
                            <i class="fas fa-tag me-1"></i>
                            <?= htmlspecialchars($p['categoria'] ?? '—') ?>
                        </span>
                    </td>
                    <td class="prod-precio">Bs. <?= number_format($p['precio'], 2) ?></td>
                    <td>
                        <div class="prod-stock">
                            <span class="prod-stock-value <?= $stockClase ?>">
                                <?= $stockIcono ?> <?= $p['stock'] ?>
                            </span>
                            <span class="prod-stock-minimo">
                                Mín: <?= $p['stockMinimo'] ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="prod-unidad">
                            <i class="fas fa-cube me-1"></i>
                            <?= htmlspecialchars($p['unidad']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="prod-estado-badge <?= $p['estado'] === 'Activo' ? 'success' : 'secondary' ?>">
                            <?= $p['estado'] ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="prod-actions">
                            <button class="prod-action-btn info"
                                onclick="abrirAjusteStock(<?= $p['idProducto'] ?>, '<?= htmlspecialchars($p['nombre']) ?>', <?= $p['stock'] ?>)"
                                title="Ajustar stock">
                                <i class="fas fa-boxes"></i>
                            </button>
                            <a href="<?= url('admin/productos/editar?id=' . $p['idProducto']) ?>"
                                class="prod-action-btn primary" title="Editar producto">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($p['estado'] === 'Activo'): ?>
                            <a href="<?= url('admin/productos/eliminar?id=' . $p['idProducto']) ?>"
                                class="prod-action-btn danger" onclick="return confirm('¿Desactivar este producto?')"
                                title="Desactivar">
                                <i class="fas fa-ban"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINACIÓN -->
<div class="prod-paginacion-card">
    <div class="prod-paginacion-info" id="paginacionInfo">
        Mostrando 0 - 0 de 0 productos
    </div>
    <div class="prod-registros-select">
        <span class="text-muted small">Mostrar:</span>
        <select id="registrosPorPagina">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="text-muted small">registros</span>
    </div>
    <div class="prod-paginacion-buttons" id="paginacionButtons">
        <!-- Se genera con JS -->
    </div>
</div>

<!-- MODAL AJUSTE DE STOCK -->
<div class="modal fade prod-modal" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-boxes me-2" style="color: var(--prod-accent);"></i>
                    Ajustar Stock
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/productos/stock') ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="stockId">

                    <div class="mb-3">
                        <div class="p-3 bg-light rounded" style="background: #f8fafc !important;">
                            <div class="mb-2">
                                <span class="text-muted small">Producto</span>
                                <div class="fw-bold fs-6" id="stockNombre">—</div>
                            </div>
                            <div>
                                <span class="text-muted small">Stock actual</span>
                                <div class="fw-bold fs-5" id="stockActual" style="color: var(--prod-accent);">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de movimiento</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="entrada"
                                    id="tipoEntrada" checked>
                                <label class="form-check-label text-success fw-semibold" for="tipoEntrada">
                                    <i class="fas fa-arrow-up me-1"></i>Entrada (+)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="salida" id="tipoSalida">
                                <label class="form-check-label text-danger fw-semibold" for="tipoSalida">
                                    <i class="fas fa-arrow-down me-1"></i>Salida (-)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="prod-btn-primary" style="padding: 0.5rem 1.2rem;">
                        <i class="fas fa-save me-1"></i>Guardar ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales
let productosData = [];
let filtroCategoriaActual = 'todos';
let filtroStockActual = 'todos';
let textoBusquedaActual = '';
let ordenActual = 'nombre_asc';
let paginaActual = 1;
let registrosPorPagina = 25;

// Función para capturar los datos de la tabla
function capturarDatosTabla() {
    const rows = document.querySelectorAll('#tablaProductosBody tr');
    productosData = [];
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return;
        productosData.push({
            elemento: row,
            nombre: row.dataset.nombre || '',
            descripcion: row.dataset.descripcion || '',
            categoria: row.dataset.categoriaNombre || '',
            precio: parseFloat(row.dataset.precio) || 0,
            stock: parseInt(row.dataset.stockCantidad) || 0,
            unidad: row.dataset.unidad || '',
            estado: row.dataset.estado || '',
            catId: row.dataset.cat || '',
            stockTipo: row.dataset.stockTipo || ''
        });
    });
    
    console.log('Datos capturados:', productosData.length, 'productos');
}

// Función para filtrar productos
function filtrarProductos() {
    const filtrados = productosData.filter(item => {
        // Filtro por categoría
        if (filtroCategoriaActual !== 'todos' && item.catId !== filtroCategoriaActual) return false;

        // Filtro por stock
        if (filtroStockActual !== 'todos' && item.stockTipo !== filtroStockActual) return false;

        // Filtro por búsqueda
        if (textoBusquedaActual) {
            const busqueda = textoBusquedaActual.toLowerCase();
            return item.nombre.includes(busqueda) ||
                item.descripcion.includes(busqueda) ||
                item.categoria.includes(busqueda);
        }

        return true;
    });
    
    console.log('Filtrados:', filtrados.length, 'productos (Total:', productosData.length, ')');
    return filtrados;
}

// Función para ordenar productos
function ordenarProductos(productos) {
    console.log('Ordenando', productos.length, 'productos por:', ordenActual);
    
    const productosOrdenados = [...productos].sort((a, b) => {
        switch (ordenActual) {
            case 'nombre_asc':
                return a.nombre.localeCompare(b.nombre);
            case 'nombre_desc':
                return b.nombre.localeCompare(a.nombre);
            case 'precio_asc':
                return a.precio - b.precio;
            case 'precio_desc':
                return b.precio - a.precio;
            case 'stock_asc':
                return a.stock - b.stock;
            case 'stock_desc':
                return b.stock - a.stock;
            default:
                return 0;
        }
    });
    
    return productosOrdenados;
}

// Función para renderizar la tabla con paginación
function renderizarTabla() {
    const productosFiltrados = filtrarProductos();
    const productosOrdenados = ordenarProductos(productosFiltrados);

    // Paginación
    const totalRegistros = productosOrdenados.length;
    const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
    const inicio = (paginaActual - 1) * registrosPorPagina;
    const fin = inicio + registrosPorPagina;
    const productosPagina = productosOrdenados.slice(inicio, fin);

    // Ocultar todos los rows
    productosData.forEach(item => {
        item.elemento.style.display = 'none';
    });

    // Mostrar solo los de la página actual
    productosPagina.forEach(item => {
        item.elemento.style.display = '';
    });

    // Actualizar info de paginación
    const desde = totalRegistros === 0 ? 0 : inicio + 1;
    const hasta = Math.min(fin, totalRegistros);
    const paginacionInfo = document.getElementById('paginacionInfo');
    if (paginacionInfo) {
        paginacionInfo.innerHTML = `
            <i class="fas fa-chart-line me-1"></i>
            Mostrando ${desde} - ${hasta} de ${totalRegistros} productos
        `;
    }

    // Generar botones de paginación
    generarBotonesPaginacion(totalPaginas);
}

// Función para generar botones de paginación
function generarBotonesPaginacion(totalPaginas) {
    const container = document.getElementById('paginacionButtons');
    if (!container) return;
    
    container.innerHTML = '';

    // Botón Anterior
    const btnPrev = document.createElement('button');
    btnPrev.className = `prod-paginacion-btn ${paginaActual === 1 ? 'disabled' : ''}`;
    btnPrev.innerHTML = '<i class="fas fa-chevron-left"></i>';
    btnPrev.onclick = () => {
        if (paginaActual > 1) {
            paginaActual--;
            renderizarTabla();
        }
    };
    container.appendChild(btnPrev);

    // Números de página
    const startPage = Math.max(1, paginaActual - 2);
    const endPage = Math.min(totalPaginas, paginaActual + 2);

    if (startPage > 1) {
        const btn1 = document.createElement('button');
        btn1.className = 'prod-paginacion-btn';
        btn1.textContent = '1';
        btn1.onclick = () => {
            paginaActual = 1;
            renderizarTabla();
        };
        container.appendChild(btn1);
        if (startPage > 2) {
            const dots = document.createElement('span');
            dots.innerHTML = '...';
            dots.style.padding = '0 0.25rem';
            container.appendChild(dots);
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.className = `prod-paginacion-btn ${paginaActual === i ? 'active' : ''}`;
        btn.textContent = i;
        btn.onclick = () => {
            paginaActual = i;
            renderizarTabla();
        };
        container.appendChild(btn);
    }

    if (endPage < totalPaginas) {
        if (endPage < totalPaginas - 1) {
            const dots = document.createElement('span');
            dots.innerHTML = '...';
            dots.style.padding = '0 0.25rem';
            container.appendChild(dots);
        }
        const btnLast = document.createElement('button');
        btnLast.className = 'prod-paginacion-btn';
        btnLast.textContent = totalPaginas;
        btnLast.onclick = () => {
            paginaActual = totalPaginas;
            renderizarTabla();
        };
        container.appendChild(btnLast);
    }

    // Botón Siguiente
    const btnNext = document.createElement('button');
    btnNext.className = `prod-paginacion-btn ${paginaActual === totalPaginas ? 'disabled' : ''}`;
    btnNext.innerHTML = '<i class="fas fa-chevron-right"></i>';
    btnNext.onclick = () => {
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderizarTabla();
        }
    };
    container.appendChild(btnNext);
}

// Función para ordenar por columna
function ordenarPorColumna(columna) {
    const ordenamientos = {
        'nombre': ordenActual === 'nombre_asc' ? 'nombre_desc' : 'nombre_asc',
        'categoria': ordenActual === 'nombre_asc' ? 'nombre_desc' : 'nombre_asc',
        'precio': ordenActual === 'precio_asc' ? 'precio_desc' : 'precio_asc',
        'stock': ordenActual === 'stock_asc' ? 'stock_desc' : 'stock_asc',
        'unidad': ordenActual === 'nombre_asc' ? 'nombre_desc' : 'nombre_asc',
        'estado': ordenActual === 'nombre_asc' ? 'nombre_desc' : 'nombre_asc'
    };
    ordenActual = ordenamientos[columna] || 'nombre_asc';
    const ordenarSelect = document.getElementById('ordenarProductos');
    if (ordenarSelect) {
        ordenarSelect.value = ordenActual;
    }
    paginaActual = 1;
    renderizarTabla();
    actualizarIconosOrdenamiento();
}

// Función para actualizar íconos de ordenamiento
function actualizarIconosOrdenamiento() {
    const ths = document.querySelectorAll('#tablaProductos thead th');
    ths.forEach(th => {
        const icon = th.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-sort';
        }
    });

    let columnaActiva = '';
    if (ordenActual.includes('nombre')) columnaActiva = 'nombre';
    else if (ordenActual.includes('precio')) columnaActiva = 'precio';
    else if (ordenActual.includes('stock')) columnaActiva = 'stock';

    if (columnaActiva) {
        const thActivo = document.querySelector(`#tablaProductos thead th[data-order="${columnaActiva}"]`);
        if (thActivo) {
            const icon = thActivo.querySelector('i');
            if (icon) {
                icon.className = ordenActual.includes('asc') ? 'fas fa-sort-up' : 'fas fa-sort-down';
            }
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIALIZANDO PÁGINA DE PRODUCTOS ===');
    
    // Inicializar el select con el valor correcto
    const ordenarSelect = document.getElementById('ordenarProductos');
    if (ordenarSelect) {
        ordenarSelect.value = ordenActual;
        console.log('Select inicializado con valor:', ordenActual);
    }
    
    capturarDatosTabla();

    // Buscador
    const buscador = document.getElementById('buscadorProducto');
    const limpiarBuscador = document.getElementById('limpiarBuscador');

    if (buscador) {
        buscador.addEventListener('input', function(e) {
            textoBusquedaActual = e.target.value;
            limpiarBuscador.style.display = textoBusquedaActual ? 'block' : 'none';
            paginaActual = 1;
            renderizarTabla();
        });
    }

    if (limpiarBuscador) {
        limpiarBuscador.addEventListener('click', function() {
            buscador.value = '';
            textoBusquedaActual = '';
            limpiarBuscador.style.display = 'none';
            paginaActual = 1;
            renderizarTabla();
        });
    }

    // Ordenamiento por select
    if (ordenarSelect) {
        ordenarSelect.addEventListener('change', function(e) {
            const valorNuevo = e.target.value;
            console.log('=== CAMBIO DE ORDENAMIENTO ===');
            console.log('Valor anterior:', ordenActual);
            console.log('Valor nuevo:', valorNuevo);
            console.log('Select value:', this.value);
            
            ordenActual = valorNuevo;
            paginaActual = 1;
            renderizarTabla();
            actualizarIconosOrdenamiento();
            
            console.log('Ordenamiento actualizado a:', ordenActual);
        });
    } else {
        console.error('❌ No se encontró el select #ordenarProductos');
    }

    // Ordenamiento por clic en columna
    document.querySelectorAll('#tablaProductos thead th[data-order]').forEach(th => {
        th.addEventListener('click', function() {
            ordenarPorColumna(this.dataset.order);
        });
    });

    // Filtros de stock
    document.querySelectorAll('.prod-stock-filtro').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.prod-stock-filtro').forEach(b => b.classList.remove(
                'active'));
            this.classList.add('active');
            filtroStockActual = this.dataset.stockFiltro;
            console.log('Filtro de stock:', filtroStockActual);
            paginaActual = 1;
            renderizarTabla();
        });
    });

    // Filtro de categoría (modificado para trabajar con datos)
    document.querySelectorAll('.prod-filtro-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.prod-filtro-btn').forEach(b => b.classList.remove(
                'active'));
            this.classList.add('active');
            filtroCategoriaActual = this.dataset.cat;
            console.log('Filtro de categoría:', filtroCategoriaActual);
            paginaActual = 1;
            renderizarTabla();
        });
    });

    // Registros por página
    const regSelect = document.getElementById('registrosPorPagina');
    if (regSelect) {
        regSelect.addEventListener('change', function(e) {
            registrosPorPagina = parseInt(e.target.value);
            paginaActual = 1;
            renderizarTabla();
        });
    }

    // Render inicial
    console.log('Renderizando tabla inicial...');
    renderizarTabla();
    console.log('✅ Página inicializada correctamente');
});

function abrirAjusteStock(id, nombre, stock) {
    document.getElementById('stockId').value = id;
    document.getElementById('stockNombre').textContent = nombre;
    document.getElementById('stockActual').textContent = stock;
    new bootstrap.Modal(document.getElementById('modalStock')).show();
}
</script>
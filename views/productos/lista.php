<?php
declare(strict_types=1);
?>
<?php if ($mensaje !== ''): ?>
    <p class="notice <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>" role="status">
        <?= htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
<?php endif; ?>
<div class="toolbar">
    <a class="button" href="?action=productos.nuevo">Registrar producto</a>
</div>
<?php if ($productos === []): ?>
    <p>No hay productos registrados.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Stock mínimo</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr class="<?= $producto->tieneStockBajo() ? 'low-stock' : '' ?>">
                    <td><?= htmlspecialchars($producto->codigo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($producto->nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($producto->categoria, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td>S/ <?= htmlspecialchars($producto->precio_venta, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $producto->stock ?></td>
                    <td><?= $producto->stock_minimo ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
declare(strict_types=1);
?>
<?php if ($mensaje !== ''): ?>
    <p class="notice <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>" role="status">
        <?= htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
<?php endif; ?>
<?php if ($ventas === []): ?>
    <p>Aún no hay ventas registradas hoy.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Hora</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($venta->fecha_hora, 11, 8), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($venta->nombre_producto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= $venta->cantidad ?></td>
                    <td>S/ <?= htmlspecialchars($venta->precio_unitario, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>S/ <?= htmlspecialchars($venta->total, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4">Total del día</th>
                <th>S/ <?= htmlspecialchars($totalDia, ENT_QUOTES, 'UTF-8') ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>

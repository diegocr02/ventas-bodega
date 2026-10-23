<?php
declare(strict_types=1);

$valores = $valores ?? [];
$productoSeleccionado = (string) ($valores['producto_id'] ?? '');
$cantidadIngresada = htmlspecialchars(
    (string) ($valores['cantidad'] ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
<?php if ($mensaje !== ''): ?>
    <p class="notice <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>" role="alert">
        <?= htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
<?php endif; ?>
<?php if ($productos === []): ?>
    <p>Registre un producto antes de ingresar una venta.</p>
    <a class="button" href="?action=productos.nuevo">Registrar producto</a>
<?php else: ?>
    <form method="post" action="?action=ventas.registrar" class="form-grid">
        <label for="producto_id">Producto</label>
        <select id="producto_id" name="producto_id" required>
            <option value="">Seleccione un producto</option>
            <?php foreach ($productos as $producto): ?>
                <option value="<?= $producto->id ?>"
                    <?= (string) $producto->id === $productoSeleccionado ? 'selected' : '' ?>>
                    <?= htmlspecialchars($producto->codigo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    — <?= htmlspecialchars($producto->nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    (stock: <?= $producto->stock ?>; S/ <?= htmlspecialchars($producto->precio_venta, ENT_QUOTES, 'UTF-8') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="cantidad">Cantidad</label>
        <input id="cantidad" name="cantidad" type="number" min="1" step="1" required
               value="<?= $cantidadIngresada ?>">

        <div class="form-actions">
            <button type="submit">Registrar venta</button>
            <a href="?action=productos.lista">Cancelar</a>
        </div>
    </form>
<?php endif; ?>

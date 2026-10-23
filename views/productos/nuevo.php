<?php
declare(strict_types=1);

$valores = $valores ?? [];
$valor = static fn (string $campo): string => htmlspecialchars(
    (string) ($valores[$campo] ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
<?php if ($mensaje !== ''): ?>
    <p class="notice <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>" role="alert">
        <?= htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
<?php endif; ?>
<form method="post" action="?action=productos.guardar" class="form-grid">
    <label for="codigo">Código</label>
    <input id="codigo" name="codigo" maxlength="20" required value="<?= $valor('codigo') ?>">

    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" maxlength="120" required value="<?= $valor('nombre') ?>">

    <label for="categoria">Categoría</label>
    <input id="categoria" name="categoria" maxlength="60" required value="<?= $valor('categoria') ?>">

    <label for="precio_venta">Precio de venta</label>
    <input id="precio_venta" name="precio_venta" type="number" min="0" step="0.01" required
           value="<?= $valor('precio_venta') ?>">

    <label for="stock">Stock actual</label>
    <input id="stock" name="stock" type="number" min="0" step="1" required value="<?= $valor('stock') ?>">

    <label for="stock_minimo">Stock mínimo</label>
    <input id="stock_minimo" name="stock_minimo" type="number" min="0" step="1" required
           value="<?= $valor('stock_minimo') ?>">

    <div class="form-actions">
        <button type="submit">Guardar producto</button>
        <a href="?action=productos.lista">Cancelar</a>
    </div>
</form>

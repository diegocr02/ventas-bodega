<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> | Ventas Bodega</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="?action=productos.lista">Ventas Bodega</a>
    <nav aria-label="Navegación principal">
        <a href="?action=productos.lista">Productos</a>
        <a href="?action=productos.nuevo">Nuevo producto</a>
        <a href="?action=ventas.nueva">Registrar venta</a>
        <a href="?action=ventas.delDia">Ventas del día</a>
    </nav>
</header>
<main class="container">
    <h1><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
    <?= $content ?>
</main>
</body>
</html>

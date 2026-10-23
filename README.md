# ventas-bodega

Aplicacion web interna para registrar ventas y controlar el stock del minimarket en San Vicente de Canete.

## Estructura del proyecto

```
ventas-bodega/
├── app/                        # Modelos, repositorios, servicios, controladores
├── config/                     # Conexion PDO configurada desde .env
├── database/                   # Esquema y datos de prueba
├── public/                     # Punto de entrada HTTP y recursos publicos
├── views/                      # Plantillas HTML
└── .kiro/specs/ventas-bodega/  # Requisitos y diseno tecnico
```

## Stack

- PHP 8 OOP · `declare(strict_types=1)`
- MySQL (XAMPP) · base de datos `bodega_ventas` · `utf8mb4`
- PDO con sentencias preparadas
- Sin frameworks

## Documentacion

- Definicion del proyecto: `docs/D0.md`
- Requisitos: `.kiro/specs/ventas-bodega/requirements.md`
- Diseno tecnico: `.kiro/specs/ventas-bodega/design.md`

## Ejecucion local con XAMPP

1. Inicia Apache y MySQL desde el panel de XAMPP.
2. En phpMyAdmin, ejecuta `database/schema.sql` para crear la base de datos y las tablas.
3. Copia `.env.example` a `.env` y configura `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` para tu instalacion local. El archivo `.env` no se versiona.
4. Opcionalmente, ejecuta `database/seed.sql` para cargar productos de prueba.
5. Configura Apache para servir `public/` como directorio publico del proyecto y abre la aplicacion en el navegador.

La aplicacion requiere PHP 8 con PDO MySQL y las extensiones `mbstring` y `pdo_mysql`.

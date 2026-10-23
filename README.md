# ventas-bodega

Aplicación web interna para registrar ventas y controlar el stock de un minimarket.

## Requisitos

- PHP 8.0 o superior.
- Extensiones PHP `pdo_mysql` y `mbstring`.
- MySQL o MariaDB, por ejemplo el incluido en XAMPP.
- Apache de XAMPP o el servidor web integrado de PHP para desarrollo.

## Crear la base de datos

1. Inicia MySQL desde el panel de control de XAMPP.
2. Abre phpMyAdmin en `http://localhost/phpmyadmin/`.
3. Selecciona **Importar**, carga `database/schema.sql` y ejecútalo. El script crea
   `bodega_ventas` y sus tablas.
4. Para cargar los ocho productos de muestra, importa y ejecuta
   `database/seed.sql` después del esquema.

**Atención:** `database/schema.sql` elimina y vuelve a crear las tablas `venta` y
`producto`. `database/seed.sql` elimina las ventas y los productos existentes
antes de cargar los datos de muestra. No ejecutes esos archivos en una base con
datos que quieras conservar.

También se pueden ejecutar desde la terminal si el cliente MySQL está en el PATH:

```powershell
mysql -u root -p < database/schema.sql
mysql -u root -p bodega_ventas < database/seed.sql
```

Si el usuario local no tiene contraseña, omite `-p`.

## Configurar `.env`

En la raíz del proyecto, crea `.env` copiando `.env.example`. En PowerShell:

```powershell
Copy-Item .env.example .env
```

Configura estas variables según tu instalación local de MySQL:

```dotenv
DB_HOST=127.0.0.1
DB_NAME=bodega_ventas
DB_USER=root
DB_PASS=
```

`DB_PASS` puede quedar vacía si el usuario local de XAMPP no tiene contraseña.
No pongas credenciales reales en `.env.example` ni lo compartas: `.env` está
excluido de Git.

## Ejecutar la aplicación

### Servidor integrado de PHP (desarrollo)

Desde la raíz del repositorio, con PHP 8.0+ en el PATH:

```powershell
php -S 127.0.0.1:8088 -t public
```

Luego abre <http://127.0.0.1:8088/>. El proceso se detiene con `Ctrl+C`.

### Apache de XAMPP

Configura el `DocumentRoot` de un VirtualHost para apuntar a la carpeta `public`
del proyecto y reinicia Apache. Con un VirtualHost local llamado
`ventas-bodega.local`, la dirección sería `http://ventas-bodega.local/`.
No uses la raíz completa del repositorio como directorio público, porque allí
están `.env`, configuración y código no destinado a exposición web.

## Ejecutar las pruebas

Las pruebas de los Services usan repositorios falsos en memoria; no necesitan
MySQL, PHPUnit ni Composer. Desde la raíz del repositorio ejecuta:

```powershell
php tests/run.php
```

Para usar específicamente el PHP incluido en XAMPP desde PowerShell:

```powershell
& 'C:\xampp\php\php.exe' tests/run.php
```

Cada `[OK]` indica un caso satisfactorio. Al final, `N correctas; 0 fallidas`
significa que todas las pruebas pasaron; el comando devuelve código de salida
distinto de cero si falla alguna.

## Documentación del proyecto

- Requisitos: `.kiro/specs/ventas-bodega/requirements.md`
- Diseño técnico: `.kiro/specs/ventas-bodega/design.md`
- Deuda técnica: `TECH_DEBT.md`

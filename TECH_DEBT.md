# Deuda técnica

Atajos elegidos para completar el alcance actual. Conviene revisarlos antes de
ampliar el sistema o exponerlo a más usuarios.

| Atajo | Por qué se tomó | Impacto | Cuándo pagar la deuda |
|---|---|---|---|
| Enrutamiento y composición de dependencias manuales en `public/index.php`, sin framework ni contenedor. | El alcance tiene pocas pantallas y el diseño pide evitar frameworks. | El front controller asume más responsabilidades y cada ruta nueva requiere edición manual; es más fácil introducir errores al crecer. | Cuando se agreguen módulos o rutas, extraer el enrutamiento y la composición a componentes pequeños, conservando un punto de entrada HTTP. |
| Pruebas con un runner propio y repositorios falsos en memoria, sin PHPUnit ni pruebas de integración MySQL. | Permite probar reglas de negocio con `php tests/run.php` sin instalar herramientas ni depender de una base de datos. | Las pruebas no comprueban el SQL real, restricciones del motor, bloqueo `FOR UPDATE` ni el rollback ante fallos reales de persistencia. | Antes de una puesta en producción o de cambiar repositorios/esquema: añadir pruebas de integración aisladas contra MySQL/MariaDB, incluyendo fallos entre INSERT y UPDATE. |
| Protección CSRF basada en sesiones nativas PHP y un helper pequeño, sin capa de seguridad más amplia ni validación de `Origin`. | Resuelve la protección requerida en los formularios mutables sin añadir dependencias; actualmente no hay autenticación ni cuentas de usuario. | La protección depende de la configuración segura del cookie de sesión y del despliegue; el chequeo adicional de origen no está implementado. | Antes de habilitar acceso desde redes no confiables o añadir autenticación: revisar configuración de cookies/sesión por entorno, validar origen cuando corresponda y probar los escenarios CSRF en el navegador. |
| Estilos y experiencia de usuario implementados con CSS propio y HTML de formularios sencillo. | El objetivo inmediato es tener flujos funcionales sin incorporar dependencias front-end. | La interfaz es básica y carece de un sistema formal de componentes, diseño adaptable exhaustivo y pruebas de accesibilidad. | Cuando se solicite una interfaz más amplia o se valide con usuarios: definir tokens/componentes visuales y verificar accesibilidad y tamaños de pantalla. |

## Compatibilidad del entorno local

El código debe mantenerse compatible con PHP 8.0, que es la versión incluida
en el Apache de XAMPP de este entorno. Las propiedades `readonly` se retiraron
para cumplir ese requisito; los modelos exponen propiedades públicas mutables
y las dependencias usan propiedades promovidas privadas sin `readonly`.
Al incorporar nuevas características del lenguaje, ejecutar el lint y las
pruebas con `C:\xampp\php\php.exe` antes de aceptar el cambio.

| Fase | Qué generó la IA | Qué corregí o rechacé | Por qué |
|------|------------------|-----------------------|---------|
| 1 | D0.md con RF, RNF, RN, alcance y supuestos | Agregué RNF de seguridad (PDO preparado, .env, escape de salida) | La rúbrica pide seguridad e integridad; Kiro solo puso integridad |
| 1 | D0.md | Quité "suma el total a mano" y marqué RN1 "entero" como SUPUESTO | No está en el caso |
| 2 | requirements.md — CA-RF3.1 redactado como «CUANDO el cajero registre una venta exitosa» | Separé en CA-RF3.2 y CA-RF3.3 los casos de rechazo (cantidad ≤ 0 y stock insuficiente), en lugar de mezclarlos en un solo criterio positivo | Un criterio EARS debe cubrir una sola condición; mezclar el caso feliz con los rechazos impide verificar RN1 y RN2 por separado |
| 2 | requirements.md — RNF3 (disponibilidad en red local) incluido como criterio de aceptación | Moví la condición de red local a Supuestos (S1 en D0.md) y eliminé el criterio de aceptación correspondiente | La disponibilidad en red local es un supuesto de despliegue, no un comportamiento verificable del sistema en tiempo de ejecución; incluirlo como CA generaba un requisito que el código no puede satisfacer ni fallar por sí solo |
| 2 | design.md con capas, tablas, contratos, transacción y errores | Moví schema.sql y seed.sql a database/ | El examen exige esa ruta |
| 2 | design.md | Agregué CHECK (stock >= 0) y CHECK (stock_minimo >= 0); Kiro los omitió por usar UNSIGNED | La Fase 3 exige CHECK explícito para stock |
| 2 | design.md | Agregué índice en fecha_hora y filtro por rango en lugar de DATE() | DATE() impide usar el índice en el listado del día |
| 3 | schema.sql, seed.sql, .env, .env.example, .gitignore | Aceptado sin cambios; verifiqué seed contra la tabla del caso y COUNT(*) = 8 | Se ejecutó sin errores en phpMyAdmin |
| 4 | Aplicación por capas + tests/run.php (7 OK) | Acepté interfaces de repositorio no previstas en design.md; verifiqué cobertura RN1–RN5 en tests | Las interfaces permiten probar Services sin MySQL |
| 4 | Cálculo en centavos enteros en lugar de bcmul | Acepté y actualicé design.md | Evita errores de float y no depende de la extensión bcmath |
| 5 | TECH_DEBT.md y README.md | Rechacé dejar PHP 8.1 como requisito; pedí compatibilidad con PHP 8.0.30 (sin readonly) | El Apache de XAMPP es 8.0 y la app fallaba con error fatal |
# ventas-bodega

Aplicacion web interna para registrar ventas y controlar el stock del minimarket en San Vicente de Canete.

## Estructura del proyecto

```
ventas-bodega/
├── .kiro/
│   └── specs/
│       └── ventas-bodega/
│           ├── .config.kiro    # Configuracion del spec (Kiro)
│           ├── requirements.md # Criterios de aceptacion EARS
│           └── design.md       # Arquitectura, DDL y contratos de clases
├── docs/
│   ├── D0.md                   # Documento de definicion del proyecto
│   ├── PROMPTS.md              # Historial de prompts del proyecto
│   └── REGISTRO.md             # Correcciones y decisiones de diseno
└── README.md
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

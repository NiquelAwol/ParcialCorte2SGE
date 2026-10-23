# Droguería Vida Sana &bull; Segundo Parcial (Corte 2)
## Corporación de Estudios Tecnológicos del Norte del Valle — COTECNOVA
### Asignatura: Software de Gestión Empresarial (SGE)
**Docente:** Seminario Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD  
**Estudiante:** Andrés Felipe Mejía ([@NiquelAwol](https://github.com/NiquelAwol))  
Miguel Angel Ramirez ([@MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez))
**Fecha:** Octubre 2026  

---

## 1. Contexto Empresarial

La **Droguería Vida Sana**, ubicada en Cartago (Valle del Cauca), comercializa medicamentos de venta libre, medicamentos con fórmula médica, medicamentos de control especial y productos de cuidado personal. Para solucionar problemáticas operativas graves (venta involuntaria de productos vencidos y despacho de control especial sin supervisión técnica), este proyecto implementa la arquitectura integral de gestión farmacéutica:

* **Trazabilidad estricta por lotes y caducidades:** Registro riguroso de número de lote y fecha de vencimiento.
* **Regla sanitaria FEFO (First Expired, First Out):** Despacho prioritario del lote que vence antes, bloqueando totalmente cualquier lote vencido.
* **Segregación de funciones:** Separación entre quien genera la orden de compra y quien recibe físicamente la mercancía en bodega.
* **Control de acceso con Roles y Permisos (Spatie):** Matriz de seguridad para Administrador, Regente de Farmacia, Cajero y Auxiliar de Bodega.

---

## 2. Estructura de Entregables del Parcial

Todos los requisitos del Segundo Parcial se encuentran organizados en la rama `parcial2`:

* **Punto 1, 2 y 3 (Análisis, Diccionario y Matriz):** [`docs/parcial2/analisis_drogueria.md`](docs/parcial2/analisis_drogueria.md)
* **Punto 2 (Diagrama Entidad-Relación con Cardinalidades):** [`docs/parcial2/er_drogueria.png`](docs/parcial2/er_drogueria.png)
* **Punto 4 (Código Fuente):**
  * Migraciones: [`database/migrations/`](database/migrations/) (tabla `lots` con índice único y FK)
  * Modelos: [`app/Models/Lot.php`](app/Models/Lot.php), [`app/Models/Product.php`](app/Models/Product.php)
  * Seeder de Seguridad: [`database/seeders/DrogueriaRolesSeeder.php`](database/seeders/DrogueriaRolesSeeder.php)
  * Servicio FEFO: [`app/Services/DispensingService.php`](app/Services/DispensingService.php)
* **Punto 4.4 (Evidencias de Ejecución):** [`docs/parcial2/capturas/`](docs/parcial2/capturas/)
  * `01_describe_lots_mysql.png`: Estructura en MySQL e índices.
  * `02_codigo_modelo_lot.png`: Código del modelo `Lot`.
  * `03_codigo_seeder_roles.png`: Código del seeder de seguridad.
  * `04_codigo_servicio_dispense.png`: Código del servicio transaccional `DispensingService`.
  * `05_tinker_antes_despacho.png`: Creación de lotes y estado inicial en Tinker.
  * `06_tinker_despues_despacho.png`: Despacho de 8 unidades aplicando FEFO en Tinker.
  * `07_tinker_error_stock_insuficiente.png`: Validación y Rollback en Tinker.

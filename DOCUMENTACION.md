\# Bitácora técnica — Reto 9: Formulario de contacto con Laminas



\*\*Curso:\*\* Desarrollo Web con Frameworks 4

\*\*Unidad:\*\* U2.2 — Reto 9

\*\*Autor:\*\* jdthg

\*\*Fecha:\*\* Octubre 2026



\---



\## 📖 Introducción



Este documento complementa al `README.md` del proyecto y recoge, de forma cronológica y detallada, \*\*todo el proceso de desarrollo del Reto 9\*\*, incluyendo:



\- El entorno técnico empleado y las decisiones que llevaron a él

\- Las incompatibilidades y errores encontrados por el camino

\- El análisis de cada problema y la solución aplicada

\- Las lecciones aprendidas



El objetivo es doble: \*\*dejar constancia del trabajo técnico\*\* y \*\*servir como referencia\*\* a futuros desarrollos en entornos similares (PHP + framework MVC + Windows).



\---



\## 🎯 Resumen ejecutivo



| Aspecto | Resultado |

|---|---|

| Framework final | \*\*Laminas MVC\*\* (sucesor oficial de Zend Framework) |

| Versión del framework | Laminas MVC 3.x |

| PHP | 7.4.33 NTS x64 en `C:\\php74` |

| Composer | 2.10.3 |

| Base de datos | MariaDB 10.4.32 (XAMPP) en `127.0.0.1:3306` |

| Sistema operativo | Windows 11 (Build 26300) |

| Servidor de desarrollo | PHP built-in server (`php -S`) |

| Incidencias resueltas | \*\*17 documentadas\*\* |

| Estado final | ✅ Todos los Pasos 1-6 completados y verificados |



\---



\## 🖥️ Entorno de desarrollo



\### Hardware y sistema operativo



\- \*\*Sistema:\*\* Windows 11 (Build 26300: Service Pack 0)

\- \*\*Arquitectura:\*\* AMD64

\- \*\*Terminal:\*\* PowerShell 7.6.6



\### Componentes instalados



| Componente | Versión | Ruta |

|---|---|---|

| PHP (para Laminas) | 7.4.33 NTS x64 | `C:\\php74\\php.exe` |

| PHP (sistema, WinGet) | 8.4.25 | `C:\\Users\\jdthg\\AppData\\Local\\Microsoft\\WinGet\\Packages\\PHP.PHP.8.4\_...` |

| Composer | 2.10.3 | `C:\\ProgramData\\ComposerSetup\\bin\\` |

| XAMPP | 8.x | `C:\\xampp\\` |

| MariaDB | 10.4.32 | Puerto 3306 |

| Visual C++ Redistributable | 14.51.36247 | Instalado |



\### La convivencia de dos PHPs



Un factor clave del desarrollo fue la \*\*coexistencia de dos instalaciones de PHP\*\*:



1\. \*\*PHP 8.4.25\*\* instalado vía WinGet (para proyectos generales)

2\. \*\*PHP 7.4.33\*\* instalado manualmente en `C:\\php74` (requerido por Laminas MVC 3.x)



La convivencia provocó numerosos conflictos porque:



\- Windows resuelve `php` según el orden del `PATH`

\- Composer (un `.phar`) usa el `php.exe` que encuentre primero

\- Algunos scripts heredan variables de entorno de sesiones anteriores



\*\*Solución estable adoptada:\*\* invocar siempre de forma explícita:



```powershell

C:\\php74\\php.exe -c C:\\php74\\php.ini <script>

```



Y para Composer:



```powershell

$env:PHPRC = "C:\\php74"

$env:Path  = "C:\\php74;" + $env:Path

composer <comando>

```



\---



\## 🧭 Decisiones arquitectónicas clave



\### 1. Migración de Zend Framework 3 a Laminas



\*\*Motivo:\*\* Zend Framework fue archivado en 2019. Su sucesor oficial es \*\*Laminas\*\*, mantenido por la Linux Foundation. La API es idéntica; solo cambia el namespace raíz.



| Zend | Laminas |

|---|---|

| `Zend\\Form` | `Laminas\\Form` |

| `Zend\\Db` | `Laminas\\Db` |

| `Zend\\Validator` | `Laminas\\Validator` |

| `Zend\\View` | `Laminas\\View` |



\*\*Impacto en el reto:\*\* ninguno. Se cumple íntegramente el enunciado.



\### 2. Uso de PHP 7.4 en lugar de 8.x



\*\*Motivo:\*\* Laminas MVC 3.x (y su predecesor Zend Framework 3) fue diseñado para PHP 7.x. En PHP 8.x:



\- Aparecen múltiples `Deprecated` en el framework (parámetros implícitamente nullable)

\- El skeleton oficial está marcado como compatible sólo hasta PHP 7.x

\- Composer rechaza la instalación de paquetes que requieren `composer-plugin-api ^1.0`



\### 3. Uso de `127.0.0.1` en lugar de `localhost`



\*\*Motivo:\*\* en Windows, `localhost` puede resolverse a IPv6 (`::1`). MariaDB de XAMPP escucha por defecto en IPv4 (`0.0.0.0:3306`), por lo que la conexión falla con `\[2002] Connection refused`. Usar `127.0.0.1` fuerza IPv4.



\### 4. Sustitución de `EmailAddress` por `Regex` personalizado



\*\*Motivo:\*\* el validador `Laminas\\Validator\\EmailAddress` internamente usa un `Regex` cuyos mensajes \*\*no son sobrescribibles\*\* desde la API pública en Laminas 2.x. Para obtener un único mensaje en español, se reemplazó por un `Validator\\Regex` con patrón de email estándar.



\### 5. Añadir `novalidate` al formulario



\*\*Motivo:\*\* el navegador aplica validación HTML5 nativa sobre `<input type="email">`, bloqueando el POST. Laminas nunca recibe los datos inválidos y por tanto no puede mostrar sus mensajes. Con `novalidate`, la validación siempre llega al servidor.



\---



\## 🐞 Bitácora detallada de incidencias



\### Incidencia 1 — Incompatibilidad de plugins con Composer 2



\- \*\*Contexto:\*\* Instalación inicial de Zend Framework 3 Skeleton.

\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; - Root composer.json requires zendframework/zend-skeleton-installer ^0.1.7 || ^1.0

&#x20; - zendframework/zend-skeleton-installer 0.1.7 requires composer-plugin-api ^1.0

&#x20; - found composer-plugin-api\[2.9.0] but it does not match the constraint.

&#x20; ```

\- \*\*Análisis:\*\* el esqueleto de Zend Framework 3 depende de plugins que exigen `composer-plugin-api ^1.0`. Composer 2.x expone `2.9.0`.

\- \*\*Solución:\*\* degradar temporalmente a Composer 1.x (`composer self-update --1`).

\- \*\*Lección:\*\* al trabajar con frameworks antiguos, verificar la compatibilidad de los plugins con la versión de Composer activa.



\### Incidencia 2 — Packagist cerró el soporte a Composer 1



\- \*\*Síntoma:\*\* Advertencia y fallo al descargar paquetes:

&#x20; ```

&#x20; Warning from https://repo.packagist.org: Support for Composer 1 has been

&#x20; shutdown on September 1st 2025. You should upgrade to Composer 2.

&#x20; ```

\- \*\*Análisis:\*\* Packagist dejó de servir metadatos a Composer 1 a partir de agosto de 2025.

\- \*\*Solución:\*\* volver a Composer 2 y migrar el proyecto a Laminas.

\- \*\*Lección:\*\* las dependencias del ecosistema evolucionan; no se puede depender indefinidamente de versiones antiguas de herramientas.



\### Incidencia 3 — Error `Module (Laminas\\Form) could not be initialized`



\- \*\*Síntoma:\*\* `Fatal error: Module (Laminas\\Form) could not be initialized`

\- \*\*Análisis:\*\* el módulo `Laminas\\Session` no estaba cargado, pero `FlashMessenger` (que sí estaba registrado) lo requiere. El error reportaba el primer módulo de la cadena como fallo, lo cual era engañoso.

\- \*\*Solución:\*\* completar `config/modules.config.php` con `Laminas\\Session`, `Laminas\\Mvc\\Plugin\\FlashMessenger`, `Laminas\\Mvc\\Plugin\\Prg`, `Laminas\\Mvc\\Plugin\\Identity`.

\- \*\*Lección:\*\* los módulos de Laminas/Zend tienen dependencias cruzadas; el mensaje del error puede señalar al módulo equivocado.



\### Incidencia 4 — `Laminas\\I18n` en la config sin estar instalado



\- \*\*Síntoma:\*\* tras corregir la incidencia 3, apareció un error de módulo no encontrado.

\- \*\*Análisis:\*\* el instalador de Laminas había inyectado `Laminas\\I18n` en `modules.config.php` aunque habíamos respondido `n` a esa instalación.

\- \*\*Solución:\*\* eliminar `Laminas\\I18n` del array de módulos.

\- \*\*Lección:\*\* revisar siempre qué módulos inyecta automáticamente el instalador de componentes.



\### Incidencia 5 — `Laminas\\ZendFrameworkBridge` sin instalar



\- \*\*Síntoma:\*\* error al cargar la app en modo development.

\- \*\*Análisis:\*\* `development.config.php.dist` incluía `Laminas\\ZendFrameworkBridge`, paquete no instalado.

\- \*\*Solución:\*\* eliminar esa línea del archivo de development.

\- \*\*Lección:\*\* los archivos `.dist` del skeleton traen módulos por defecto que pueden no estar instalados.



\### Incidencia 6 — Extensión `intl` requerida por Composer



\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; To enable extensions, verify that they are enabled in your .ini files:

&#x20;   - C:\\php74\\php.ini

&#x20; ```

\- \*\*Análisis:\*\* Composer intentó instalar paquetes que requerían `intl`, pero no estaba habilitada en `php.ini`.

\- \*\*Solución:\*\* descomentar `extension=intl` en `C:\\php74\\php.ini`. Verificar que las DLLs ICU (`icu\*.dll`) estén en `C:\\php74`.

\- \*\*Lección:\*\* `intl` en Windows requiere DLLs de ICU junto al binario, no solo el `.ini`.



\### Incidencia 7 — `Undefined array key "db"` en `AdapterServiceFactory`



\- \*\*Síntoma:\*\* `PHP Warning: Undefined array key "db"` al construir el adaptador.

\- \*\*Análisis:\*\* el archivo `config/autoload/global.php` no existía o su caché estaba desactualizada.

\- \*\*Solución:\*\* crear `global.php` con la clave `db` y borrar `data/cache/\*`.

\- \*\*Lección:\*\* Laminas cachea la configuración combinada; cambios en `global.php` no se reflejan hasta limpiar caché.



\### Incidencia 8 — `could not find driver` (PDO)



\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; PDOException: could not find driver

&#x20; ```

\- \*\*Análisis:\*\* PHP 7.4 estaba cargando el `php.ini` de PHP 8.4 (WinGet). Verificado con `php --ini`.

\- \*\*Solución:\*\* definir `PHPRC=C:\\php74` o invocar con `-c C:\\php74\\php.ini`.

\- \*\*Lección:\*\* con múltiples PHPs, comprobar siempre con `php --ini` qué archivo de configuración se está leyendo.



\### Incidencia 9 — `Unable to load dynamic library 'php\_pdo\_mysql.dll'`



\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; Warning: PHP Startup: Unable to load dynamic library 'php\_pdo\_mysql.dll'

&#x20; (tried: C:\\php74\\ext\\php\_pdo\_mysql.dll)

&#x20; ```

&#x20; …aunque el archivo \*\*existía\*\*.

\- \*\*Análisis:\*\* había una línea duplicada `extension=php\_pdo\_mysql.dll` (sintaxis de PHP 5.x) además de `extension=pdo\_mysql`. PHP 7.2+ interpreta el valor como \*\*nombre de extensión\*\*, no como \*\*ruta de archivo\*\*, y busca `php\_php\_pdo\_mysql.dll.dll`.

\- \*\*Solución:\*\* dejar solo `extension=pdo\_mysql`.

\- \*\*Lección:\*\* desde PHP 7.2, usar `extension=nombre`, no `extension=php\_nombre.dll`.



\### Incidencia 10 — `extension\_dir` con ruta relativa



\- \*\*Síntoma:\*\* mismo error que la incidencia 9 pero para todas las extensiones.

\- \*\*Análisis:\*\* `extension\_dir = "ext"` era relativo al directorio de trabajo, no a `C:\\php74`.

\- \*\*Solución:\*\* cambiar a `extension\_dir = "C:/php74/ext"` (ruta absoluta).

\- \*\*Lección:\*\* en Windows, usar siempre rutas absolutas con barras normales para `extension\_dir`.



\### Incidencia 11 — Mezcla de PHP 7.4 y PHP 8.4 en el mismo comando



\- \*\*Síntoma:\*\* al ejecutar `composer dump-autoload`, aparecían simultáneamente:

&#x20; - Warnings de extensiones de PHP 7.4 (`Unable to load...`)

&#x20; - Errores de deprecación de PHP 8.4 (`Implicitly marking parameter ... is nullable`)

\- \*\*Análisis:\*\* la sesión tenía variables `PHPRC` y `PATH` apuntando a versiones distintas.

\- \*\*Solución:\*\* invocar explícitamente `C:\\php74\\php.exe -c C:\\php74\\php.ini` para cualquier script.

\- \*\*Lección:\*\* no confiar en la resolución implícita del `PATH`; usar rutas absolutas.



\### Incidencia 12 — `SQLSTATE\[HY000] \[2002] Connection refused`



\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; SQLSTATE\[HY000] \[2002] No se puede establecer una conexión ya que el

&#x20; equipo de destino denegó expresamente dicha conexión.

&#x20; ```

\- \*\*Análisis:\*\* `hostname = 'localhost'` en `global.php`. En Windows, `localhost` puede resolverse a IPv6 (`::1`), pero MariaDB escucha en IPv4.

\- \*\*Solución:\*\* cambiar `hostname` a `127.0.0.1` y añadir `'port' => 3306`.

\- \*\*Lección:\*\* en Windows, siempre usar `127.0.0.1` en lugar de `localhost` para MySQL.



\### Incidencia 13 — `SQLSTATE\[HY000] \[1049] Unknown database`



\- \*\*Síntoma:\*\* `Unknown database 'reto9\_contact'`

\- \*\*Análisis:\*\* la BD había sido creada en otra instancia de MySQL, no en la de XAMPP.

\- \*\*Solución:\*\* `CREATE DATABASE reto9\_contact` desde el cliente de XAMPP (`C:\\xampp\\mysql\\bin\\mysql.exe`).

\- \*\*Lección:\*\* confirmar con `SHOW DATABASES` que la BD existe en el motor que usa la app.



\### Incidencia 14 — Instalador VC++ `0x80070666`



\- \*\*Síntoma:\*\* al instalar `vc\_redist.x64.exe`:

&#x20; ```

&#x20; Error 0x80070666: Cannot install a product when a newer version is installed.

&#x20; ```

\- \*\*Análisis:\*\* el sistema ya tenía una versión más reciente (14.51.36247 vs. 14.44.35211 del instalador).

\- \*\*Solución:\*\* no hacer nada. El runtime está OK.

\- \*\*Lección:\*\* los errores de downgrade de instaladores no siempre indican problema real.



\### Incidencia 15 — Mensaje del `Regex` interno de `EmailAddress` en inglés



\- \*\*Síntoma:\*\* con datos inválidos, aparecían dos mensajes:

&#x20; ```

&#x20; \[email] The input does not match against pattern '/^...$/'

&#x20; \[email] El correo electrónico no tiene un formato válido.

&#x20; ```

\- \*\*Análisis:\*\* `Laminas\\Validator\\EmailAddress` usa internamente un `Regex` cuyas claves de mensaje (`regexNotMatch`) \*\*no están expuestas\*\* en la API pública para sobrescribir.

\- \*\*Solución:\*\* sustituir `EmailAddress` por `Validator\\Regex` con un patrón de email estándar. Un solo validador, un solo mensaje traducible.

\- \*\*Lección:\*\* cuando un validador tiene submensajes no sobrescribibles, usar un validador más simple.



\### Incidencia 16 — Validación HTML5 bloquea el POST



\- \*\*Síntoma:\*\* los errores de Laminas no aparecían al enviar datos inválidos desde el navegador. Solo aparecía el tooltip nativo del navegador.

\- \*\*Análisis:\*\* `<input type="email">` activa validación HTML5. El navegador bloquea el envío y nunca se llega al servidor.

\- \*\*Solución:\*\* añadir `novalidate` al `<form>` para que la validación siempre llegue a Laminas.

\- \*\*Lección:\*\* en aplicaciones con validación server-side, deshabilitar la validación HTML5 nativa para que la experiencia sea consistente.



\### Incidencia 17 — `hasRoute` no existe en `PriorityList`



\- \*\*Síntoma:\*\*

&#x20; ```

&#x20; Error: Call to undefined method Laminas\\Router\\PriorityList::hasRoute()

&#x20; ```

\- \*\*Análisis:\*\* la API del router de Laminas expone `match()` con un objeto `Request`, no `hasRoute()`.

\- \*\*Solución:\*\* usar `$router->match($request)` con `Laminas\\Http\\Request` para verificar rutas.

\- \*\*Lección:\*\* verificar la API real del framework antes de usarla en tests.



\---



\## ✅ Verificación final



| Verificación | Método | Resultado |

|---|---|---|

| PHP 7.4 activo | `php -v` | ✅ 7.4.33 |

| Extensiones críticas | `php -m` | ✅ pdo\_mysql, mbstring, curl, openssl, intl, mysqli |

| Composer usa PHP 7.4 | `composer -V` | ✅ PHP 7.4.33 |

| Formulario renderiza | `GET /contact` | ✅ |

| Envío válido | POST con datos correctos | ✅ alert verde + fila en BD |

| Envío inválido | POST con datos malos | ✅ 3 alerts rojos en español |

| Patrón PRG | F5 tras envío | ✅ sin duplicación |

| Persistencia | `SELECT \* FROM contacts` | ✅ fila con `fecha` autogenerada |

| Developer Tools | Barra en la vista | ✅ activa |



\---



\## 🎓 Conclusión



El desarrollo del Reto 9 requirió \*\*17 resoluciones de incompatibilidades\*\* que abarcaron:



\- Gestión de versiones y dependencias (PHP, Composer, Zend/Laminas)

\- Configuración de extensiones PHP en Windows

\- Diferencias sutiles entre `localhost` e `127.0.0.1`

\- Limitaciones de la API pública de los validadores de Laminas

\- Interacción entre validación HTML5 y validación server-side

\- Patrones de arquitectura web (PRG, FlashMessenger, TableGateway)



El proyecto final \*\*cumple íntegramente los requisitos del reto\*\*, adaptado a un framework con soporte activo (Laminas), con la lógica y estructura solicitadas en el enunciado. Las incidencias documentadas aquí sirven como referencia para cualquier desarrollo PHP + framework MVC en Windows en el futuro.



\---



\## 👤 Autor



\*\*jdthg\*\*

Curso de Desarrollo Web con Frameworks 4

Octubre 2026


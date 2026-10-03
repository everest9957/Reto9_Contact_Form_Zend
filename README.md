# Reto 9 — Formulario de contacto con Laminas (ex Zend Framework)

**Curso:** Desarrollo Web con Frameworks 4
**Unidad:** U2.2 — Reto 9
**Autor:** Judit Giravent
**Fecha:** Octubre 2026

---
## 🧩 Tecnologías del proyecto

![PHP](https://img.shields.io/badge/PHP-7.4.33-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laminas](https://img.shields.io/badge/Laminas-3.x-00A0DF?style=for-the-badge&logo=laminas&logoColor=white)
![Zend](https://img.shields.io/badge/Zend_Framework-3.x-68B604?style=for-the-badge&logo=zend&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-2.10.3-885630?style=for-the-badge&logo=composer&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-10.4.32-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Compatible-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

![XAMPP](https://img.shields.io/badge/XAMPP-8.x-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![phpMyAdmin](https://img.shields.io/badge/phpMyAdmin-5.x-6C78AF?style=for-the-badge&logo=phpmyadmin&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![PowerShell](https://img.shields.io/badge/PowerShell-7.6-5391FE?style=for-the-badge&logo=powershell&logoColor=white)
![Git](https://img.shields.io/badge/Git-2.50-F05032?style=for-the-badge&logo=git&logoColor=white)
![GitHub](https://img.shields.io/badge/GitHub-everest9957-181717?style=for-the-badge&logo=github&logoColor=white)

![Windows](https://img.shields.io/badge/Windows-11-0078D4?style=for-the-badge&logo=windows11&logoColor=white)
![VS Code](https://img.shields.io/badge/VS_Code-Editor-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)

## 📋 Índice

1. [Descripción del reto](#-descripción-del-reto)
2. [Decisiones técnicas](#-decisiones-técnicas)
3. [Requisitos e instalación](#️-requisitos-e-instalación)
4. [Estructura del proyecto](#️-estructura-del-proyecto)
5. [Flujo de la aplicación](#-flujo-de-la-aplicación)
6. [Documentación por pasos](#-documentación-por-pasos)
7. [Incidencias resueltas](#-incidencias-resueltas)
8. [Cómo probar el proyecto](#-cómo-probar-el-proyecto)
9. [Tecnologías usadas](#-tecnologías-usadas)

---

## 📋 Descripción del reto

Implementar un **formulario de contacto** en una aplicación **Laminas MVC** (sucesor oficial de Zend Framework) que permita a los usuarios enviar mensajes que se almacenen en una **base de datos MySQL**. El formulario incluye:

- Campo **nombre** (2 a 100 caracteres)
- Campo **email** (formato válido)
- Campo **mensaje** (10 a 1000 caracteres)
- **Validaciones** con `Laminas\Validator`
- **Persistencia** con `Laminas\Db\TableGateway`
- **Retroalimentación al usuario** mediante `FlashMessenger` y patrón **PRG** (Post/Redirect/Get)

---

## 🧭 Decisiones técnicas

### Migración de Zend Framework 3 a Laminas

El reto pide originalmente **Zend Framework 3** (`Zend\Form`, `Zend\Db`, `Zend\Validator`, `Zend\View`). Sin embargo:

- Zend Framework fue **archivado en 2019**.
- Su sucesor oficial es **Laminas**, mantenido por la Linux Foundation.
- La API es **100% compatible** a nivel de clases y métodos.

**Mapeo de namespaces aplicado:**

| Zend Framework (reto) | Laminas (implementación) |
|---|---|
| `Zend\Form` | `Laminas\Form` |
| `Zend\Db` | `Laminas\Db` |
| `Zend\Validator` | `Laminas\Validator` |
| `Zend\View` | `Laminas\View` |
| `Zend\Mvc` | `Laminas\Mvc` |

La lógica del reto se cumple **íntegramente**; solo cambia el nombre del namespace raíz.

### Entorno

- **PHP 7.4.33** (NTS, x64) en `C:\php74` — requerido por compatibilidad con Laminas MVC 3.x
- **Composer 2.10.3**
- **XAMPP** con MariaDB 10.4.32 en `127.0.0.1:3306`
- **Laminas MVC Skeleton** como base del proyecto
- **Laminas Developer Tools** para depuración en modo development

---

## ⚙️ Requisitos e instalación

### Requisitos

| Componente | Versión | Ubicación |
|---|---|---|
| PHP | 7.4.33 NTS x64 | `C:\php74\php.exe` |
| Composer | 2.x | Global |
| MariaDB | 10.4.x | XAMPP, puerto 3306 |
| Extensión PHP `pdo_mysql` | — | Habilitada en `php.ini` |
| Extensión PHP `openssl` | — | Habilitada en `php.ini` |
| Extensión PHP `mbstring` | — | Habilitada en `php.ini` |

### Configuración de PHP (`C:\php74\php.ini`)

```ini
extension_dir = "C:/php74/ext"
extension=curl
extension=intl
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
date.timezone = America/Mexico_City
```

### Crear la base de datos

```sql
CREATE DATABASE IF NOT EXISTS reto9_contact
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE reto9_contact;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Configurar la conexión (`config/autoload/global.php`)

```php
return [
    'db' => [
        'driver'   => 'Pdo_Mysql',
        'database' => 'reto9_contact',
        'username' => 'root',
        'password' => '',
        'hostname' => '127.0.0.1',   // ← no usar 'localhost' (IPv6 en Windows)
        'port'     => 3306,
        'charset'  => 'utf8mb4',
    ],
];
```

### Levantar el servidor de desarrollo

```powershell
cd C:\Users\jdthg\Documents\cursoDesarrolloWebFrameworks4\Reto9_Contact_Form_Zend
C:\php74\php.exe -c C:\php74\php.ini -S 0.0.0.0:8080 -t public public/index.php
```

Abrir en el navegador: **http://localhost:8080/contact**

---

## 🗂️ Estructura del proyecto

```
Reto9_Contact_Form_Zend/
├── capturas/                                # 25 capturas del proceso (P1_01 … P6_02)
├── config/
│   ├── application.config.php
│   └── autoload/
│       └── global.php                       # Configuración del adaptador de BD
├── data/
│   └── cache/                               # Caché de configuración (gitignored)
├── module/
│   └── Application/
│       ├── config/
│       │   └── module.config.php            # Rutas, controladores, servicios
│       ├── src/
│       │   ├── Controller/
│       │   │   └── ContactController.php    # indexAction + processAction
│       │   ├── Form/
│       │   │   └── ContactForm.php          # Formulario + validaciones
│       │   └── Model/
│       │       ├── Contact.php              # Entidad
│       │       └── ContactTable.php         # Mapper con TableGateway
│       └── view/
│           └── application/
│               └── contact/
│                   └── index.phtml          # Vista del formulario + flash messages
├── public/
│   └── index.php
├── vendor/                                  # Dependencias (gitignored)
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

---

## 🔄 Flujo de la aplicación

```
Usuario → GET /contact          → ContactController::indexAction  → index.phtml
Usuario → POST /contact/process → ContactController::processAction
                                     ├─ setData($request->getPost())
                                     ├─ isValid()  → si falla: FlashMessenger + redirect
                                     └─ saveContact() + FlashMessenger + redirect
                                                       ↓
                                              GET /contact (PRG)
```

### Rutas registradas

| Ruta | Método | Controlador::Acción | Propósito |
|---|---|---|---|
| `/contact` | GET | `ContactController::indexAction` | Mostrar formulario + mensajes flash |
| `/contact/process` | POST | `ContactController::processAction` | Validar, guardar, redirigir (PRG) |

---

## 📚 Documentación por pasos

### Paso 1 — Configuración del entorno

**Componentes instalados y verificados:**

- PHP 7.4.33 en `C:\php74`
- Extensiones `pdo_mysql`, `mbstring`, `curl`, `openssl`, `intl`, `mysqli` activas
- Composer 2.10.3 apuntando a PHP 7.4
- Proyecto base Laminas MVC Skeleton
- Módulos cargados: `Laminas\Form`, `Laminas\Db`, `Laminas\Validator`, `Laminas\View`, `Laminas\Session`, `Laminas\Di`, `Laminas\Mvc\Plugin\FlashMessenger`, `Laminas\Mvc\Plugin\Prg`, `Laminas\DeveloperTools` (dev)

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P1_01_php_version.png` | `php -v` → PHP 7.4.33 |
| `P1_02_extensiones_php.png` | `php -m` → extensiones críticas activas |
| `P1_03_composer_version.png` | `composer -V` con PHP 7.4 |
| `P1_04_laminas_welcome.png` | Página de bienvenida en `localhost:8080` |
| `P1_05_modules_config.png` | `config/modules.config.php` |

### Paso 2 — Modelo y base de datos

**Componentes creados:**

- Tabla `contacts` en MariaDB
- `Application\Model\Contact` (entidad)
- `Application\Model\ContactTable` (mapper con `TableGateway`)
- Registro de `ContactTable` en `service_manager.factories`

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P2_01_phpmyadmin_tabla_contacts.png` | Estructura de la tabla `contacts` |
| `P2_02_registro_insertado.png` | Fila insertada desde script |
| `P2_03_terminal_insert_ok.png` | Salida del script de prueba |
| `P2_04_global_php.png` | `config/autoload/global.php` |
| `P2_05_ContactTable_php.png` | Mapper `ContactTable.php` |
| `P2_06_module_config_service_manager.png` | Registro de servicios |

### Paso 3 — Formulario y validaciones

**Componentes creados:**

- `Application\Form\ContactForm` con 4 elementos (`nombre`, `email`, `mensaje`, `submit`)
- `getInputFilterSpecification()` con validadores `NotEmpty`, `StringLength`, `Regex`
- Registro de `ContactForm` en `service_manager.factories`

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P3_01_ContactForm_php.png` | Clase `ContactForm` con 4 campos |
| `P3_02_validaciones_form.png` | `getInputFilterSpecification()` con validadores |
| `P3_03_terminal_form_ok.png` | Prueba con datos válidos e inválidos |
| `P3_04_module_config_ContactForm.png` | Registro de `ContactForm` como servicio |

### Paso 4 — Controlador

**Componentes creados:**

- `Application\Controller\ContactController`
- `indexAction`: muestra formulario + mensajes flash
- `processAction`: valida, guarda con PRG, gestiona flash messages
- Rutas `/contact` y `/contact/process` en `module.config.php`
- Factory del controlador inyectando `ContactForm` y `ContactTable`

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P4_01_ContactController_php.png` | Clase + constructor + `indexAction` |
| `P4_02_ContactController_processAction.png` | Método `processAction` con PRG |
| `P4_03_rutas_controller.png` | Rutas y factories en `module.config.php` |
| `P4_04_terminal_controller_ok.png` | Verificación del controlador por CLI |

### Paso 5 — Vistas

**Componentes creados:**

- `module/Application/view/application/contact/index.phtml`
- Renderiza el formulario con Bootstrap
- Muestra alerts de éxito (verde) y error (rojo) desde `FlashMessenger`
- Formulario con `novalidate` para que Laminas gestione las validaciones

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P5_01_formulario_renderizado.png` | Formulario vacío en el navegador |
| `P5_02_envio_exitoso.png` | Alert verde tras envío válido |
| `P5_03_registro_guardado.png` | Fila guardada en phpMyAdmin |
| `P5_04_validacion_errores.png` | 3 alerts rojos con errores de Laminas |

### Paso 6 — Pruebas

**Escenarios verificados:**

1. **Envío válido:** guarda en BD y muestra alert verde
2. **Envío inválido:** rechaza con 3 alerts rojos (uno por campo)
3. **PRG:** pulsar F5 no duplica el registro (`COUNT(*) = 1` antes y después)
4. **Developer Tools:** barra de depuración activa al pie de la página

**Capturas:**

| Archivo | Contenido |
|---|---|
| `P6_01_toolbar_devtools.png` | Barra de Laminas Developer Tools |
| `P6_02_prg_sin_duplicados.png` | `COUNT(*) = 1` antes y después de F5 (PRG) |

---

## 🐞 Incidencias resueltas

Registro completo de problemas encontrados y sus soluciones durante el desarrollo del reto.

| # | Incidencia | Causa | Solución |
|---|---|---|---|
| 1 | `composer-plugin-api ^1.0` no compatible | Composer 2 vs. plugins de Zend F3 | Degradar temporalmente a Composer 1 |
| 2 | Packagist cerró Composer 1 (ago-2025) | Deprecación oficial | Volver a Composer 2 + migrar a Laminas |
| 3 | `Module (Laminas\Form) could not be initialized` | Faltaba `Laminas\Session` para FlashMessenger | Completar `modules.config.php` |
| 4 | `Laminas\I18n` en config sin estar instalado | Respondimos `n` a i18n | Eliminar de `modules.config.php` |
| 5 | `Laminas\ZendFrameworkBridge` en dev sin instalar | No instalado | Eliminar de `development.config.php.dist` |
| 6 | Composer pedía extensión `intl` | No habilitada en `php.ini` | Descomentar `extension=intl` |
| 7 | `Undefined array key "db"` | Caché de config sin la clave `db` | Borrar `data/cache/*` |
| 8 | `could not find driver` | PHP 7.4 leía `php.ini` de PHP 8.4 (WinGet) | Definir `PHPRC=C:\php74` |
| 9 | `Unable to load dynamic library 'php_pdo_mysql.dll'` | Línea obsoleta `extension=php_pdo_mysql.dll` | Dejar solo `extension=pdo_mysql` |
| 10 | `Unable to load dynamic library 'php_*.dll'` | `extension_dir` con ruta relativa `"ext"` | Cambiar a `"C:/php74/ext"` |
| 11 | Mezcla de PHP 7.4 y 8.4 | Variables de entorno contaminadas | Invocar `C:\php74\php.exe -c C:\php74\php.ini` |
| 12 | `SQLSTATE[HY000] [2002] Connection refused` | `localhost` resolvía a IPv6 sin servicio | Usar `127.0.0.1` en `global.php` |
| 13 | `SQLSTATE[HY000] [1049] Unknown database` | BD no existía en la instancia MariaDB de XAMPP | `CREATE DATABASE reto9_contact` |
| 14 | Instalador VC++ `0x80070666` | Ya había versión más reciente instalada | No es problema, ignorar |
| 15 | Mensaje del `Regex` interno de `EmailAddress` en inglés | API pública no permite sobrescribirlo | Sustituir `EmailAddress` por `Validator\Regex` |
| 16 | Validación HTML5 del navegador bloqueaba el POST | `type="email"` nativo | Añadir `novalidate` al `<form>` |
| 17 | `hasRoute` no existe en `PriorityList` | API del router de Laminas | Usar `$router->match()` con `HttpRequest` |

---

## 🧪 Cómo probar el proyecto

### 1. Arrancar MySQL

Abrir **XAMPP Control Panel** → **Start** junto a MySQL.

### 2. Arrancar el servidor PHP

```powershell
cd C:\Users\jdthg\Documents\cursoDesarrolloWebFrameworks4\Reto9_Contact_Form_Zend
C:\php74\php.exe -c C:\php74\php.ini -S 0.0.0.0:8080 -t public public/index.php
```

### 3. Enviar un mensaje válido

- Ir a `http://localhost:8080/contact`
- Rellenar:
  - **Nombre:** `Juan Pérez`
  - **Email:** `juan@example.com`
  - **Mensaje:** `Este es un mensaje de prueba con más de 10 caracteres.`
- Clic en **Enviar mensaje** → debe aparecer alert verde

### 4. Enviar un mensaje inválido

- **Nombre:** `A`
- **Email:** `no-es-email`
- **Mensaje:** `corto`
- Clic en **Enviar** → deben aparecer 3 alerts rojos

### 5. Verificar persistencia

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 -P 3306 reto9_contact -e "SELECT * FROM contacts;"
```

---

## 🧩 Tecnologías usadas

- **Laminas MVC** 3.x (`laminas/laminas-mvc`)
- **Laminas DB** (`laminas/laminas-db`) con `TableGateway`
- **Laminas Form** (`laminas/laminas-form`)
- **Laminas Validator** (`laminas/laminas-validator`)
- **Laminas View** (`laminas/laminas-view`)
- **Laminas Session** (`laminas/laminas-session`) para FlashMessenger
- **Laminas MVC Plugin FlashMessenger** y **Prg**
- **Laminas Developer Tools** (solo en development)
- **MariaDB** 10.4
- **PHP** 7.4.33
- **Composer** 2.10.3
- **Bootstrap 5** (vía CDN, solo para estilos del formulario)

---

## 👤 Autor

**everest9957**
Curso de Desarrollo Web con Frameworks 4
Octubre 2026
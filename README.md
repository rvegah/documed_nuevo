# DocuMed - Sistema de Gestión Documental

**DocuMed** es un CRM desarrollado en Laravel 11 para la gestión de documentación de clínicas dentales y centros médicos. Permite el registro, seguimiento y aprobación de documentos necesarios para la autorización sanitaria.

## Características Principales

- ✅ **CRUD de Empresas** con 5 estados: Tramitación → Presentada → Aprobada → Resuelta → Rechazada
- ✅ **Sistema de Documentos** con 35 tipos diferentes (básicos, profesionales, clínicos)
- ✅ **Múltiples Archivos** para "Contratos de Mantenimiento" (hasta 6 archivos)
- ✅ **Formulario Wizard** por pasos para crear empresas con Livewire
- ✅ **Panel de Administración** para aprobar/rechazar documentos
- ✅ **🆕 Configuración de Documentos Obligatorios** - Admin puede definir qué documentos son requeridos
- ✅ **🆕 Validación Dinámica** - Frontend y backend validan documentos obligatorios
- ✅ **🆕 Prevención de Envío** - No permite guardar sin documentos obligatorios
- ✅ **🆕 Progreso Visual** - Indicadores de documentos faltantes en tiempo real
- ✅ **Gestión de Personal** profesional y clínico
- ✅ **Sistema de Usuarios** con roles Admin/Usuario
- ✅ **Dashboard moderno** con estadísticas dinámicas
- ✅ **Descarga Individual** de documentos
- ✅ **Descarga Masiva en ZIP** con estructura organizada
- ✅ **Autorización completa** por usuario y empresa

## 🛠 Tecnologías Utilizadas

### Backend
- **PHP** 8.2+
- **Laravel** 11.x
- **MySQL** 8.0+
- **Laravel Breeze** (Autenticación)
- **Livewire** 3.x (Componentes reactivos)
- **ZipArchive** (Descarga masiva)

### Frontend
- **Bootstrap** 5.3
- **Font Awesome** 6.0
- **JavaScript Vanilla** (Validaciones dinámicas)
- **Blade Templates**

### Herramientas
- **Composer** (Gestión de dependencias PHP)
- **NPM/Node.js** (Gestión de assets)
- **Git** (Control de versiones)
- **XAMPP** (Desarrollo local)

## 🚀 Instalación y Configuración

### Prerequisitos

Asegúrate de tener instalado:

```bash
# Verificar versiones
php --version        # >= 8.2
composer --version   # >= 2.0
node --version       # >= 16.0
npm --version        # >= 8.0
mysql --version      # >= 8.0
```

### 1. Clonar el Repositorio

```bash
git clone https://github.com/rvegah/documed_nuevo.git
cd documed_nuevo
```

### 2. Instalar Dependencias PHP

```bash
composer install
```

### 3. Instalar Dependencias Frontend

```bash
npm install
```

### 4. Configurar Entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 5. Configurar Base de Datos

Edita el archivo `.env` con tus credenciales:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=documed
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Configuración de archivos
FILESYSTEM_DISK=public
```

### 6. Ejecutar Migraciones

```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE documed;"

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos de ejemplo)
php artisan db:seed
```

### 7. Crear Enlaces Simbólicos

```bash
php artisan storage:link
```

### 8. Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 9. Iniciar Servidor

```bash
# Servidor de desarrollo
php artisan serve

# La aplicación estará disponible en: http://localhost:8000
```

## Usuarios de Prueba

Después de ejecutar los seeders, tendrás estos usuarios:

| Email | Password | Rol |
|-------|----------|-----|
| admin@documed.com | password | Administrador |
| usuario@documed.com | password | Usuario |

## Estructura del Proyecto

```
documed_nuevo/
├── app/
│   ├── Http/Controllers/
│   │   ├── CompanyController.php           # CRUD Empresas + Descargas + Validaciones
│   │   ├── StaffController.php             # Gestión Personal
│   │   ├── DocumentApprovalController.php  # Panel Admin
│   │   └── DocumentConfigController.php    # 🆕 Configuración documentos obligatorios
│   ├── Models/
│   │   ├── Company.php                     # Modelo Empresa + Múltiples archivos
│   │   ├── Staff.php                       # Modelo Personal
│   │   ├── Document.php                    # Modelo Documento + Campo required
│   │   └── User.php                        # Modelo Usuario
│   ├── Livewire/
│   │   └── CompanyWizard.php               # 🆕 Wizard + Validación documentos obligatorios
│   ├── Http/Requests/
│   │   └── StoreCompanyRequest.php         # 🆕 Validaciones dinámicas documentos
│   └── Middleware/
│       └── AdminMiddleware.php             # Middleware admin
├── database/
│   ├── migrations/
│   │   ├── create_companies_table.php
│   │   ├── create_documents_table.php
│   │   ├── create_staff_table.php
│   │   ├── company_document_pivot.php
│   │   ├── staff_document_pivot.php
│   │   ├── add_file_index_to_company_document.php # ⭐ Múltiples archivos
│   │   └── add_required_field_to_documents.php    # 🆕 Campo required
│   └── seeders/                            # Datos de ejemplo
├── resources/
│   ├── views/
│   │   ├── companies/                      # 🆕 Vistas con validación documentos
│   │   ├── staff/                          # Vistas personal
│   │   ├── admin/
│   │   │   ├── document-approval/          # Panel administración
│   │   │   └── document-config/            # 🆕 Configuración documentos obligatorios
│   │   ├── livewire/
│   │   │   └── company-wizard.blade.php    # 🆕 Wizard con validaciones
│   │   └── layouts/
│   │       └── documed.blade.php           # 🆕 Layout con enlace configuración
│   └── js/                                 # Assets JavaScript
├── routes/
│   └── web.php                             # 🆕 Rutas + Admin config documentos
└── storage/
    └── app/
        ├── public/
        │   ├── company_documents/          # Documentos por empresa
        │   └── staff_documents/            # Documentos de personal
        └── temp/                           # ZIP temporales
```

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerar autoload
composer dump-autoload

# Crear migración
php artisan make:migration nombre_migracion

# Crear controlador
php artisan make:controller NombreController

# Crear modelo
php artisan make:model NombreModelo -m

# Crear componente Livewire
php artisan make:livewire ComponenteNombre

# Ver rutas
php artisan route:list

# Verificar almacenamiento
php artisan storage:link

# Modo mantenimiento
php artisan down
php artisan up
```

## Estados de Empresa

| Estado | Descripción | Acciones Disponibles |
|--------|-------------|---------------------|
| **Tramitación** | Empresa recién creada | Editar, Subir documentos obligatorios |
| **Presentada** | Documentos subidos para revisión | Solo lectura (usuario) |
| **Aprobada** | Documentos aprobados por admin | Completar información |
| **Resuelta** | Proceso completado exitosamente | Solo lectura |
| **Rechazada** | Documentos rechazados | Resubir documentos |

## 📄 Tipos de Documentos

### 🆕 Sistema de Documentos Obligatorios/Opcionales

El administrador puede configurar qué documentos son **obligatorios** u **opcionales** desde el panel de administración. Los usuarios solo pueden crear/editar empresas si han subido todos los documentos marcados como obligatorios.

### Documentos Básicos de Empresa (19)
1. Copia del DNI del Representante Legal 🔴 **(Obligatorio por defecto)**
2. Copia RC del Titular 🔴 **(Obligatorio por defecto)**
3. Copia del Último Pago de la RC del Titular 🔴 **(Obligatorio por defecto)**
4. Copia de la Compra Venta / Alquiler del Local 🔴 **(Obligatorio por defecto)**
5. Copia Licencia de Actividad (Ayuntamiento) 🔴 **(Obligatorio por defecto)**
6. Copia Memoria Técnica del Centro 🔴 **(Obligatorio por defecto)**
7. Plano de Situación 🔴 **(Obligatorio por defecto)**
8. Plano de Planta, Firmado 1/100 o 1/150 🔴 **(Obligatorio por defecto)**
9. Plano de Planta con Especificaciones 🔴 **(Obligatorio por defecto)**
10. **Contratos de Mantenimiento** ⭐ **(Múltiples archivos - hasta 6)** 🔴 **(Obligatorio por defecto)**
11. Alta Agencia Protección de Datos 🔴 **(Obligatorio por defecto)**
12. Contrato de Protección de Datos 🔴 **(Obligatorio por defecto)**
13. Copia Alta Productor Residuos Tipo III 🔴 **(Obligatorio por defecto)**
14. Copia Contrato Recogida de Residuos 🔴 **(Obligatorio por defecto)**
15. Alta Instalación de RX 🔴 **(Obligatorio por defecto)**
16. Contrato Protección Radiológica 🔴 **(Obligatorio por defecto)**
17. Programa de Garantía de Calidad 🔴 **(Obligatorio por defecto)**
18. Programa de Protección Radiológica 🔴 **(Obligatorio por defecto)**
19. Contrato de Dosimetría 🔴 **(Obligatorio por defecto)**

### Documentos Profesionales (10)
20. DNI Profesional 🔴 **(Obligatorio por defecto)**
21. Título General Profesional 🔴 **(Obligatorio por defecto)**
22. Títulos de Especialidades 🔴 **(Obligatorio por defecto)**
23. Póliza Responsabilidad Civil Profesional 🔴 **(Obligatorio por defecto)**
24. Comprobante Último Pago RC Profesional 🔴 **(Obligatorio por defecto)**
25. Certificado Colegiación Actual 🔴 **(Obligatorio por defecto)**
26. Certificado Delitos Sexuales Profesional 🔴 **(Obligatorio por defecto)**
27. Acuerdo de Colaboración 🔴 **(Obligatorio por defecto)**
28. Título RX Profesional 🔴 **(Obligatorio por defecto)**
29. Título RCP Profesional 🔴 **(Obligatorio por defecto)**

### Documentos Personal Clínico (6)
30. DNI Personal Clínico 🔴 **(Obligatorio por defecto)**
31. Título General Personal Clínico 🔴 **(Obligatorio por defecto)**
32. Otros Títulos Personal Clínico 🔴 **(Obligatorio por defecto)**
33. Contrato/ITA Personal Clínico 🔴 **(Obligatorio por defecto)**
34. Título RX Personal Clínico 🔴 **(Obligatorio por defecto)**
35. Título RCP Personal Clínico 🔴 **(Obligatorio por defecto)**

## Roles y Permisos

### Administrador
- ✅ Ver todas las empresas del sistema
- ✅ Aprobar/Rechazar documentos
- ✅ Acceso al panel de administración
- ✅ **🆕 Configurar documentos obligatorios/opcionales**
- ✅ **🆕 Cambiar configuración en tiempo real**
- ✅ Gestionar estados de empresas
- ✅ Descargar todos los documentos
- ✅ Estadísticas completas del sistema

### Usuario
- ✅ Crear/Editar solo sus empresas
- ✅ Subir documentos (únicos y múltiples)
- ✅ **🆕 Solo puede guardar con documentos obligatorios**
- ✅ **🆕 Ver progreso de documentos faltantes**
- ✅ Ver estado de sus solicitudes
- ✅ Descargar sus documentos
- ✅ Gestionar personal de sus empresas
- ❌ No puede aprobar documentos
- ❌ No ve empresas de otros usuarios
- ❌ **🆕 No puede configurar documentos obligatorios**

## 🌟 Funcionalidades Avanzadas

### 🆕 Sistema de Documentos Obligatorios
- **Panel de administración**: `/admin/document-config`
- **Configuración dinámica**: Admin puede cambiar obligatorio ↔ opcional
- **Validación frontend**: JavaScript previene envío sin documentos
- **Validación backend**: Laravel valida antes de guardar
- **Indicadores visuales**: 
  - 🔴 **Badges rojos** para obligatorios
  - ⚫ **Badges grises** para opcionales
  - 🟢 **Bordes verdes** para completados
  - 🔴 **Bordes rojos** para faltantes
- **Progreso en tiempo real**: Barra de progreso con documentos completados
- **Mensajes específicos**: "Faltan X documentos obligatorios: Lista específica"

### Sistema de Múltiples Archivos
- **Documento específico**: "Contratos de Mantenimiento" (ID: 45)
- **Límite**: Hasta 6 archivos por documento
- **Funciona en**: Wizard de creación ✅ y Vista de edición ✅
- **Organización**: `file_index` para diferenciar archivos
- **Interfaz**: Botones dinámicos para agregar/quitar archivos
- **🆕 Validación**: Si es obligatorio, requiere al menos 1 archivo

### Sistema de Descargas
- **Individual**: Descarga documento específico con nombre original
- **Masiva ZIP**: Estructura organizada:
  ```
  Documentos_EmpresaName_2025-08-09.zip
  ├── 1_Empresa/
  │   ├── documento1.pdf
  │   └── documento2.jpg
  ├── 2_Profesionales/
  │   └── NombreProfesional/
  │       ├── dni.pdf
  │       └── titulo.pdf
  └── 2_Personal_Clinico/
      └── NombrePersonal/
          ├── contrato.pdf
          └── titulo.pdf
  ```

### 🆕 Wizard de Creación con Validaciones (Livewire)
- **Paso 1**: Datos básicos de la empresa
- **Paso 2**: Subida de documentos con validación obligatorios
  - 🔍 **Progreso visual**: X/Y documentos obligatorios completados
  - 🚫 **Bloqueo**: No permite avanzar sin documentos obligatorios
  - 🎨 **Visual**: Bordes rojos para faltantes, verdes para completados
- **Paso 3**: Revisión final con validación secundaria
- **Características**: Progreso visual, validación por pasos, datos persistentes

### 🆕 Panel de Configuración de Documentos
- **Acceso**: Solo administradores
- **Funcionalidad**: 
  - 📋 **Lista todos los documentos** organizados por categoría
  - 🔄 **Switches en tiempo real** para cambiar obligatorio/opcional
  - 🎨 **Badges dinámicos** que actualizan al cambiar
  - 💾 **Guardado automático** via AJAX
  - 📡 **Notificaciones** de confirmación
- **Categorías**:
  - 🏢 **Documentos Básicos** (azul)
  - 👨‍⚕️ **Documentos Profesionales** (verde)
  - 👥 **Personal Clínico** (celeste)

### Panel de Administración
- **Dashboard**: Estadísticas en tiempo real
- **Gestión de documentos**: Aprobar/rechazar con comentarios
- **🆕 Configuración documentos**: Panel para administrar obligatorios
- **Filtros avanzados**: Por estado, fecha, tipo de documento
- **Historial**: Seguimiento de cambios y aprobaciones

## 🔗 Rutas Principales

### Públicas
```
/ → Dashboard (si auth) | Login (si guest)
/login → Iniciar sesión
/register → Registrarse
```

### Autenticadas
```
/dashboard → Vista principal con estadísticas
/companies → Listado de empresas (filtrado por usuario)
/companies/wizard → Registro paso a paso (Livewire + validaciones)
/companies/{id} → Ver empresa con documentos
/companies/{id}/edit → Editar empresa (con validación obligatorios)
/companies/{id}/staff → Gestión de personal
/companies/{id}/documents/{doc}/download → Descarga individual
/companies/{id}/documents/download-all → Descarga ZIP
```

### 🆕 Administrador
```
/admin/document-approval → Panel principal
/admin/document-approval/company/{id} → Revisar docs empresa
/admin/document-approval/staff/{id} → Revisar docs personal
/admin/document-config → 🆕 Configurar documentos obligatorios
```

### 🆕 API Interna (AJAX)
```
POST /admin/document-config/{document}/toggle → Cambiar obligatorio/opcional
```

## 🔧 Base de Datos

### Tablas Principales
- **users**: Usuarios del sistema
- **companies**: Empresas/clínicas
- **documents**: Catálogo de documentos + **🆕 campo `required`**
- **staff**: Personal profesional y clínico

### Tablas Pivot (con metadatos)
- **company_document**: Documentos de empresas
  - Clave primaria: `(company_id, document_id, file_index)`
  - Soporte para múltiples archivos con `file_index`
- **staff_document**: Documentos de personal

### 🆕 Cambios en Schema
```sql
-- Agregado a tabla documents
ALTER TABLE documents ADD COLUMN required BOOLEAN DEFAULT TRUE;
ALTER TABLE documents ADD COLUMN category VARCHAR(50) DEFAULT 'basic';
ALTER TABLE documents ADD COLUMN description TEXT;
```

## 🚨 Solución de Problemas

### Error de permisos de storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error de symlink
```bash
php artisan storage:link
```

### Error de memoria al instalar
```bash
composer install --no-dev --optimize-autoloader
```

### Error de base de datos
```bash
php artisan migrate:fresh --seed
```

### 🆕 Problemas con validación de documentos
```bash
# Verificar que los seeders hayan configurado el campo required
php artisan tinker
Document::where('required', true)->count(); // Debería ser > 0

# Si no hay documentos obligatorios configurados
php artisan db:seed --class=DocumentsSeeder
```

### Problemas con múltiples archivos
```bash
# Verificar estructura de tabla
php artisan tinker
Schema::hasColumn('company_document', 'file_index')

# Reejecutar migración específica
php artisan migrate --path=/database/migrations/add_file_index_to_company_document_table.php
```

### 🆕 JavaScript no funciona (validaciones)
```bash
# Limpiar cache de vistas
php artisan view:clear

# Verificar que el CSRF token esté presente
# En el navegador: document.querySelector('meta[name="csrf-token"]')
```

### Límites de subida PHP
```ini
# En php.ini
upload_max_filesize = 32M
post_max_size = 32M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

## 📊 Consideraciones de Rendimiento

- **Lazy Loading**: Relaciones cargadas bajo demanda
- **Índices**: Optimizados para consultas frecuentes
- **Archivos**: Organizados por carpetas de empresa
- **ZIP**: Generación asíncrona para archivos grandes
- **Caché**: Sistema de caché para consultas repetitivas
- **🆕 Validaciones**: Frontend previene peticiones innecesarias al servidor

## 🔒 Seguridad

- **Autorización**: Cada usuario solo ve sus empresas
- **Validación**: Tipos de archivo y tamaños controlados
- **🆕 Validación dual**: Frontend (UX) + Backend (seguridad)
- **Nombres seguros**: Archivos renombrados al subir
- **Rutas protegidas**: Middleware de autenticación y admin
- **Tokens CSRF**: Protección en formularios
- **🆕 AJAX seguro**: Todas las peticiones con CSRF token

## Contribución

1. Fork el proyecto
2. Crea una rama feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Añade nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Abre un Pull Request

## Convenciones de Código

- **PSR-12** para código PHP
- **Camel Case** para métodos y variables
- **Pascal Case** para clases
- **Snake Case** para base de datos y migraciones
- **Kebab Case** para rutas y vistas

## 📝 Changelog

### v3.0.0 - Sistema de Documentos Obligatorios (Agosto 2025)
- ✅ **🆕 Panel de configuración** de documentos obligatorios para admin
- ✅ **🆕 Validación frontend** en wizard y formularios de edición
- ✅ **🆕 Validación backend** en CompanyController y StoreCompanyRequest
- ✅ **🆕 Prevención de envío** sin documentos obligatorios
- ✅ **🆕 Indicadores visuales** de progreso y documentos faltantes
- ✅ **🆕 AJAX dinámico** para cambiar configuración en tiempo real
- ✅ **🆕 Notificaciones** de confirmación y errores
- ✅ **🆕 Campo `required`** en tabla documents
- ✅ **🆕 Mensajes específicos** de validación por documento

### v2.0.0 - Múltiples Archivos (Agosto 2025)
- ✅ Implementado sistema de múltiples archivos para "Contratos de Mantenimiento"
- ✅ Agregado `file_index` a tabla `company_document`
- ✅ Mejorado wizard de creación con soporte múltiple
- ✅ Sistema de descargas individual y masiva
- ✅ Panel de administración mejorado

### v1.0.0 - Versión Inicial
- ✅ CRUD básico de empresas
- ✅ Sistema de documentos únicos
- ✅ Autenticación y autorización
- ✅ Panel básico de administración

## 🎯 Próximas Funcionalidades

### v3.1.0 - Mejoras Planificadas
- [ ] **Notificaciones email** cuando se configuran documentos obligatorios
- [ ] **Historial de cambios** en configuración de documentos
- [ ] **Validaciones por rol** (diferentes documentos obligatorios según tipo empresa)
- [ ] **API REST** para integraciones externas
- [ ] **Dashboard mejorado** con estadísticas de documentos obligatorios

### v3.2.0 - Funcionalidades Avanzadas
- [ ] **Workflow de aprobación** por pasos
- [ ] **Comentarios en documentos** por parte de usuarios
- [ ] **Versionado de documentos** (historial de cambios)
- [ ] **Plantillas de documentos** descargables
- [ ] **Integración con firma digital**

## Reportar Bugs

Abre un issue en GitHub con:
- Descripción detallada del problema
- Pasos para reproducir
- Resultado esperado vs actual
- Screenshots si aplica
- Información del entorno (PHP, Laravel, navegador)
- Logs relevantes
- **🆕 Para bugs de validación**: Incluir configuración de documentos obligatorios

## Soporte y Contacto

- **Email**: rodrigovegaheredia@gmail.com
- **GitHub**: https://github.com/rvegah/documed_nuevo
- **Issues**: GitHub Issues
- **Documentación**: Este README + Comentarios en código

## 📄 Licencia

Este proyecto es privado y está bajo desarrollo para DocuMed 1804 S.L.

---

**🚀 DocuMed v3.0 - ¡Gestión documental inteligente con validación de documentos obligatorios!**

Para cualquier duda durante la instalación o uso, revisa este README o contacta al equipo de desarrollo.
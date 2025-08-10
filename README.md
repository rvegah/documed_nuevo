# DocuMed - Sistema de Gestión Documental

**DocuMed** es un CRM desarrollado en Laravel 11 para la gestión de documentación de clínicas dentales y centros médicos. Permite el registro, seguimiento y aprobación de documentos necesarios para la autorización sanitaria.

## Características Principales

- ✅ **CRUD de Empresas** con 5 estados: Tramitación → Presentada → Aprobada → Resuelta → Rechazada
- ✅ **Sistema de Documentos** con 35 tipos diferentes (básicos, profesionales, clínicos)
- ✅ **Múltiples Archivos** para "Contratos de Mantenimiento" (hasta 6 archivos)
- ✅ **Formulario Wizard** por pasos para crear empresas con Livewire
- ✅ **Panel de Administración** para aprobar/rechazar documentos
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
- **JavaScript Vanilla**
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
git clone https://github.com/tu-usuario/documed.git
cd documed
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
documed/
├── app/
│   ├── Http/Controllers/
│   │   ├── CompanyController.php      # CRUD Empresas + Descargas
│   │   ├── StaffController.php        # Gestión Personal
│   │   └── DocumentApprovalController.php # Panel Admin
│   ├── Models/
│   │   ├── Company.php                # Modelo Empresa + Múltiples archivos
│   │   ├── Staff.php                  # Modelo Personal
│   │   ├── Document.php               # Modelo Documento
│   │   └── User.php                   # Modelo Usuario
│   ├── Livewire/
│   │   └── CompanyWizard.php          # Wizard creación + Múltiples archivos
│   └── Middleware/
│       └── AdminMiddleware.php        # Middleware admin
├── database/
│   ├── migrations/
│   │   ├── create_companies_table.php
│   │   ├── create_documents_table.php
│   │   ├── create_staff_table.php
│   │   ├── company_document_pivot.php
│   │   ├── staff_document_pivot.php
│   │   └── add_file_index_to_company_document.php # ⭐ Múltiples archivos
│   └── seeders/                       # Datos de ejemplo
├── resources/
│   ├── views/
│   │   ├── companies/                 # Vistas empresas
│   │   ├── staff/                     # Vistas personal
│   │   ├── admin/                     # Panel administración
│   │   ├── livewire/
│   │   │   └── company-wizard.blade.php # Wizard paso a paso
│   │   └── layouts/
│   │       └── documed.blade.php      # Layout principal
│   └── js/                           # Assets JavaScript
├── routes/
│   └── web.php                       # Rutas web + Admin
└── storage/
    └── app/
        ├── public/
        │   ├── company_documents/     # Documentos por empresa
        │   └── staff_documents/       # Documentos de personal
        └── temp/                      # ZIP temporales
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
| **Tramitación** | Empresa recién creada | Editar, Subir documentos |
| **Presentada** | Documentos subidos para revisión | Solo lectura (usuario) |
| **Aprobada** | Documentos aprobados por admin | Completar información |
| **Resuelta** | Proceso completado exitosamente | Solo lectura |
| **Rechazada** | Documentos rechazados | Resubir documentos |

## 📄 Tipos de Documentos

### Documentos Básicos de Empresa (19)
1. Copia del DNI del Representante Legal
2. Copia RC del Titular
3. Copia del Último Pago de la RC del Titular
4. Copia de la Compra Venta / Alquiler del Local
5. Copia Licencia de Actividad (Ayuntamiento)
6. Copia Memoria Técnica del Centro
7. Plano de Situación
8. Plano de Planta, Firmado 1/100 o 1/150
9. Plano de Planta con Especificaciones
10. **Contratos de Mantenimiento** ⭐ **(Múltiples archivos - hasta 6)**
11. Alta Agencia Protección de Datos
12. Contrato de Protección de Datos
13. Copia Alta Productor Residuos Tipo III
14. Copia Contrato Recogida de Residuos
15. Alta Instalación de RX
16. Contrato Protección Radiológica
17. Programa de Garantía de Calidad
18. Programa de Protección Radiológica
19. Contrato de Dosimetría

### Documentos Profesionales (10)
20. DNI Profesional
21. Título General Profesional
22. Títulos de Especialidades
23. Póliza Responsabilidad Civil Profesional
24. Comprobante Último Pago RC Profesional
25. Certificado Colegiación Actual
26. Certificado Delitos Sexuales Profesional
27. Acuerdo de Colaboración
28. Título RX Profesional
29. Título RCP Profesional

### Documentos Personal Clínico (6)
30. DNI Personal Clínico
31. Título General Personal Clínico
32. Otros Títulos Personal Clínico
33. Contrato/ITA Personal Clínico
34. Título RX Personal Clínico
35. Título RCP Personal Clínico

## Roles y Permisos

### Administrador
- ✅ Ver todas las empresas del sistema
- ✅ Aprobar/Rechazar documentos
- ✅ Acceso al panel de administración
- ✅ Gestionar estados de empresas
- ✅ Descargar todos los documentos
- ✅ Estadísticas completas del sistema

### Usuario
- ✅ Crear/Editar solo sus empresas
- ✅ Subir documentos (únicos y múltiples)
- ✅ Ver estado de sus solicitudes
- ✅ Descargar sus documentos
- ✅ Gestionar personal de sus empresas
- ❌ No puede aprobar documentos
- ❌ No ve empresas de otros usuarios

## 🌟 Funcionalidades Avanzadas

### Sistema de Múltiples Archivos
- **Documento específico**: "Contratos de Mantenimiento" (ID: 45)
- **Límite**: Hasta 6 archivos por documento
- **Funciona en**: Wizard de creación ✅ y Vista de edición ✅
- **Organización**: `file_index` para diferenciar archivos
- **Interfaz**: Botones dinámicos para agregar/quitar archivos

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

### Wizard de Creación (Livewire)
- **Paso 1**: Datos básicos de la empresa
- **Paso 2**: Subida de documentos (con soporte múltiple)
- **Paso 3**: Revisión final antes de guardar
- **Características**: Progreso visual, validación por pasos, datos persistentes

### Panel de Administración
- **Dashboard**: Estadísticas en tiempo real
- **Gestión de documentos**: Aprobar/rechazar con comentarios
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
/companies/wizard → Registro paso a paso (Livewire)
/companies/{id} → Ver empresa con documentos
/companies/{id}/edit → Editar empresa
/companies/{id}/staff → Gestión de personal
/companies/{id}/documents/{doc}/download → Descarga individual
/companies/{id}/documents/download-all → Descarga ZIP
```

### Administrador
```
/admin/document-approval → Panel principal
/admin/document-approval/company/{id} → Revisar docs empresa
/admin/document-approval/staff/{id} → Revisar docs personal
```

## 🔧 Base de Datos

### Tablas Principales
- **users**: Usuarios del sistema
- **companies**: Empresas/clínicas
- **documents**: Catálogo de documentos
- **staff**: Personal profesional y clínico

### Tablas Pivot (con metadatos)
- **company_document**: Documentos de empresas
  - Clave primaria: `(company_id, document_id, file_index)`
  - Soporte para múltiples archivos con `file_index`
- **staff_document**: Documentos de personal

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

### Problemas con múltiples archivos
```bash
# Verificar estructura de tabla
php artisan tinker
Schema::hasColumn('company_document', 'file_index')

# Reejecutar migración específica
php artisan migrate --path=/database/migrations/add_file_index_to_company_document_table.php
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

## 🔒 Seguridad

- **Autorización**: Cada usuario solo ve sus empresas
- **Validación**: Tipos de archivo y tamaños controlados
- **Nombres seguros**: Archivos renombrados al subir
- **Rutas protegidas**: Middleware de autenticación
- **Tokens CSRF**: Protección en formularios

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

## Reportar Bugs

Abre un issue en GitHub con:
- Descripción detallada del problema
- Pasos para reproducir
- Resultado esperado vs actual
- Screenshots si aplica
- Información del entorno (PHP, Laravel, navegador)
- Logs relevantes

## Soporte y Contacto

- **Email**: rodrigovegaheredia@gmail.com
- **Issues**: GitHub Issues
- **Documentación**: Este README + Comentarios en código

## 📄 Licencia

Este proyecto es privado y está bajo desarrollo para DocuMed 1804 S.L.

---

**🚀 DocuMed v2.0 - ¡Gestión documental avanzada con múltiples archivos!**

Para cualquier duda durante la instalación o uso, revisa este README o contacta al equipo de desarrollo.
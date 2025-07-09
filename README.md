# DocuMed - Sistema de Gestión Documental

**ESTE README COMPLETO es el que debes copiar y pegar en tu archivo README.md**

**DocuMed** es un CRM desarrollado en Laravel para la gestión de documentación de clínicas dentales y centros médicos. Permite el registro, seguimiento y aprobación de documentos necesarios para la autorización sanitaria.

## Características Principales

- ✅ **CRUD de Empresas** con 3 estados: Tramitación → Presentada → Aprobada
- ✅ **Sistema de Documentos** con 16 tipos diferentes
- ✅ **Formulario Wizard** por pasos para crear empresas
- ✅ **Panel de Administración** para aprobar/rechazar documentos
- ✅ **Gestión de Personal** del centro y profesionales
- ✅ **Sistema de Usuarios** con roles Admin/Usuario
- ✅ **Dashboard moderno** con estadísticas
- ✅ **Exportación a PDF** de formularios

## 🛠 Tecnologías Utilizadas

### Backend
- **PHP** 8.1+
- **Laravel** 10.x
- **MySQL** 8.0+ / PostgreSQL 13+
- **Laravel Breeze** (Autenticación)
- **Livewire** 3.x (Componentes reactivos)

### Frontend
- **Bootstrap** 5.3
- **Font Awesome** 6.0
- **JavaScript Vanilla**
- **Blade Templates**

### Herramientas
- **Composer** (Gestión de dependencias PHP)
- **NPM/Node.js** (Gestión de assets)
- **Git** (Control de versiones)

## 🚀 Instalación y Configuración

### Prerequisitos

Asegúrate de tener instalado:

```bash
# Verificar versiones
php --version        # >= 8.1
composer --version   # >= 2.0
node --version       # >= 16.0
npm --version        # >= 8.0
mysql --version      # >= 8.0 (o PostgreSQL >= 13)
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
│   │   ├── CompanyController.php      # CRUD Empresas
│   │   ├── StaffController.php        # Gestión Personal
│   │   └── DocumentApprovalController.php # Panel Admin
│   ├── Models/
│   │   ├── Company.php                # Modelo Empresa
│   │   ├── Staff.php                  # Modelo Personal
│   │   ├── Document.php               # Modelo Documento
│   │   └── User.php                   # Modelo Usuario
│   └── Livewire/
│       └── CompanyWizard.php          # Wizard creación
├── database/
│   ├── migrations/                    # Migraciones DB
│   └── seeders/                       # Datos de ejemplo
├── resources/
│   ├── views/
│   │   ├── companies/                 # Vistas empresas
│   │   ├── staff/                     # Vistas personal
│   │   └── layouts/
│   │       └── documed.blade.php      # Layout principal
│   └── js/                           # Assets JavaScript
├── routes/
│   └── web.php                       # Rutas web
└── storage/
    └── app/public/
        └── company_documents/         # Documentos subidos
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

# Ver rutas
php artisan route:list

# Modo mantenimiento
php artisan down
php artisan up
```

## Estados de Empresa

| Estado | Descripción |
|--------|-------------|
| **Tramitación** | Empresa recién creada |
| **Presentada** | Documentos subidos para revisión |
| **Aprobada** | Documentos aprobados por admin |
| **Resuelta** | Proceso completado |
| **Rechazada** | Documentos rechazados |

## 📄 Tipos de Documentos

### Documentos Base (14)
1. DNI Representante Legal
2. RC del Titular
3. Último pago RC
4. Compra/Alquiler local
5. Licencia de Actividad
6. Memoria Técnica
7. Plano de Situación
8. Plano de Planta Firmado
9. Plano de Planta Indicativo
10. Contratos Mantenimiento
11. Alta Protección Datos
12. Contrato Protección Datos
13. Gestión Residuos Sanitarios
14. Protección Radiológica

### Documentos Personal (si aplica)
15. **Profesionales**: DNI, Títulos, RC, Colegiación, etc.
16. **Personal Clínico**: DNI, Títulos, Contratos, etc.

## Roles y Permisos

### Administrador
- ✅ Ver todas las empresas
- ✅ Aprobar/Rechazar documentos
- ✅ Gestionar usuarios
- ✅ Acceso al panel de aprobación

### Usuario
- ✅ Crear/Editar sus empresas
- ✅ Subir documentos
- ✅ Ver estado de sus solicitudes
- ❌ No puede aprobar documentos

## 🚨 Solución de Problemas

### Error de permisos de storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
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
- **Snake Case** para base de datos
- **Kebab Case** para rutas y vistas

## Reportar Bugs

Abre un issue en GitHub con:
- Descripción del problema
- Pasos para reproducir
- Resultado esperado vs actual
- Screenshots si aplica
- Información del entorno

## Soporte

- **Email**: rodrigovegaheredia@gmail.com
- **Issues**: GitHub Issues
- **Documentación**: Este README

## 📄 Licencia

Este proyecto es privado y está bajo desarrollo para DocuMed 1804 S.L.

---

**¡Listo para empezar!**

Para cualquier duda durante la instalación, revisa este README o contacta al equipo de desarrollo.
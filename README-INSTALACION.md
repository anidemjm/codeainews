# 🚀 Instalación de CodeaiNews

## 📋 Requisitos Previos

- **PHP 7.4 o superior** con las siguientes extensiones:
  - PDO
  - PDO_SQLite
  - SQLite3
- **Servidor web** (Apache, Nginx, o servidor integrado de PHP)
- **Permisos de escritura** en la carpeta del proyecto

## 🔧 Instalación Paso a Paso

### 1. Preparar el Entorno

```bash
# Clonar o descargar el proyecto
cd codeainews_1

# Verificar que PHP esté disponible
php --version

# Verificar extensiones PHP
php -m | grep -E "(pdo|sqlite)"
```

### 2. Configurar la Base de Datos

```bash
# Crear la carpeta config si no existe
mkdir -p config

# Verificar que config/database.php existe
ls -la config/
```

### 3. Ejecutar la Instalación

```bash
# Ejecutar el script de instalación
php install.php
```

**Este script hará lo siguiente:**
- ✅ Crear la base de datos SQLite
- ✅ Crear todas las tablas necesarias
- ✅ Insertar categorías y etiquetas iniciales
- ✅ Insertar posts de ejemplo del blog
- ✅ Insertar banners de ejemplo
- ✅ Configurar información de contacto
- ✅ Crear elementos del footer
- ✅ Crear usuario administrador

### 4. Credenciales de Acceso

Después de la instalación, tendrás acceso con:
- **Usuario:** `admin1`
- **Contraseña:** `admin1`

**⚠️ IMPORTANTE:** Cambia la contraseña después del primer login por seguridad.

## 🌐 Acceso al Sistema

### Panel de Administración
- **URL:** `dashboard.php`
- **Login:** `login.php`

### Páginas Públicas
- **Inicio:** `index.html`
- **Blog:** `blog.html`
- **Contacto:** `contacto.html`
- **Entradas del blog:** `entrada-blog.html`

## 🗄️ Estructura de la Base de Datos

El sistema incluye las siguientes tablas:

| Tabla | Descripción |
|-------|-------------|
| `usuarios` | Usuarios del sistema |
| `categorias` | Categorías del sitio |
| `blog_posts` | Entradas del blog |
| `etiquetas` | Etiquetas para SEO |
| `blog_posts_etiquetas` | Relación posts-etiquetas |
| `noticias` | Noticias de la página principal |
| `banners` | Banners rotativos |
| `footer_items` | Elementos del pie de página |
| `contactos` | Mensajes del formulario de contacto |
| `info_contacto` | Información de contacto de la empresa |
| `configuracion` | Configuraciones del sitio |
| `logs` | Registro de actividades |

## 🔐 Seguridad

### Después de la Instalación

1. **Eliminar archivos de instalación:**
   ```bash
   rm install.php
   rm setup-admin.php
   rm migrate.php
   rm actualizar-opinion-afiliados.php
   rm limpiar-practicos.php
   ```

2. **Cambiar contraseña del administrador**
3. **Configurar HTTPS** en producción
4. **Revisar permisos de archivos**

### Permisos Recomendados

```bash
# Archivos PHP
chmod 644 *.php
chmod 644 config/*.php

# Base de datos
chmod 664 *.db
chmod 664 config/*.db

# Carpetas
chmod 755 config/
```

## 🚨 Solución de Problemas

### Error: "No se encontró config/database.php"
- Verifica que existe la carpeta `config/`
- Verifica que `database.php` esté en esa carpeta

### Error: "PDO_SQLite no disponible"
- Instala la extensión SQLite para PHP
- En Ubuntu/Debian: `sudo apt-get install php-sqlite3`
- En CentOS/RHEL: `sudo yum install php-sqlite3`

### Error: "Permiso denegado"
- Verifica permisos de escritura en la carpeta del proyecto
- Ejecuta: `chmod -R 755 .`

### Base de datos corrupta
- Elimina el archivo `*.db`
- Ejecuta `php install.php` nuevamente

## 📱 Características del Sistema

### ✅ Implementado
- Panel de administración completo
- Gestión de noticias, banners, categorías
- Sistema de blog con SEO
- Formulario de contacto con CAPTCHA
- Gestión de mensajes de contacto
- Sistema de usuarios y autenticación
- Base de datos SQLite robusta
- Interfaz responsive y moderna

### 🔄 Funcionalidades CRUD
- **Create:** Añadir nuevo contenido
- **Read:** Ver y listar contenido
- **Update:** Editar contenido existente
- **Delete:** Eliminar contenido

### 🎨 Personalización
- Colores de categorías personalizables
- Imágenes destacadas para posts
- Etiquetas SEO configurables
- Banners con duración ajustable

## 📞 Soporte

Si encuentras problemas durante la instalación:

1. Verifica los requisitos previos
2. Revisa los logs de error del servidor
3. Ejecuta `php install.php` con `-v` para más detalles
4. Verifica permisos de archivos y carpetas

## 🎯 Próximos Pasos

Después de la instalación exitosa:

1. **Personalizar contenido** en el dashboard
2. **Configurar categorías** según tus necesidades
3. **Añadir posts del blog** con contenido real
4. **Configurar banners** promocionales
5. **Personalizar información de contacto**
6. **Probar todas las funcionalidades**

---

**¡Disfruta usando CodeaiNews! 🎉**





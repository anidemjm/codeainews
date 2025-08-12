# 🚀 Guía de Despliegue - CodeaiNews

## 📋 Requisitos Previos

- Cuenta en un servicio de hosting gratuito (recomendamos 000webhost)
- Editor de texto (Notepad++, Visual Studio Code, etc.)
- Navegador web

## 🌐 Opciones de Hosting Gratuito

### 1. **000webhost (RECOMENDADO)**
- **URL**: https://www.000webhost.com/
- **Ventajas**: 
  - Completamente gratuito
  - Soporta PHP 8.0+
  - Base de datos MySQL incluida
  - SSL gratuito
  - Panel de control intuitivo
- **Limitaciones**: 
  - 1GB de almacenamiento
  - 10GB de transferencia mensual
  - Subdominio: `tu-sitio.000webhostapp.com`

### 2. **InfinityFree**
- **URL**: https://infinityfree.net/
- **Ventajas**: Ilimitado en almacenamiento y transferencia
- **Limitaciones**: Puede ser lento, publicidad en el panel

## 📁 Preparación de Archivos

### Estructura de archivos a subir:
```
tu-sitio/
├── index.php
├── blog.php
├── entrada-blog.php
├── contacto.php
├── login.php
├── dashboard.php
├── logout.php
├── install-hosting.php
├── config/
│   ├── hosting.php
│   └── database.php
├── api/
│   ├── noticias.php
│   ├── blog.php
│   ├── carrusel.php
│   ├── banners.php
│   ├── categorias.php
│   ├── contacto.php
│   ├── contact-info.php
│   └── footer.php
├── styles.css
├── .htaccess
└── README-INSTALACION.md
```

## 🚀 Pasos para Desplegar en 000webhost

### **Paso 1: Crear cuenta**
1. Ve a https://www.000webhost.com/
2. Haz clic en "Create Website"
3. Regístrate con tu email
4. Verifica tu cuenta

### **Paso 2: Crear sitio web**
1. En el panel, haz clic en "Create Website"
2. Elige un nombre para tu sitio (ej: `codeainews`)
3. Selecciona PHP 8.0 o superior
4. Haz clic en "Create"

### **Paso 3: Acceder al panel de control**
1. En tu sitio, haz clic en "Manage"
2. Ve a "File Manager"
3. Haz clic en "Go to File Manager"

### **Paso 4: Subir archivos**
1. En el File Manager, navega a `public_html`
2. Sube todos los archivos de tu proyecto
3. **IMPORTANTE**: Asegúrate de que `config/hosting.php` esté presente

### **Paso 5: Configurar base de datos**
1. En el panel principal, ve a "Databases"
2. Crea una nueva base de datos MySQL
3. Anota: nombre de BD, usuario, contraseña y host
4. Modifica `config/hosting.php` con estos datos

### **Paso 6: Ejecutar instalación**
1. Ve a tu sitio: `https://tu-sitio.000webhostapp.com/install-hosting.php`
2. Verifica que aparezcan todos los ✅
3. **IMPORTANTE**: Elimina `install-hosting.php` después

### **Paso 7: Probar el sitio**
1. Ve a tu sitio principal
2. Prueba el login: `admin1` / `admin1`
3. Verifica que el panel funcione

## ⚙️ Configuración de `config/hosting.php`

Modifica este archivo con tus datos de hosting:

```php
// Ejemplo para 000webhost:
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_nombre_db');     // El nombre que diste a tu BD
define('DB_USER', 'tu_usuario_db');    // Usuario de la BD
define('DB_PASS', 'tu_password_db');   // Contraseña de la BD
define('SITE_URL', 'https://tu-sitio.000webhostapp.com');
define('ADMIN_EMAIL', 'tu@email.com');
```

## 🔒 Seguridad Post-Despliegue

### **Archivos a eliminar después de la instalación:**
- `install-hosting.php`
- `install.php` (si existe)
- `setup-admin.php` (si existe)
- `actualizar-opinion-afiliados.php` (si existe)

### **Cambiar credenciales por defecto:**
1. Accede al panel con `admin1`/`admin1`
2. Ve a la sección de usuarios
3. Cambia la contraseña del administrador

## 🐛 Solución de Problemas

### **Error: "Could not connect to database"**
- Verifica los datos en `config/hosting.php`
- Asegúrate de que la base de datos esté creada
- Verifica que el usuario tenga permisos

### **Error: "Table doesn't exist"**
- Ejecuta `install-hosting.php` nuevamente
- Verifica que todas las tablas se crearon

### **Error: "Permission denied"**
- Verifica que los archivos tengan permisos 644
- Las carpetas deben tener permisos 755

### **Error: "Page not found"**
- Verifica que `.htaccess` esté presente
- Asegúrate de que mod_rewrite esté habilitado

## 📱 Personalización Post-Despliegue

### **Cambiar imágenes del carrusel:**
1. Sube tus imágenes a la carpeta `uploads/`
2. En el panel, ve a "Carrusel"
3. Edita cada elemento y cambia la URL de la imagen

### **Agregar contenido del blog:**
1. En el panel, ve a "Blog"
2. Haz clic en "Nueva Entrada"
3. Completa título, contenido, imagen y etiquetas

### **Modificar información de contacto:**
1. En el panel, ve a "Contacto"
2. Edita la información de contacto
3. Los cambios se reflejan automáticamente

## 🌍 Dominio Personalizado (Opcional)

### **Para usar tu propio dominio:**
1. Compra un dominio (GoDaddy, Namecheap, etc.)
2. En 000webhost, ve a "Domains"
3. Agrega tu dominio personalizado
4. Configura los DNS según las instrucciones

## 📊 Monitoreo y Mantenimiento

### **Revisar regularmente:**
- Mensajes de contacto en el panel
- Estadísticas de visitas (si las habilitas)
- Logs de errores en el panel de hosting
- Actualizaciones de seguridad

### **Backup recomendado:**
- Exporta tu base de datos regularmente
- Descarga archivos importantes
- Guarda copias de `config/hosting.php`

## 🎯 Próximos Pasos

### **Mejoras recomendadas:**
1. **Google Analytics**: Para estadísticas de visitas
2. **Google Search Console**: Para SEO
3. **CDN**: Para mejorar velocidad de carga
4. **Backup automático**: Para proteger tu contenido
5. **SSL personalizado**: Para mayor seguridad

### **Monetización:**
1. **Google AdSense**: Ya tienes espacios preparados
2. **Afiliados**: Usa la sección "Afiliados"
3. **Contenido premium**: Artículos exclusivos

## 📞 Soporte

### **Si tienes problemas:**
1. Revisa esta guía paso a paso
2. Verifica los logs de error en tu hosting
3. Consulta la documentación de tu proveedor de hosting
4. Revisa que todos los archivos estén subidos correctamente

---

**¡Felicitaciones! 🎉 Tu sitio CodeaiNews está listo para el mundo.**

**Credenciales por defecto:**
- **Usuario**: `admin1`
- **Contraseña**: `admin1`
- **URL del panel**: `https://tu-sitio.000webhostapp.com/dashboard.php`

**Recuerda cambiar la contraseña después del primer login por seguridad.**



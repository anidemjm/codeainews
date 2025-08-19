# 🚀 Guía de Despliegue en Heroku - CodeaiNews

## 🌟 **¿Por qué Heroku?**

Heroku es una plataforma de **nivel empresarial** que ofrece:
- ✅ **Despliegue automático** desde GitHub
- ✅ **Base de datos PostgreSQL** incluida
- ✅ **SSL gratuito** automático
- ✅ **Escalabilidad** profesional
- ✅ **Monitoreo** avanzado
- ✅ **Logs** en tiempo real
- ✅ **Backup automático** de base de datos

## 📋 **Requisitos Previos**

- Cuenta en [GitHub](https://github.com) (gratuita)
- Cuenta en [Heroku](https://heroku.com) (gratuita con verificación)
- [Git](https://git-scm.com) instalado en tu computadora
- [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) instalado

## 🚀 **Paso 1: Preparar el Repositorio GitHub**

### **1.1 Crear repositorio en GitHub:**
1. Ve a [github.com](https://github.com)
2. Haz clic en "New repository"
3. Nombre: `codeainews`
4. Descripción: "Sitio web de noticias tecnológicas"
5. Público o privado (tu elección)
6. Haz clic en "Create repository"

### **1.2 Subir código a GitHub:**
```bash
# En tu carpeta del proyecto
git init
git add .
git commit -m "Primera versión de CodeaiNews"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/codeainews.git
git push -u origin main
```

## 🎯 **Paso 2: Crear Aplicación en Heroku**

### **2.1 Crear cuenta en Heroku:**
1. Ve a [heroku.com](https://heroku.com)
2. Haz clic en "Sign up"
3. Conecta tu cuenta de GitHub
4. **IMPORTANTE**: Verifica tu cuenta con tarjeta de crédito (no cobra)

### **2.2 Crear nueva aplicación:**
1. En el dashboard de Heroku, haz clic en "New" → "Create new app"
2. **App name**: `codeainews` (o el nombre que prefieras)
3. **Region**: United States (o la más cercana)
4. Haz clic en "Create app"

### **2.3 Conectar con GitHub:**
1. En tu app, ve a "Deploy" tab
2. En "Deployment method", selecciona "GitHub"
3. Conecta tu repositorio `codeainews`
4. Selecciona la rama `main`

## 🗄️ **Paso 3: Configurar Base de Datos PostgreSQL**

### **3.1 Agregar PostgreSQL:**
1. En tu app, ve a "Resources" tab
2. Haz clic en "Find more add-ons"
3. Busca "Heroku Postgres"
4. Selecciona el plan **Hobby Dev** (gratuito)
5. Haz clic en "Submit Order Form"

### **3.2 Configurar variables de entorno:**
1. Ve a "Settings" tab
2. Haz clic en "Reveal Config Vars"
3. Agrega estas variables:

```
HEROKU_APP_NAME=codeainews
SITE_URL=https://tu-app.herokuapp.com
ADMIN_EMAIL=tu@email.com
ENABLE_DEBUG=false
LOG_LEVEL=INFO
```

## 🔧 **Paso 4: Configurar Buildpacks**

### **4.1 Agregar buildpack de PHP:**
1. En "Settings" tab, haz clic en "Buildpacks"
2. Haz clic en "Add buildpack"
3. Selecciona `heroku/php`
4. Haz clic en "Save changes"

## 📁 **Paso 5: Preparar Archivos para Heroku**

### **5.1 Renombrar archivos:**
```bash
# Renombrar .htaccess para Heroku
mv .htaccess .htaccess-heroku
cp .htaccess-heroku .htaccess
```

### **5.2 Verificar archivos críticos:**
- ✅ `Procfile` (ya creado)
- ✅ `composer.json` (ya creado)
- ✅ `config/heroku.php` (ya creado)
- ✅ `config/database-heroku.php` (ya creado)
- ✅ `.htaccess` (renombrado)

### **5.3 Commit y push:**
```bash
git add .
git commit -m "Configuración para Heroku"
git push origin main
```

## 🚀 **Paso 6: Desplegar en Heroku**

### **6.1 Despliegue automático:**
1. En Heroku, ve a "Deploy" tab
2. En "Automatic deploys", activa "Enable automatic deploys"
3. Haz clic en "Deploy Branch"
4. Espera a que termine el build

### **6.2 Verificar despliegue:**
1. Cuando termine, haz clic en "View"
2. Deberías ver tu sitio funcionando
3. Si hay errores, revisa los logs

## ⚙️ **Paso 7: Configurar Base de Datos**

### **7.1 Ejecutar script de instalación:**
1. Ve a tu sitio: `https://tu-app.herokuapp.com/install-heroku.php`
2. Verifica que aparezcan todos los ✅
3. **IMPORTANTE**: Elimina `install-heroku.php` después

### **7.2 Verificar base de datos:**
1. Ve a tu sitio principal
2. Prueba el login: `admin1` / `admin1`
3. Verifica que el panel funcione

## 🔒 **Paso 8: Seguridad Post-Despliegue**

### **8.1 Archivos a eliminar:**
- `install-heroku.php`
- `install-hosting.php` (si existe)
- `verificar-hosting.php` (si existe)

### **8.2 Cambiar credenciales:**
1. Accede al panel con `admin1`/`admin1`
2. Ve a la sección de usuarios
3. Cambia la contraseña del administrador

## 📊 **Paso 9: Monitoreo y Mantenimiento**

### **9.1 Ver logs en tiempo real:**
```bash
heroku logs --tail --app codeainews
```

### **9.2 Acceder a la consola:**
```bash
heroku run bash --app codeainews
```

### **9.3 Verificar estado:**
```bash
heroku ps --app codeainews
```

## 🐛 **Solución de Problemas Comunes**

### **Error: "Build failed"**
- Verifica que `composer.json` esté correcto
- Asegúrate de que `Procfile` esté presente
- Revisa que no haya errores de sintaxis en PHP

### **Error: "Database connection failed"**
- Verifica que PostgreSQL esté agregado
- Revisa las variables de entorno
- Ejecuta `heroku config` para ver la configuración

### **Error: "Page not found"**
- Verifica que `.htaccess` esté presente
- Asegúrate de que `mod_rewrite` esté habilitado
- Revisa los logs de Heroku

### **Error: "Permission denied"**
- Verifica que los archivos tengan permisos correctos
- Asegúrate de que `composer.json` esté en la raíz

## 🌍 **Paso 10: Dominio Personalizado (Opcional)**

### **10.1 Agregar dominio:**
1. En "Settings" tab, haz clic en "Domains"
2. Agrega tu dominio personalizado
3. Configura los DNS según las instrucciones

### **10.2 SSL automático:**
- Heroku proporciona SSL gratuito automáticamente
- Se renueva automáticamente

## 📈 **Paso 11: Optimizaciones Avanzadas**

### **11.1 Agregar Redis para caché:**
1. En "Resources", agrega "Heroku Redis"
2. Configura las variables de entorno
3. El sistema detectará automáticamente Redis

### **11.2 Configurar monitoreo:**
1. Agrega "Heroku Add-ons" para métricas
2. Configura alertas automáticas
3. Revisa el dashboard de rendimiento

## 🎯 **Ventajas de Heroku vs Hosting Gratuito**

| Característica | Heroku | Hosting Gratuito |
|----------------|---------|------------------|
| **Base de datos** | PostgreSQL profesional | MySQL limitado |
| **SSL** | Automático y renovable | Manual y limitado |
| **Escalabilidad** | Ilimitada | Muy limitada |
| **Monitoreo** | Avanzado | Básico |
| **Logs** | Tiempo real | Limitados |
| **Backup** | Automático | Manual |
| **Soporte** | 24/7 | Comunitario |
| **Uptime** | 99.9%+ | Variable |

## 📞 **Comandos Útiles de Heroku CLI**

```bash
# Ver todas las apps
heroku apps

# Ver logs en tiempo real
heroku logs --tail --app codeainews

# Ejecutar comando en la app
heroku run php --app codeainews

# Ver variables de entorno
heroku config --app codeainews

# Abrir la app en el navegador
heroku open --app codeainews

# Ver estado de la app
heroku ps --app codeainews

# Reiniciar la app
heroku restart --app codeainews
```

## 🔄 **Actualizaciones Automáticas**

### **Configurar GitHub Actions (Opcional):**
1. Crea `.github/workflows/deploy.yml`
2. Configura despliegue automático en cada push
3. Heroku se actualizará automáticamente

## 💰 **Costos y Planes**

### **Plan Gratuito (Hobby):**
- ✅ **Aplicación web**: Gratis
- ✅ **Base de datos**: 10,000 filas
- ✅ **SSL**: Gratis
- ✅ **Dominio**: `tu-app.herokuapp.com`
- ⚠️ **Sleep mode**: Después de 30 min de inactividad

### **Plan Pagado (Basic):**
- 💰 **$7/mes** por aplicación
- ✅ **Sin sleep mode**
- ✅ **Base de datos**: 10,000 filas
- ✅ **Soporte**: Email

## 🎉 **¡Felicitaciones!**

Tu sitio CodeaiNews está ahora desplegado en **Heroku**, una plataforma de nivel empresarial.

### **URLs importantes:**
- **Sitio principal**: `https://tu-app.herokuapp.com`
- **Panel de control**: `https://tu-app.herokuapp.com/dashboard.php`
- **Login**: `https://tu-app.herokuapp.com/login.php`

### **Credenciales por defecto:**
- **Usuario**: `admin1`
- **Contraseña**: `admin1`

### **Próximos pasos recomendados:**
1. **Cambiar contraseña** del administrador
2. **Configurar dominio** personalizado
3. **Agregar Google Analytics**
4. **Configurar backup** automático
5. **Monitorear logs** regularmente

---

**¿Necesitas ayuda con algún paso específico?** ¡Estoy aquí para ayudarte con el despliegue en Heroku!





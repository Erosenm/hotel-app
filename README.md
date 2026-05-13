
# 🏨 Hotel-App Project

Repositorio principal del proyecto hotel-app. Todo el código y actualizaciones deben manejarse desde GitHub para mantener el proyecto sincronizado y evitar versiones desactualizadas.

---

## 📩 Permisos de colaborador

Si GitHub muestra errores como `Permission denied` o no permite hacer `push`:

1. Pasarle tu correo de GitHub a **Ekrom**
2. Aceptar la invitación de colaborador desde GitHub o correo
3. Volver a intentar el `push`

---

## 📥 Clonar el proyecto (solo la primera vez)

Abrir Git Bash dentro de:

```bash
C:/xampp/htdocs/
````

Ejecutar:

```bash
git clone https://github.com/Erosenm/hotel-app.git
cd hotel-app
```

---

## 🔄 Flujo de trabajo

### 1. Actualizar el proyecto antes de trabajar

```bash
git pull origin main
```

---

### 2. Guardar cambios

```bash
git add .
git commit -m "Descripción clara del cambio"
```

Ejemplos:

```bash
git commit -m "Fix: corregido login"
git commit -m "Feat: agregado módulo de reservas"
```

---

### 3. Subir cambios

```bash
git push origin main
```

---

## 🗄️ Base de datos

Los archivos SQL estarán dentro de:

```bash
/database
```

Si alguien actualiza la base de datos:

1. Ejecutar `git pull origin main`
2. Importar nuevamente el `.sql` en PHPMyAdmin

---

## ⚠️ Recomendaciones

* No enviar archivos `.zip`
* Hacer `git pull` antes de programar
* Usar mensajes claros en los commits
* Si aparece conflicto al hacer `push`, ejecutar:

```bash
git pull origin main
```

y luego volver a hacer:

```bash
git push origin main
```

---

## 🚀 Comandos rápidos

### Actualizar proyecto

```bash
git pull origin main
```

### Guardar cambios

```bash
git add .
git commit -m "Descripción del cambio"
```

### Subir cambios

```bash
git push origin main
```

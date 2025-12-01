Perfecto, aquí tienes un README ampliado y más completo, integrando estadísticas, perfiles, estado del juego y funcionalidades en desarrollo:

---

# Hundir la Flota 🎯⚓

**Hundir la Flota** es un juego web basado en el clásico juego de batalla naval, donde los jugadores colocan sus barcos y tratan de hundir los de sus oponentes. Este proyecto se encuentra actualmente en **desarrollo**, pero ya permite iniciar partidas, jugar contra enemigos y visualizar estadísticas básicas. El objetivo es ofrecer una experiencia interactiva y visualmente atractiva directamente desde el navegador.

---

## 🛠 Tecnologías utilizadas

El proyecto está desarrollado con tecnologías web modernas y convencionales:

- **HTML5** – Estructura de las páginas y contenido semántico.
- **CSS3** – Estilos visuales, animaciones y responsive design.
- **JavaScript (ES6)** – Interactividad, validaciones de formularios, manejo dinámico de contenido y modales.
- **PHP** – Lógica del servidor, sesiones, gestión de usuarios y conexión a la base de datos.
- **MySQL / MariaDB** – Base de datos para usuarios, partidas, estadísticas y almirantes.
- **AJAX / Fetch API** – Interacciones dinámicas sin recargar la página.
- **Google Fonts** – Tipografía personalizada (`Russo One`) para el estilo naval del juego.

---

## 📂 Estructura del proyecto

```
Hundir-la-flota/
├─ assets/
│  ├─ css/        # Archivos de estilos
│  ├─ js/         # Scripts JS para interactividad y lógica del juego
│  └─ img/        # Imágenes de barcos, almirantes, perfiles y fondos
├─ php/           # Backend: conexión a DB, manejo de partidas, usuarios, estadísticas
├─ juego/         # Archivos del juego: HTML, JS y assets específicos
├─ index.php      # Página de login / registro
├─ menuJuego.php  # Menú principal del juego
└─ README.md      # Documentación del proyecto
```

---

## ⚙ Funcionalidades actuales

- **Registro e inicio de sesión** de usuarios.
- **Perfiles de usuario** con información personal, foto de perfil y fecha de registro.
- **Estadísticas de partidas**: victorias del usuario y de los almirantes, historial contra cada almirante.
- **Menú principal** que permite:

  - Iniciar partida nueva.
  - Continuar partidas guardadas.
  - Consultar ranking de jugadores (en desarrollo).
  - Ajustes y configuración del usuario.

- **Música de fondo** persistente mediante `iframe`.
- **Mensajes dinámicos y notificaciones** dentro del juego.
- **Validación básica de formularios** y seguridad mínima en el backend.
- **Selección aleatoria de almirantes enemigos** con su imagen y nombre legible.
- **Control de partidas**: las partidas finalizadas no aparecen en la lista de “cargar partida”.

---

## 🏆 Control de estadísticas y perfiles

- Cada usuario tiene un **contador de victorias totales**.
- Se registran las victorias de los **almirantes enemigos** frente a cada usuario.
- Se puede consultar el historial de partidas con cada almirante, mostrando:

  - Número de victorias del usuario.
  - Número de victorias del almirante.

- Las estadísticas se muestran en **perfil.php** con gráficos simples o listados de almirantes.

---

## 🚀 Estado del juego

- **Ya funcional**: se puede colocar barcos, disparar, finalizar partidas y ver resultados.
- **En desarrollo**:

  - Ranking global de jugadores.
  - Mejoras en la interfaz y animaciones del tablero.
  - Mensajes de victoria/derrota con animaciones.
  - Implementación de niveles o dificultad de almirantes.
  - Guardado y carga de partidas más avanzado.
  - Sonidos y efectos adicionales.

---

## 🔧 Instalación local

1. Clonar este repositorio en un servidor local (XAMPP, WAMP o similar).
2. Crear la base de datos MySQL y ejecutar los scripts de creación de tablas (`usuarios`, `partidas`, `almirantes`, etc.).
3. Configurar los datos de conexión en `php/conexion.php`.
4. Abrir `http://localhost/Hundir-la-flota/index.php` en tu navegador.
5. Crear un usuario o iniciar sesión con una cuenta existente.

---

## ⚠ Estado del proyecto

Este proyecto **está en desarrollo**. Algunas funcionalidades pueden no estar completas y el diseño podría cambiar.
Se prioriza la jugabilidad y el control de estadísticas antes de pulir la interfaz final.

---

## 📜 Licencia

Este proyecto es **personal / educativo** y no cuenta con licencia específica. Se permite su uso para fines de aprendizaje o demostración.

---

💡 **Nota**: Se recomienda jugar en escritorio para mejor experiencia, aunque el diseño es parcialmente responsive.

---

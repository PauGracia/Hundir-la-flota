Aquí tienes una versión actualizada y mejorada del README, incorporando:

✔️ Que el juego ya es completamente jugable
✔️ Que el enemigo tiene **IA inteligente** (hunt → target → remate)
✔️ Que maneja barcos especiales como portaaviones 2×5
✔️ Que evita casillas prohibidas alrededor de barcos hundidos
✔️ Mejor redacción y estructura general

Puedes copiarlo tal cual:

---

# Hundir la Flota 🎯⚓

**Hundir la Flota** es un juego web totalmente jugable basado en el clásico juego de batalla naval.
El jugador coloca su flota, elige un almirante enemigo y se enfrenta a una **IA avanzada** que analiza impactos, sigue patrones, remata barcos y evita disparos inútiles.

El objetivo del proyecto es ofrecer una experiencia fluida, divertida y visualmente atractiva, con estadísticas reales, sistema de perfiles y partidas continuables.

---

## 🛠 Tecnologías utilizadas

El proyecto está desarrollado con tecnologías web modernas:

- **HTML5** – Estructura y semántica.
- **CSS3** – Estilos, diseño y animaciones.
- **JavaScript (ES6)** – Lógica del juego, IA enemiga, interfaz y validaciones.
- **PHP** – Backend, sesiones, control de partidas y estadísticas.
- **MySQL / MariaDB** – Base de datos del usuario, almirantes, partidas y rankings.
- **Fetch API / AJAX** – Comunicación asíncrona con el servidor.
- **Google Fonts** – Tipografía temática militar (`Russo One`).

---

## 📂 Estructura del proyecto

```
Hundir-la-flota/
├─ assets/
│  ├─ css/        # Estilos y efectos
│  ├─ js/         # Lógica, IA y scripts interactivos
│  └─ img/        # Barcos, iconos, avatares y fondos
├─ php/           # Backend y gestión de datos
├─ juego/         # Pantallas y archivos del tablero de batalla
├─ index.php      # Login / Registro
├─ menuJuego.php  # Menú principal
└─ README.md      # Documentación del proyecto
```

---

## ⚙ Funcionalidades actuales

### 🎮 Juego completamente funcional

- Colocación manual de barcos.
- Tablero animado y efectos de explosión/agua.
- Sistema de turnos.
- Detección automática de **tocado**, **hundido** y **fin de partida**.
- Guardado y carga de partidas en cualquier momento.
- Sonidos y avisos del capitán en cada acción.

### 🤖 IA Inteligente del enemigo

La máquina no es aleatoria. Implementa comportamientos reales del Battleship moderno:

- **Modo búsqueda (random):** dispara en casillas estratégicas evitando repetidos.
- **Modo hunt:** cuando toca un barco, analiza las casillas libres alrededor.
- **Modo target:** deduce la orientación del barco (horizontal, vertical o especial como portaaviones 2×5).
- **Remate completo:** no abandona un barco hasta hundirlo.
- **Zonas prohibidas:** una vez hundido, marca automáticamente todas las casillas adyacentes como imposibles y no dispara allí (como en las reglas originales).
- Compatible con barcos especiales como portaaviones de **2×5**, destructores verticales, etc.
- Funciona correctamente incluso con partidas guardadas y tableros parcialmente descubiertos.

### 👤 Sistema de usuario

- Registro e inicio de sesión.
- Perfil con foto, estadísticas y fecha de registro.
- Puntuaciones acumuladas.
- Música persistente entre pantallas.

### 📊 Estadísticas y almirantes

- Se guarda cada victoria del jugador.
- Los almirantes enemigos también registran sus victorias.
- Historial de partidas contra cada almirante.
- Vista de estadísticas en `perfil.php`.

---

## 🚀 Estado del proyecto

Actualmente el juego está **totalmente jugable**, estable y completo en su núcleo:

✔️ Tablero
✔️ IA enemiga avanzada
✔️ Gestión de partidas
✔️ Finalización y guardado de resultados
✔️ Estadísticas y perfiles

En desarrollo:

- Ranking global entre jugadores.
- Niveles de dificultad para los almirantes.
- Animaciones avanzadas y mejoras visuales.
- Interfaz responsive más refinada.
- Nuevos efectos de sonido.

---

## 🔧 Instalación local

1. Clonar el repositorio en un entorno local (XAMPP, WAMP, Laragon…).
2. Crear la base de datos y tablas según el dump del proyecto.
3. Configurar la conexión en `php/conexion.php`.
4. Iniciar sesión en `http://localhost/Hundir-la-flota/index.php`.
5. Crear usuario y comenzar a jugar.

---

## ⚠ Nota

El proyecto se encuentra en desarrollo activo. Algunas características visuales o de menú pueden cambiar con el tiempo, pero **el núcleo jugable ya es sólido y funcional**.

---

## 📜 Licencia

Proyecto de uso personal y educativo. Se permite su uso libre con fines formativos.

---

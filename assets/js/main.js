document.addEventListener("DOMContentLoaded", () => {
  // Lista de archivos (RENOMBRADOS)
  const almirantes = [
    "Almirante_Cunningham.jpg",
    "Almirante_Donitz.jpg",
    "Almirante_Nimitz.jpg",
    "Almirante_Yamamoto.jpg",
  ];

  // Elegir uno al azar
  const elegido = almirantes[Math.floor(Math.random() * almirantes.length)];

  // Cambiar imagen
  const imgAlmirante = document.getElementById("almirante-img");
  if (imgAlmirante) {
    imgAlmirante.src = "../assets/img/almirantes/" + elegido;
  }

  // Convertir el nombre del archivo en texto legible
  // "Almirante_Nimitz.jpg" → "Almirante Nimitz"
  const nombreLegible = elegido
    .replace(".jpg", "")
    .replace("Almirante_", "Almirante ");

  // Mostrar el nombre
  const nombreAlmirante = document.getElementById("almirante-nombre");
  if (nombreAlmirante) {
    nombreAlmirante.innerText = nombreLegible;
  }

  const sonidoHover = document.getElementById("sonidoHover");
  const sonidoClick = document.getElementById("sonidoClick");

  // Audio fantasma
  const audioUnlock = new Audio();
  audioUnlock.src =
    "data:audio/mp3;base64,//uQxAAAAAAAAAAAAAAAAAAAAAAAWGlnZwAAAA8AAAACAAACcQCA";
  document.addEventListener(
    "click",
    () => {
      audioUnlock.play().catch(() => {});
    },
    { once: true }
  );

  /* ========== COOKIES ========== */
  function setCookie(name, value, days = 365) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`;
  }
  function getCookie(name, defaultValue = null) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parseFloat(parts.pop().split(";")[0]);
    return defaultValue;
  }

  /* ========== CARGAR VOLÚMENES ========== */
  const volumenEfectos = getCookie("volumenEfectos", 0.5);
  const volumenMusica = getCookie("volumenMusica", 0.5);

  if (sonidoHover) sonidoHover.volume = volumenEfectos;
  if (sonidoClick) sonidoClick.volume = volumenEfectos;
  window.volumenEfectos = volumenEfectos;

  // Función global para actualizar efectos
  window.actualizarVolumenEfectos = function (vol) {
    window.volumenEfectos = vol;
    if (sonidoHover) sonidoHover.volume = vol;
    if (sonidoClick) sonidoClick.volume = vol;
    setCookie("volumenEfectos", vol);
  };

  /* ========== SLIDERS ========== */
  const sliderMusica = document.getElementById("volumenMusica");
  const sliderEfectos = document.getElementById("volumenEfectos");

  if (sliderMusica) {
    sliderMusica.value = volumenMusica;
    sliderMusica.addEventListener("input", () => {
      const vol = parseFloat(sliderMusica.value);
      setCookie("volumenMusica", vol);
      if (window.audioGlobal) window.audioGlobal.volume = vol;
    });
  }

  if (sliderEfectos) {
    sliderEfectos.value = volumenEfectos;
    sliderEfectos.addEventListener("input", () => {
      const vol = parseFloat(sliderEfectos.value);
      window.actualizarVolumenEfectos(vol);
    });
  }

  /* ========== HOVER GLOBAL ========== */
  document
    .querySelectorAll(".btn, .menu__btn, .topbar__usuario")
    .forEach((el) => {
      el.addEventListener("mouseover", () => {
        if (sonidoHover) {
          sonidoHover.currentTime = 0;
          sonidoHover.play();
        }
      });
    });

  /* ========== CLICK UNIVERSAL EN <a> ========== */
  document.querySelectorAll("a[href]").forEach((enlace) => {
    enlace.addEventListener("click", (e) => {
      const href = enlace.getAttribute("href");
      if (href === "#" || href.startsWith("javascript:")) return;
      e.preventDefault();
      if (sonidoClick) {
        sonidoClick.currentTime = 0;
        sonidoClick.play();
      }
      setTimeout(() => (window.location.href = href), 200);
    });
  });

  /* ========== CLICK + RETRASO UNIVERSAL EN <form> ========== */
  document.querySelectorAll("form").forEach((form) => {
    if (form.id === "loginForm" || form.id === "registerForm") return;
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (sonidoClick) {
        sonidoClick.currentTime = 0;
        sonidoClick.play();
      }
      setTimeout(() => form.submit(), 200);
    });
  });

  /* =====================================================
     LOGIN / REGISTER
  ===================================================== */

  const toggleLink = document.getElementById("toggleLink");
  const toggleText = document.getElementById("toggleText");
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  if (toggleLink) {
    toggleLink.addEventListener("click", (e) => {
      e.preventDefault();

      const isRegistering = registerForm.classList.contains("hidden");

      if (isRegistering) {
        loginForm.classList.add("hidden");
        registerForm.classList.remove("hidden");
        toggleText.innerHTML =
          '¿Ya tienes cuenta? <a href="#" id="toggleLink" class="auth__link">Inicia sesión</a>';
      } else {
        loginForm.classList.remove("hidden");
        registerForm.classList.add("hidden");
        toggleText.innerHTML =
          '¿No eres usuario? <a href="#" id="toggleLink" class="auth__link">Crea tu cuenta</a>';
      }

      document.getElementById("toggleLink").addEventListener("click", (ev) => {
        ev.preventDefault();
        toggleLink.click();
      });
    });
  }

  // --- Login ---
  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const datos = new FormData(loginForm);
      const respuesta = await fetch("php/login.php", {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();

      if (resultado.success) {
        mostrarMensaje("Inicio de sesión correcto.", "success");
        setTimeout(() => {
          window.location.href =
            "http://localhost/Hundir-la-flota/juego/menuJuego.php";
        }, 1500);
      } else {
        mostrarMensaje(resultado.message, "error");
      }
    });
  }

  // --- Registro ---
  if (registerForm) {
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const pass1 = document.getElementById("nuevoPassword").value;
      const pass2 = document.getElementById("rePassword").value;
      const usuario = document.getElementById("nuevoUsuario").value.trim();

      if (usuario.length < 3 || usuario.length > 15) {
        mostrarMensaje(
          "El nombre de usuario debe tener entre 3 y 15 caracteres.",
          "error"
        );
        return;
      }

      const regexPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
      if (!regexPass.test(pass1)) {
        mostrarMensaje(
          "La contraseña debe tener al menos 8 caracteres, incluir mayúscula, minúscula y número.",
          "error"
        );
        return;
      }

      if (pass1 !== pass2) {
        mostrarMensaje("Las contraseñas no coinciden.", "error");
        return;
      }

      const datos = new FormData(registerForm);
      const respuesta = await fetch("php/registrar.php", {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();

      if (resultado.success) {
        mostrarMensaje(resultado.message, "success");
        setTimeout(() => {
          loginForm.classList.remove("hidden");
          registerForm.classList.add("hidden");
        }, 1500);
      } else {
        mostrarMensaje(resultado.message, "error");
      }
    });
  }

  /* =====================================================
   SALIR DEL JUEGO
===================================================== */
  const btnSalir = document.getElementById("btnSalir");
  const modalSalir = document.getElementById("modalSalir");
  const confirmarSalir = document.getElementById("confirmarSalir");
  const cancelarSalir = document.getElementById("cancelarSalir");

  if (btnSalir && modalSalir) {
    btnSalir.addEventListener("click", (e) => {
      e.preventDefault();
      sonidoClick.currentTime = 0;
      sonidoClick.play();
      modalSalir.classList.remove("oculto");
    });
  }

  if (cancelarSalir && modalSalir) {
    cancelarSalir.addEventListener("click", () => {
      sonidoClick.currentTime = 0;
      sonidoClick.play();
      modalSalir.classList.add("oculto");
    });
  }

  if (confirmarSalir) {
    confirmarSalir.addEventListener("click", () => {
      sonidoClick.currentTime = 0;
      sonidoClick.play();

      setTimeout(() => {
        mostrarMensaje("Saliendo del juego...", "info");
        window.location.href = "../index.php";
      }, 200);
    });
  }

  /* =====================================================
     PERFIL - Cambio de contraseña
  ===================================================== */

  const perfilForm = document.querySelector(".perfil__form");
  if (perfilForm) {
    perfilForm.addEventListener("submit", (e) => {
      const pass1 = document.getElementById("pass1").value.trim();
      const pass2 = document.getElementById("pass2").value.trim();

      if (pass1 !== "" || pass2 !== "") {
        if (pass1 !== pass2) {
          e.preventDefault();
          mostrarMensaje("Las contraseñas no coinciden.", "error");
          return;
        }

        const regexPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!regexPass.test(pass1)) {
          e.preventDefault();
          mostrarMensaje(
            "La contraseña no es lo suficientemente segura.",
            "error"
          );
          return;
        }
      }
    });
  }

  /* =====================================================
   SETTINGS - VOLÚMENES
===================================================== */
  const sliderVolumenMusica = document.getElementById("volumenMusica");
  const sliderVolumenEfectos = document.getElementById("volumenEfectos");

  if (sliderVolumenMusica) {
    sliderVolumenMusica.value =
      parseFloat(localStorage.getItem("volumenMusica")) || 0.5;

    sliderVolumenMusica.addEventListener("input", () => {
      const vol = parseFloat(sliderVolumenMusica.value);
      localStorage.setItem("volumenMusica", vol);

      // Si existe un iframe con música en otras páginas
      const audioFrame = document.getElementById("audioFrame");
      if (audioFrame && audioFrame.contentWindow.setVolumenMusica) {
        audioFrame.contentWindow.setVolumenMusica(vol);
      }
    });
  }

  if (sliderVolumenEfectos) {
    sliderVolumenEfectos.value = window.volumenEfectos;

    sliderVolumenEfectos.addEventListener("input", () => {
      const vol = parseFloat(sliderVolumenEfectos.value);
      window.actualizarVolumenEfectos(vol);
    });
  }
});

// =========================
//  SLIDER DE EFECTOS (PUNTO 4)
// =========================
const sliderEfectos = document.getElementById("volumenEfectos");

if (sliderEfectos) {
  sliderEfectos.value = window.volumenEfectos;

  sliderEfectos.addEventListener("input", () => {
    const vol = parseFloat(sliderEfectos.value);
    window.actualizarVolumenEfectos(vol);
  });
}

/* =====================================================
   SISTEMA DE MENSAJES
===================================================== */
function mostrarMensaje(texto, tipo = "info") {
  const mensaje = document.getElementById("mensaje");
  if (!mensaje) return;

  mensaje.innerHTML = texto;
  mensaje.className = `mensaje ${tipo} visible`;

  clearTimeout(mensaje._timeout);
  mensaje._timeout = setTimeout(() => {
    mensaje.classList.remove("visible");
    mensaje.classList.add("oculto");
  }, 2500);
}

//****Logica colocación de barcos***** */
(function () {
  const board = document.getElementById("board");
  if (!board) return;
  const labelsTop = document.getElementById("labels-top");
  const labelsLeft = document.getElementById("labels-left");
  const letters = "ABCDEFGHIJ";
  const shipsPlaced = [];

  let selectedShip = null;

  // Generar labels
  for (let i = 0; i < 10; i++) {
    const label = document.createElement("div");
    label.textContent = letters[i];
    labelsTop.appendChild(label);
  }

  for (let i = 1; i <= 10; i++) {
    const label = document.createElement("div");
    label.textContent = i;
    labelsLeft.appendChild(label);
  }

  // Generar tablero
  for (let row = 1; row <= 10; row++) {
    for (let col = 0; col < 10; col++) {
      const cell = document.createElement("div");
      cell.classList.add("cell");
      cell.dataset.col = letters[col];
      cell.dataset.row = row;

      cell.addEventListener("click", () => {
        if (selectedShip) {
          placeShip(selectedShip, cell.dataset.col, parseInt(cell.dataset.row));
        }
      });

      board.appendChild(cell);
    }
  }

  // Selección de barcos
  const shipButtons = document.querySelectorAll(".ship-btn");

  shipButtons.forEach((button) => {
    // Botón rotar
    button.querySelector(".rotate-btn").addEventListener("click", (e) => {
      e.stopPropagation();
      const isVertical = button.classList.contains("vertical");

      const verticalImg = button.querySelector(".vertical-img");
      const horizontalImg = button.querySelector(".horizontal-img");

      if (isVertical) {
        button.classList.remove("vertical");
        button.classList.add("horizontal");
        verticalImg.style.display = "none";
        horizontalImg.style.display = "block";
      } else {
        button.classList.remove("horizontal");
        button.classList.add("vertical");
        horizontalImg.style.display = "none";
        verticalImg.style.display = "block";
      }
    });

    // Seleccionar barco
    button.addEventListener("click", (e) => {
      if (e.target.classList.contains("rotate-btn")) return;

      shipButtons.forEach((btn) => btn.classList.remove("selected"));
      button.classList.add("selected");

      const isVertical = button.classList.contains("vertical");

      selectedShip = {
        ship: button.dataset.ship,
        size: parseInt(button.dataset.size),
        vertical: isVertical,
        src: isVertical
          ? button.querySelector(".vertical-img").src
          : button.querySelector(".horizontal-img").src,
      };
    });
  });

  function placeShip(shipInfo, col, row) {
    const size = shipInfo.size;
    const isVertical = shipInfo.vertical;
    const startColIndex = letters.indexOf(col);

    // VERIFICAR QUE EL BARCO NO ESTÉ YA COLOCADO
    if (shipsPlaced.some((placedShip) => placedShip.ship === shipInfo.ship)) {
      mostrarMensaje("Este barco ya ha sido colocado", "error");

      return;
    }

    // VERIFICAR LÍMITES DEL TABLERO
    if (shipInfo.ship === "portaviones") {
      if (isVertical) {
        if (startColIndex + 1 > 10 || row + 4 > 10) {
          mostrarMensaje("No cabe verticalmente", "error");

          return;
        }
      } else {
        if (startColIndex + 5 > 10 || row + 1 > 10) {
          mostrarMensaje("No cabe horizontalmente", "error");

          return;
        }
      }
    } else {
      if (isVertical) {
        if (row + size - 1 > 10) {
          mostrarMensaje("No cabe verticalmente", "error");
          return;
        }
      } else {
        if (startColIndex + size > 10) {
          mostrarMensaje("No cabe horizontalmente", "error");
          return;
        }
      }
    }

    // OBTENER TODAS LAS CELDAS QUE OCUPARÁ EL BARCO Y SUS ALREDEDORES
    const cellsToCheck = [];
    const cellsToOccupy = [];

    if (shipInfo.ship === "portaviones") {
      for (let i = 0; i < 10; i++) {
        let checkCol, checkRow;

        if (isVertical) {
          checkCol = letters[startColIndex + Math.floor(i / 5)];
          checkRow = row + (i % 5);
        } else {
          checkCol = letters[startColIndex + (i % 5)];
          checkRow = row + Math.floor(i / 5);
        }

        cellsToOccupy.push({ col: checkCol, row: checkRow });

        // Añadir celdas adyacentes (alrededor del barco)
        for (let adjRow = checkRow - 1; adjRow <= checkRow + 1; adjRow++) {
          for (
            let adjColIndex = letters.indexOf(checkCol) - 1;
            adjColIndex <= letters.indexOf(checkCol) + 1;
            adjColIndex++
          ) {
            if (
              adjRow >= 1 &&
              adjRow <= 10 &&
              adjColIndex >= 0 &&
              adjColIndex < 10
            ) {
              const adjCol = letters[adjColIndex];
              cellsToCheck.push({ col: adjCol, row: adjRow });
            }
          }
        }
      }
    } else {
      for (let i = 0; i < size; i++) {
        const checkCol = isVertical ? col : letters[startColIndex + i];
        const checkRow = isVertical ? row + i : row;

        cellsToOccupy.push({ col: checkCol, row: checkRow });

        // Añadir celdas adyacentes (alrededor del barco)
        for (let adjRow = checkRow - 1; adjRow <= checkRow + 1; adjRow++) {
          for (
            let adjColIndex = letters.indexOf(checkCol) - 1;
            adjColIndex <= letters.indexOf(checkCol) + 1;
            adjColIndex++
          ) {
            if (
              adjRow >= 1 &&
              adjRow <= 10 &&
              adjColIndex >= 0 &&
              adjColIndex < 10
            ) {
              const adjCol = letters[adjColIndex];
              cellsToCheck.push({ col: adjCol, row: adjRow });
            }
          }
        }
      }
    }

    // Eliminar duplicados de las celdas a verificar
    const uniqueCellsToCheck = Array.from(
      new Set(cellsToCheck.map((cell) => `${cell.col}${cell.row}`))
    ).map((str) => ({ col: str[0], row: parseInt(str.slice(1)) }));

    // VERIFICAR SOLAPAMIENTO Y PROXIMIDAD
    for (const cellPos of uniqueCellsToCheck) {
      const cell = document.querySelector(
        `.cell[data-col="${cellPos.col}"][data-row="${cellPos.row}"]`
      );
      if (cell && cell.dataset.occupied) {
        mostrarMensaje(
          "No puedes colocar barcos tan cerca de otros barcos. Debe haber al menos una casilla de separación.",
          "error"
        );

        return;
      }
    }

    // MARCAR CELDAS COMO OCUPADAS (solo las del barco, no las adyacentes)
    cellsToOccupy.forEach((cellPos) => {
      const cell = document.querySelector(
        `.cell[data-col="${cellPos.col}"][data-row="${cellPos.row}"]`
      );
      if (cell) {
        cell.dataset.occupied = shipInfo.ship;
      }
    });

    // CREAR Y POSICIONAR BARCO VISUAL
    const shipDiv = document.createElement("div");
    shipDiv.classList.add("placed-ship");
    shipDiv.classList.add(isVertical ? "vertical" : "horizontal");

    const cellSize = 48;
    const gap = 4;

    let width, height;

    if (shipInfo.ship === "portaviones") {
      if (isVertical) {
        width = 2 * (cellSize + gap) - gap;
        height = 5 * (cellSize + gap) - gap;
      } else {
        width = 5 * (cellSize + gap) - gap;
        height = 2 * (cellSize + gap) - gap;
      }
    } else {
      width = isVertical ? cellSize : size * (cellSize + gap) - gap;
      height = isVertical ? size * (cellSize + gap) - gap : cellSize;
    }

    shipDiv.style.width = width + "px";
    shipDiv.style.height = height + "px";
    shipDiv.style.left = startColIndex * (cellSize + gap) + "px";
    shipDiv.style.top = (row - 1) * (cellSize + gap) + "px";

    // Añadir imagen
    const shipImg = document.createElement("img");
    shipImg.src = shipInfo.src;
    shipDiv.appendChild(shipImg);

    const shipsLayer = document.getElementById("ships-layer");
    if (shipsLayer) shipsLayer.appendChild(shipDiv);

    // ELIMINAR BARCO DEL PANEL
    const shipButton = document.querySelector(
      `.ship-btn[data-ship="${shipInfo.ship}"]`
    );
    if (shipButton) {
      shipButton.remove();
    }

    // DESELECCIONAR
    selectedShip = null;

    // GUARDAR POSICIÓN
    shipsPlaced.push({
      ship: shipInfo.ship,
      col: col,
      row: row,
      vertical: isVertical,
      size: size,
    });

    console.log("Barcos colocados:", shipsPlaced);

    // VERIFICAR SI TODOS LOS BARCOS ESTÁN COLOCADOS
    if (shipsPlaced.length === 6) {
      document.getElementById("btn-batalla").style.background = "#4CAF50";
      mostrarMensaje(
        "¡Todos los barcos colocados! Puedes comenzar la batalla.",
        "success"
      );
    }
  }
})();

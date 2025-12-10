document.addEventListener("DOMContentLoaded", () => {
  // Lista de almirantes con nombre legible y id
  const almirantes = [
    {
      id: 1,
      archivo: "Almirante_Cunningham.jpg",
      nombreLegible: "Almirante Cunningham",
    },
    {
      id: 2,
      archivo: "Almirante_Donitz.jpg",
      nombreLegible: "Almirante Donitz",
    },
    {
      id: 3,
      archivo: "Almirante_Nimitz.jpg",
      nombreLegible: "Almirante Nimitz",
    },
    {
      id: 4,
      archivo: "Almirante_Yamamoto.jpg",
      nombreLegible: "Almirante Yamamoto",
    },
  ];

  // Elegir uno al azar
  const elegido = almirantes[Math.floor(Math.random() * almirantes.length)];

  // Cambiar imagen
  const imgAlmirante = document.getElementById("almirante-img");
  if (imgAlmirante)
    imgAlmirante.src = "../assets/img/almirantes/" + elegido.archivo;

  // Mostrar el nombre
  const nombreAlmirante = document.getElementById("almirante-nombre");
  if (nombreAlmirante) nombreAlmirante.innerText = elegido.nombreLegible;

  // Guardar id y nombre para enviar al PHP
  const enemigoNombre = elegido.nombreLegible;
  const enemigoId = elegido.id;

  const sonidoHover = document.getElementById("sonidoHover");
  const sonidoClick = document.getElementById("sonidoClick");
  const sonidoAgua = document.getElementById("sonidoAgua");
  const sonidoTocado = document.getElementById("sonidoTocado");
  const sonidoHundido = document.getElementById("sonidoHundido");

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

  /* ========== CLICK UNIVERSAL EN <button data-href> ========== */
  document.querySelectorAll("button").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const destino = btn.dataset.href;

      if (!destino) return;

      e.preventDefault();

      if (sonidoClick) {
        sonidoClick.currentTime = 0;
        sonidoClick.play();
      }

      setTimeout(() => {
        window.location.href = destino;
      }, 200);
    });
  });

  const sonidoSeleccionBarco = document.getElementById("sonidoSeleccionBarco");
  const sonidoRotarBarco = document.getElementById("sonidoRotarBarco");

  // Sonido al seleccionar un barco
  document.querySelectorAll(".ship-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      if (sonidoSeleccionBarco) {
        sonidoSeleccionBarco.currentTime = 0;
        sonidoSeleccionBarco.play();
      }
    });
  });

  // Sonido al rotar un barco
  document.querySelectorAll(".ship-btn .rotate-btn").forEach((span) => {
    span.addEventListener("click", (e) => {
      e.stopPropagation(); // Evita que se dispare el click del barco
      if (sonidoRotarBarco) {
        sonidoRotarBarco.currentTime = 0;
        sonidoRotarBarco.play();
      }
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
        window.location.href = "/Hundir-la-flota/php/logout.php";
      }, 200);
    });
  }

  // Variables globales para control de sonido
  let sonidosHabilitados = false;

  console.log("DOM cargado - inicializando eventos");

  // Habilitar sonidos después del primer click del usuario
  function habilitarSonidos() {
    sonidosHabilitados = true;
    document.removeEventListener("click", habilitarSonidos);
    document.removeEventListener("keydown", habilitarSonidos);
    console.log("Sonidos habilitados");
  }

  document.addEventListener("click", habilitarSonidos);
  document.addEventListener("keydown", habilitarSonidos);

  console.log("DOM cargado - inicializando eventos");

  // ==========================
  // MODAL CARGAR PARTIDA
  // ==========================
  if (document.getElementById("modalCargar")) {
    const btnCargarPartida = document.getElementById("btnCargarPartida");
    const modalCargar = document.getElementById("modalCargar");
    const cerrarModalCargar = document.getElementById("cerrarModalCargar");

    console.log("btnCargarPartida:", btnCargarPartida);
    console.log("modalCargar:", modalCargar);
    console.log("cerrarModalCargar:", cerrarModalCargar);

    if (btnCargarPartida) {
      btnCargarPartida.addEventListener("click", function (e) {
        console.log("Clic en btnCargarPartida");
        e.preventDefault();
        e.stopPropagation();

        if (!this.hasAttribute("disabled")) {
          console.log("Abriendo modal cargar partida");
          modalCargar.classList.remove("oculto");
        } else {
          console.log("Botón deshabilitado - no hay partidas");
        }
      });
    }

    if (cerrarModalCargar) {
      cerrarModalCargar.addEventListener("click", function () {
        console.log("Cerrando modal cargar partida");
        modalCargar.classList.add("oculto");
      });
    }

    modalCargar.addEventListener("click", function (e) {
      if (e.target === modalCargar) {
        console.log("Clic fuera del modal - cerrando");
        modalCargar.classList.add("oculto");
      }
    });
  }

  // ==========================
  // MODAL SALIR
  // ==========================
  const btnSalirModal = document.getElementById("btnSalir");
  const modalSalirModal = document.getElementById("modalSalir");
  const confirmarSalirModal = document.getElementById("confirmarSalir");
  const cancelarSalirModal = document.getElementById("cancelarSalir");

  if (btnSalirModal) {
    btnSalir.addEventListener("click", function (e) {
      e.preventDefault();
      console.log("Abriendo modal salir");
      modalSalir.classList.remove("oculto");
    });
  }

  if (confirmarSalirModal) {
    confirmarSalir.addEventListener("click", function () {
      console.log("Confirmando salida");
      window.location.href = "../php/logout.php";
    });
  }

  if (cancelarSalirModal) {
    cancelarSalir.addEventListener("click", function () {
      console.log("Cancelando salida");
      modalSalir.classList.add("oculto");
    });
  }

  if (modalSalirModal) {
    modalSalir.addEventListener("click", function (e) {
      if (e.target === modalSalir) {
        console.log("Clic fuera del modal salir - cerrando");
        modalSalir.classList.add("oculto");
      }
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

  // =========================
  //  SLIDER DE EFECTOS (PUNTO 4)
  // =========================
  //const sliderEfectos = document.getElementById("volumenEfectos");

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

  const shipsPlaced = []; // Debe ser global
  (function () {
    const board = document.getElementById("board");
    if (!board) return;
    const labelsTop = document.getElementById("labels-top");
    const labelsLeft = document.getElementById("labels-left");
    const letters = "ABCDEFGHIJ";

    let selectedShip = null;

    // Generar tablero
    for (let row = 1; row <= 10; row++) {
      for (let col = 0; col < 10; col++) {
        const cell = document.createElement("div");
        cell.classList.add("cell");
        cell.dataset.col = letters[col];
        cell.dataset.row = row;

        cell.addEventListener("click", () => {
          if (selectedShip) {
            placeShip(
              selectedShip,
              cell.dataset.col,
              parseInt(cell.dataset.row)
            );
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
      /*shipsPlaced.push({
      ship: shipInfo.ship,
      col: col,
      row: row,
      vertical: isVertical,
      size: size,
    });*/
      // DETERMINAR ANCHO Y ALTO REALES
      let anchoReal, altoReal;

      // PORTAVIONES → tamaño 2x5
      if (shipInfo.ship === "portaviones") {
        if (isVertical) {
          anchoReal = 2;
          altoReal = 5;
        } else {
          anchoReal = 5;
          altoReal = 2;
        }
      }
      // BARCOS NORMALES → 1xN
      else {
        if (isVertical) {
          anchoReal = 1;
          altoReal = size;
        } else {
          anchoReal = size;
          altoReal = 1;
        }
      }

      // GUARDAR POSICIÓN SEGÚN NUEVO FORMATO
      shipsPlaced.push({
        tipo: shipInfo.ship,
        size: anchoReal * altoReal,
        ancho: anchoReal,
        alto: altoReal,
        orientacion: isVertical ? "vertical" : "horizontal",
        xInicio: letters.indexOf(col) + 1, // convertir A → 1, B → 2...
        yInicio: row,
      });

      console.log("Barcos colocados:", shipsPlaced);

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

  //*********Modal Guardar datos para Partida/Batalla************/
  const btnBatalla = document.getElementById("btn-batalla");
  if (btnBatalla) {
    btnBatalla.addEventListener("click", async () => {
      const jugadorNombre = document.getElementById(
        "almirante-nombre-jugador"
      ).value;
      const almiranteNombre =
        document.getElementById("almirante-nombre").innerText;

      if (shipsPlaced.length !== 6) {
        mostrarMensaje(
          "Debe colocar todos los barcos antes de iniciar la batalla",
          "info"
        );
        return;
      }

      const respuesta = await fetch("../php/guardarPartida.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          jugador: jugadorNombre,
          oponente: almiranteNombre,
          flotaJugador: shipsPlaced,
        }),
      });

      const data = await respuesta.json();

      if (data.error) {
        mostrarMensaje(data.error, true);
        return;
      }

      window.location.href = "batalla.php?id=" + data.idPartida;
    });
  }
  ///////////////////////////////////////
  // Pantalla Batalla
  //////////////////////////////////////
  if (window.JUEGO_DATA) {
    const {
      flotaJugador,
      flotaEnemigo,
      disparos,
      usuario,
      imagenUsuario,
      idPartida,
    } = window.JUEGO_DATA;

    let tiempo = window.JUEGO_DATA.tiempo;
    let puntos = window.JUEGO_DATA.puntos;

    const letters = "ABCDEFGHIJ";
    const playerBoard = document.getElementById("board-player");
    const enemyBoard = document.getElementById("enemy-board");

    let matrizJugador = Array.from({ length: 10 }, () => Array(10).fill(null));
    let matrizEnemigo = Array.from({ length: 10 }, () => Array(10).fill(null));

    let turno = null; // "jugador" o "enemigo"
    let partidaIniciada = false;

    // Temporizador

    setInterval(() => {
      tiempo++;
      document.getElementById("timer-enemy").textContent =
        "Tiempo: " + tiempo + "s";
    }, 1000);

    // ==========================
    // TABLERO DEL JUGADOR
    // ==========================
    flotaJugador.forEach((b, idx) => {
      const tipo = b.tipo ?? "barco" + idx;
      const startX = (b.xInicio ?? 1) - 1;
      const startY = (b.yInicio ?? 1) - 1;
      const ancho = b.ancho ?? 1;
      const alto = b.alto ?? 1;

      for (let dy = 0; dy < alto; dy++) {
        for (let dx = 0; dx < ancho; dx++) {
          const nx = startX + dx;
          const ny = startY + dy;
          if (nx < 0 || nx > 9 || ny < 0 || ny > 9) continue;
          matrizJugador[ny][nx] = { tipo: tipo, barcoIndex: idx };
        }
      }
    });

    for (let y = 0; y < 10; y++) {
      for (let x = 0; x < 10; x++) {
        const cell = document.createElement("div");
        cell.classList.add("cell-player-batalla");
        cell.dataset.x = x + 1;
        cell.dataset.y = y + 1;
        cell.dataset.col = letters[x];
        cell.dataset.row = y + 1;
        cell.id = `player-${x + 1}-${y + 1}`;

        const info = matrizJugador[y][x];
        if (info) {
          cell.classList.add("cell-ship");
          cell.dataset.occupied = "true";
          cell.dataset.ship = info.tipo;
          cell.title = `${cell.dataset.col}${cell.dataset.row} — ${info.tipo}`;
        } else {
          cell.title = `${cell.dataset.col}${cell.dataset.row}`;
        }
        playerBoard.appendChild(cell);
      }
    }

    // ==========================
    // TABLERO DEL ENEMIGO
    // ==========================
    flotaEnemigo.forEach((b, idx) => {
      const tipo = b.tipo ?? "enemigo" + idx;
      const startX = (b.xInicio ?? 1) - 1;
      const startY = (b.yInicio ?? 1) - 1;
      const ancho = b.ancho ?? 1;
      const alto = b.alto ?? 1;

      for (let dy = 0; dy < alto; dy++) {
        for (let dx = 0; dx < ancho; dx++) {
          const nx = startX + dx;
          const ny = startY + dy;
          if (nx < 0 || nx > 9 || ny < 0 || ny > 9) continue;
          matrizEnemigo[ny][nx] = { tipo: tipo, barcoIndex: idx };
        }
      }
    });

    // Pintar tablero enemigo
    for (let y = 0; y < 10; y++) {
      for (let x = 0; x < 10; x++) {
        const cell = document.createElement("div");
        cell.classList.add("cell-enemy-batalla");
        cell.dataset.x = x + 1;
        cell.dataset.y = y + 1;
        cell.dataset.col = letters[x];
        cell.dataset.row = y + 1;
        cell.id = `enemy-${x + 1}-${y + 1}`;

        const info = matrizEnemigo[y][x];
        if (info) {
          cell.dataset.occupied = "true";
          cell.dataset.ship = info.tipo;
          cell.dataset.barcoIndex = info.barcoIndex;
        }
        enemyBoard.appendChild(cell);
      }
    }

    // ==========================
    // FUNCIONES
    // ==========================
    /*function mostrarMensaje(text, isError = false) {
      const msg = document.getElementById("mensaje");
      msg.textContent = text;
      msg.className = isError ? "mensaje error" : "mensaje";
      setTimeout(() => {
        msg.textContent = "";
        msg.className = "mensaje";
      }, 3500);
    }*/

    const mensajesJuego = [];

    const MAX_MESSAGES = 3;

    function mostrarMensajeCapitan(text) {
      mensajesJuego.push(text);
      while (mensajesJuego.length > MAX_MESSAGES) mensajesJuego.shift();

      const contenedor = document.getElementById("mensajes-juego");
      if (!contenedor) return;

      const html = mensajesJuego
        .slice()
        .map((m) => `<p class="msg-line">${m}</p>`)
        .join("");
      contenedor.innerHTML = html;

      // Forzar scroll ARRIBA de forma robusta
      mantenerScrollArriba(contenedor);
    }

    function mantenerScrollArriba(contenedor) {
      if (!contenedor) return;

      // Evitar comportamiento smooth CSS al forzar 'auto'
      try {
        contenedor.style.scrollBehavior = "auto";
      } catch (e) {}

      const cs = getComputedStyle(contenedor);
      const isColumnReverse =
        cs.display.includes("flex") && cs.flexDirection === "column-reverse";

      const targetTop = isColumnReverse ? contenedor.scrollHeight : 0;

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          setTimeout(() => {
            try {
              contenedor.scrollTo({ top: targetTop, behavior: "auto" });
            } catch (e) {
              contenedor.scrollTop = targetTop;
            }
          }, 20);
        });
      });
    }

    function actualizarTurno(turnoActual) {
      const playerHeader = document.getElementById("player-header-panel");
      const attackerHeader = document.getElementById("attacker-header-panel");

      // Limpia los bordes
      playerHeader.classList.remove("turno");
      attackerHeader.classList.remove("turno");

      if (turnoActual === "jugador") {
        playerHeader.classList.add("turno");
        attackerHeader.classList.remove("turno");
      } else {
        playerHeader.classList.remove("turno");
        attackerHeader.classList.add("turno");
      }
    }

    function actualizarPuntos(resultado) {
      if (resultado === "tocado") puntos += 100;
      else if (resultado === "hundido") puntos += 1000;
      else if (resultado === "agua") puntos -= 10;

      document.getElementById("score-enemy").textContent = "Puntos: " + puntos;
    }

    /*function finalizarPartida(ganador) {
      if (ganador === usuario) puntos += 5000;

      fetch("../php/guardarProgreso.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          idPartida: parseInt(document.getElementById("idPartida").value),
          puntos: puntos,
          estadoTablero: estadoTablero,
        }),
      })
        .then((r) => r.json())
        .then((data) => console.log("Puntos guardados:", data));
    }*/

    function colocarBarcosEnemigos(flota) {
      const layer = document.getElementById("enemy-ships-layer");
      layer.innerHTML = ""; // opcional

      flota.forEach((barco) => {
        const x = (barco.xInicio ?? 1) - 1;
        const y = (barco.yInicio ?? 1) - 1;
        const ancho = barco.ancho ?? 1;
        const alto = barco.alto ?? 1;

        for (let dy = 0; dy < alto; dy++) {
          for (let dx = 0; dx < ancho; dx++) {
            const nx = x + dx;
            const ny = y + dy;

            const cell = document.getElementById(`enemy-${nx + 1}-${ny + 1}`);
            if (!cell) continue;

            cell.classList.add("cell-ship"); // estilo gris
          }
        }
      });
    }

    function colocarBarcosJugador(flota) {
      const layer = document.createElement("div");
      layer.classList.add("ships-layer");
      layer.style.position = "absolute";
      layer.style.top = "0";
      layer.style.left = "0";
      layer.style.width = "100%";
      layer.style.height = "100%";
      layer.style.pointerEvents = "none";

      document.getElementById("board-player").appendChild(layer);

      const cellElement = document.querySelector(".cell-player-batalla");
      const cellSize = cellElement.offsetWidth;
      const gap = 2; // Modificar segun el gap del CSS

      flota.forEach((barco) => {
        const x = (barco.xInicio ?? 1) - 1;
        const y = (barco.yInicio ?? 1) - 1;
        const ancho = barco.ancho ?? 1;
        const alto = barco.alto ?? 1;

        const widthPx = ancho * cellSize + (ancho - 1) * gap;
        const heightPx = alto * cellSize + (alto - 1) * gap;

        const left = x * (cellSize + gap);
        const top = y * (cellSize + gap);

        const shipDiv = document.createElement("div");
        shipDiv.classList.add("placed-ship");
        shipDiv.style.width = widthPx + "px";
        shipDiv.style.height = heightPx + "px";
        shipDiv.style.left = left + "px";
        shipDiv.style.top = top + "px";

        const img = document.createElement("img");
        img.src =
          ancho > alto
            ? `../assets/img/imagenes/rotated_${barco.tipo}.png`
            : `../assets/img/imagenes/${barco.tipo}.png`;

        shipDiv.appendChild(img);
        layer.appendChild(shipDiv);
      });
    }

    function crearOverlayDisparos() {
      const overlay = document.getElementById("enemy-overlay");
      overlay.innerHTML = "";

      // Asegurar que el overlay tenga las dimensiones correctas
      overlay.style.width = "calc(10 * 40px + 9 * 3px)";
      overlay.style.height = "calc(10 * 40px + 9 * 3px)";

      for (let y = 1; y <= 10; y++) {
        for (let x = 1; x <= 10; x++) {
          const btn = document.createElement("div");
          btn.classList.add("overlay-cell");
          btn.dataset.x = x;
          btn.dataset.y = y;

          // DEBUG: Mostrar coordenadas temporalmente
          btn.title = `Disparar a ${letters[x - 1]}${y}`;

          // Verificar si ya hay disparo aquí
          const disparoPrevio = disparos.find(
            (d) => d.posX === x && d.posY === y && d.propietario === "jugador"
          );
          const celdaReal = document.getElementById(`enemy-${x}-${y}`);

          if (disparoPrevio) {
            btn.classList.add("revealed");
            celdaReal.dataset.disparado = "true";

            if (
              disparoPrevio.resultado === "tocado" ||
              disparoPrevio.resultado === "hundido"
            ) {
              btn.innerHTML = "💥";
              btn.style.color = "yellow";
              btn.style.fontSize = "24px";
              btn.style.display = "flex";
              btn.style.justifyContent = "center";
              btn.style.alignItems = "center";
            } else {
              btn.innerHTML = "💧";
              btn.style.color = "lightblue";
              btn.style.fontSize = "20px";
              btn.style.display = "flex";
              btn.style.justifyContent = "center";
              btn.style.alignItems = "center";
            }
          }

          btn.addEventListener("click", (e) => {
            console.log(`Click en overlay: ${x},${y}`);
            manejarDisparoJugador(x, y, btn, celdaReal);
          });

          overlay.appendChild(btn);
        }
      }
    }

    // Función para manejar disparos

    function manejarDisparoJugador(x, y, overlayCell, celdaReal) {
      if (turno !== "jugador") {
        mostrarMensajeCapitan("¡Espere su turno, almirante!");
        return;
      }

      if (celdaReal.dataset.disparado === "true") return;

      celdaReal.dataset.disparado = "true";
      overlayCell.classList.add("revealed");

      const ocupado = celdaReal.dataset.occupied === "true";
      const barco = celdaReal.dataset.ship || null;
      const layer = document.getElementById("enemy-ships-layer");

      if (ocupado) {
        // Crear contenedor para img o gif
        const fuego = document.createElement("div");
        fuego.classList.add("fire-hit-cell");
        const cellSize = 40,
          gap = 3;
        fuego.style.left = (x - 1) * (cellSize + gap) + "px";
        fuego.style.top = (y - 1) * (cellSize + gap) + "px";
        fuego.style.width = cellSize + "px";
        fuego.style.height = cellSize + "px";
        fuego.style.display = "flex";
        fuego.style.justifyContent = "center";
        fuego.style.alignItems = "center";

        // GIF o img explosión
        const imgExp = document.createElement("img");
        imgExp.src = "../assets/img/icons/explosion.png";
        imgExp.style.width = "100%";
        imgExp.style.height = "100%";
        imgExp.style.objectFit = "contain";
        fuego.appendChild(imgExp);

        layer.appendChild(fuego);

        mostrarMensajeCapitan(`¡Impacto en ${letters[x - 1]}${y}!`);
        actualizarPuntos("tocado");

        // Verificar si barco hundido
        const todasCeldas = Array.from(
          document.querySelectorAll(`.cell-enemy-batalla[data-ship='${barco}']`)
        );
        const hundido = todasCeldas.every(
          (c) => c.dataset.disparado === "true"
        );

        // Sonido de tocado
        if (sonidoTocado) {
          sonidoTocado.currentTime = 0;
          sonidoTocado.play();
        }

        if (hundido) {
          if (sonidoHundido) {
            sonidoHundido.currentTime = 0;
            sonidoHundido.play();
          }
          mostrarMensajeCapitan(
            `¡Almirante! Hemos hundido el ${barco} enemigo!`
          );
          actualizarPuntos("hundido");
        }
      } else {
        // Sonido de agua
        if (sonidoAgua) {
          sonidoAgua.currentTime = 0;
          sonidoAgua.play();
        }
        overlayCell.innerHTML = "";
        overlayCell.style.display = "flex";
        overlayCell.style.justifyContent = "center";
        overlayCell.style.alignItems = "center";
        overlayCell.style.fontSize = "20px";

        celdaReal.classList.add("miss");
        mostrarMensajeCapitan(`Agua en ${letters[x - 1]}${y}`);
        actualizarPuntos("agua");
      }

      // Guardar disparo
      fetch("../php/guardarDisparo.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          idPartida: parseInt(idPartida),
          propietario: "jugador",
          x: x,
          y: y,
          resultado: ocupado ? "tocado" : "agua",
        }),
      })
        .then((r) => r.json())
        .then((d) => d.error && console.error(d.error));

      // Verificar si todos los barcos enemigos están hundidos
      const todasCeldasEnemigo = Array.from(
        document.querySelectorAll('.cell-enemy-batalla[data-occupied="true"]')
      );
      const todosHundidos = todasCeldasEnemigo.every(
        (c) => c.dataset.disparado === "true"
      );
      if (todosHundidos) {
        finalizarPartida(usuario);
        return;
      }

      turno = "enemigo";
      actualizarTurno(turno);
      setTimeout(turnoEnemigo, 2200);
    }

    // Turno enemigo con GIF o img
    function turnoEnemigo() {
      mostrarMensajeCapitan("El enemigo está disparando…");
      let x, y, celda;

      do {
        x = Math.floor(Math.random() * 10) + 1;
        y = Math.floor(Math.random() * 10) + 1;
        celda = document.getElementById(`player-${x}-${y}`);
      } while (celda.classList.contains("disparado"));

      celda.classList.add("disparado");
      const ocupado = celda.dataset.occupied === "true";

      if (ocupado) {
        celda.classList.add("hit-player");
        // Sonido de tocado
        if (sonidoTocado) {
          sonidoTocado.currentTime = 0;
          sonidoTocado.play();
        }
        celda.innerHTML = "";
        const imgExp = document.createElement("img");
        imgExp.src = "../assets/img/icons/explosion.png";
        imgExp.style.width = "100%";
        imgExp.style.height = "100%";
        imgExp.style.objectFit = "contain";
        celda.appendChild(imgExp);

        mostrarMensajeCapitan(
          `¡Almirante! Han tocado nuestro ${celda.dataset.ship}!`
        );
        // Si está hundido
        const todasCeldasBarco = Array.from(
          document.querySelectorAll(
            `.cell-player-batalla[data-ship='${celda.dataset.ship}']`
          )
        );
        const hundido = todasCeldasBarco.every((c) =>
          c.classList.contains("disparado")
        );
        if (hundido && sonidoHundido) {
          sonidoHundido.currentTime = 0;
          sonidoHundido.play();
        }
      } else {
        celda.classList.add("miss-player");
        celda.innerHTML = "🟦";
        if (sonidoAgua) {
          sonidoAgua.currentTime = 0;
          sonidoAgua.play();
        }
        mostrarMensajeCapitan("El enemigo ha fallado.");
      }

      fetch("../php/guardarDisparo.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          idPartida: parseInt(document.getElementById("idPartida").value),
          propietario: "enemigo",
          x: x,
          y: y,
          resultado: ocupado ? "tocado" : "agua",
        }),
      })
        .then((r) => r.json())
        .then((d) => console.log("Disparo enemigo guardado:", d));

      // Verificar si todos los barcos del jugador están hundidos
      const todasCeldasJugador = Array.from(
        document.querySelectorAll('.cell-player-batalla[data-occupied="true"]')
      );
      const todosHundidosJugador = todasCeldasJugador.every((c) =>
        c.classList.contains("disparado")
      );
      if (todosHundidosJugador) {
        finalizarPartida("Enemigo");
        return;
      }

      turno = "jugador";
      mostrarMensajeCapitan("Es su turno, almirante.");
      actualizarTurno("jugador");
    }

    function sorteoInicial() {
      if (partidaIniciada) return;
      const empiezaJugador = Math.random() < 0.5;
      turno = empiezaJugador ? "jugador" : "enemigo";

      if (empiezaJugador) {
        mostrarMensajeCapitan(
          "¡Almirante! Hemos ganado el sorteo, usted dispara primero."
        );
      } else {
        mostrarMensajeCapitan(
          "Almirante… el enemigo ha ganado el sorteo. ¡Prepárese!"
        );
        setTimeout(turnoEnemigo, 1500);
      }

      partidaIniciada = true;
    }

    function finalizarPartida(ganador) {
      // Guardar partida primero
      fetch("../php/finalizarPartida.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          idPartida: parseInt(document.getElementById("idPartida").value),
          puntos: puntos,
          estadoTablero: estadoTablero,
          ganador: ganador,
          enemigoNombre: enemigoNombre, // nombre del enemigo cargado en la partida
          enemigoFoto: elegido,
          enemigoId: enemigoId,
        }),
      })
        .then((r) => r.json())
        .then((data) => {
          console.log("Partida guardada:", data);

          let tipoResultado = ganador === usuario ? "victoria" : "derrota";
          let nombreRival, fotoRival;

          if (tipoResultado === "victoria") {
            nombreRival = usuario;
            fotoRival = imagenUsuario; // avatar del usuario
          } else {
            nombreRival = enemigoNombre;
            fotoRival = elegido.archivo;
          }

          // Redirigir
          window.location.href = `resultado.php?tipo=${tipoResultado}&nombre=${encodeURIComponent(
            nombreRival
          )}&foto=${encodeURIComponent(fotoRival)}`;
        })

        .catch((err) => {
          console.error("Error guardando partida:", err);
          alert("Error al guardar la partida antes de finalizar.");
        });
    }

    /*function debugOverlay() {
    const overlayCells = document.querySelectorAll('.overlay-cell');
    console.log(`Total celdas overlay: ${overlayCells.length}`);
    
    overlayCells.forEach(cell => {
        cell.addEventListener('mouseenter', () => {
            cell.style.background = 'rgba(255, 0, 0, 0.3)';
        });
        cell.addEventListener('mouseleave', () => {
            if (!cell.classList.contains('revealed')) {
                cell.style.background = 'rgba(0, 0, 0, 0.3)';
            }
        });
    });
}

// Llamar después de crearOverlayDisparos()
setTimeout(debugOverlay, 1000);*/

    // ==========================
    // INICIALIZACION
    // ==========================
    colocarBarcosJugador(flotaJugador);

    colocarBarcosEnemigos(flotaEnemigo);
    crearOverlayDisparos();
    // Restaurar disparos del jugador
    restaurarDisparosJugador(
      disparos.filter((d) => d.propietario === "jugador")
    );
    sorteoInicial();

    // ==========================
    // BOTÓN GUARDAR PARTIDA - SOLO UNA VEZ
    // ==========================
    document
      .getElementById("guardarPartida")
      .addEventListener("click", async () => {
        // Reproducir sonido de click
        if (sonidoClick) {
          sonidoClick.currentTime = 0;
          sonidoClick.play().catch(() => {});
        }
        const idPartida = document.getElementById("idPartida").value;

        // Recolectar estado actual de los disparos
        const estadoActual = {
          // Disparos del jugador
          disparosJugador: Array.from(
            document.querySelectorAll('.cell-enemy[data-disparado="true"]')
          ).map((celda) => ({
            x: parseInt(celda.dataset.x),
            y: parseInt(celda.dataset.y),
            resultado: celda.classList.contains("miss") ? "agua" : "tocado",
          })),
          // Disparos del enemigo
          disparosEnemigo: Array.from(
            document.querySelectorAll(".cell-player.disparado")
          ).map((celda) => ({
            x: parseInt(celda.dataset.x),
            y: parseInt(celda.dataset.y),
            resultado: celda.classList.contains("hit-player")
              ? "tocado"
              : "agua",
          })),
          turnoActual: turno,
          partidaIniciada: partidaIniciada,
        };

        try {
          const respuesta = await fetch("../php/guardarProgreso.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              idPartida: parseInt(idPartida),
              flotaJugador: flotaJugador,
              flotaEnemigo: flotaEnemigo,
              estadoTablero: estadoActual, // Guardar el estado actual
              puntos: puntos,
              tiempo: tiempo,
            }),
          });

          const data = await respuesta.json();
          console.log("Respuesta guardado:", data);

          if (data.ok) {
            mostrarMensaje("Partida guardada correctamente", "success");
          } else {
            mostrarMensaje(
              "Error al guardar: " + (data.error || "Desconocido"),
              "error"
            );
          }
        } catch (error) {
          console.error("Error:", error);
          mostrarMensaje("Error al guardar la partida", "error");
        }
      });

    // ==========================
    // RESTAURAR DISPAROS PREVIOS
    // ==========================
    disparos.forEach((d) => {
      const x = d.posX;
      const y = d.posY;

      if (d.propietario === "jugador") {
        const celda = document.getElementById(`enemy-${x}-${y}`);
        if (!celda) return;

        celda.dataset.disparado = "true";
        const layer = document.getElementById("enemy-ships-layer");

        if (d.resultado === "tocado" || d.resultado === "hundido") {
          const fuego = document.createElement("div");
          fuego.classList.add("fire-hit-cell");
          const cellSize = 40,
            gap = 3;
          fuego.style.left = (x - 1) * (cellSize + gap) + "px";
          fuego.style.top = (y - 1) * (cellSize + gap) + "px";
          fuego.style.width = cellSize + "px";
          fuego.style.height = cellSize + "px";

          const imgExp = document.createElement("img");
          imgExp.src = "../assets/img/icons/explosion.png";
          imgExp.style.width = "100%";
          imgExp.style.height = "100%";
          imgExp.style.objectFit = "contain";

          fuego.appendChild(imgExp); // Añado el GIF al div
          layer.appendChild(fuego); // Y el div al layer
        } else {
          celda.classList.add("miss");
        }
      } else {
        const celda = document.getElementById(`player-${x}-${y}`);
        if (!celda) return;

        celda.classList.add("disparado");

        if (d.resultado === "tocado" || d.resultado === "hundido") {
          celda.classList.add("hit-player");
          celda.innerHTML = ""; // limpiamos el contenido
          const imgExp = document.createElement("img");
          imgExp.src = "../assets/img/icons/explosion.png";
          imgExp.style.width = "100%";
          imgExp.style.height = "100%";
          imgExp.style.objectFit = "contain";
          celda.appendChild(imgExp); // ponemos el GIF en la celda
        } else {
          celda.classList.add("miss-player");
          celda.innerHTML = "🟦";
        }
      }
    });

    // Función para restaurar overlay del jugador
    function restaurarDisparosJugador(disparosJugador) {
      disparosJugador.forEach((d) => {
        const x = d.posX;
        const y = d.posY;

        const overlayCell = document.querySelector(
          `#enemy-overlay .overlay-cell[data-x="${x}"][data-y="${y}"]`
        );
        if (!overlayCell) return;

        overlayCell.classList.add("revealed");
        overlayCell.innerHTML = ""; // limpiar contenido previo

        if (d.resultado === "tocado" || d.resultado === "hundido") {
          const img = document.createElement("img");
          img.src = "../assets/img/icons/explosion.png";
          img.style.width = "40px";
          img.style.height = "40px";
          img.style.objectFit = "contain";
          overlayCell.appendChild(img);
        }
      });
    }

    //////////////////////////////////////////////////////

    // VForzar victoria Jugador
    //finalizarPartida(usuario);

    // Forzar victoria Enemigo
    //finalizarPartida("Enemigo"); //en proceso

    ////////////////////////////////////////////////////////
  } // Cierre del if (window.JUEGO_DATA)
});

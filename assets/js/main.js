/*document.addEventListener("DOMContentLoaded", () => {

  
  // --- Alternar entre login y registro ---
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

      // reactivar evento en el nuevo enlace
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

      // Validación básica
      if (usuario.length < 3 || usuario.length > 15) {
        mostrarMensaje(
          "El nombre de usuario debe tener entre 3 y 15 caracteres.",
          "error"
        );
        return;
      }

      // Validación contraseña fuerte (mínimo 8, mayúscula, minúscula, número)
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
        // Espera 1.5 segundos y vuelve al login
        setTimeout(() => {
          loginForm.classList.remove("hidden");
          registerForm.classList.add("hidden");
        }, 1500);
      } else {
        mostrarMensaje(resultado.message, "error");
      }
    });
  }

  // --- Botón "Salir" en menú principal ---
  const btnSalir = document.getElementById("btnSalir");
  const modalSalir = document.getElementById("modalSalir");
  const confirmarSalir = document.getElementById("confirmarSalir");
  const cancelarSalir = document.getElementById("cancelarSalir");

  if (btnSalir) {
    btnSalir.addEventListener("click", (e) => {
      e.preventDefault();
      modalSalir.classList.remove("oculto");
    });
  }

  if (cancelarSalir) {
    cancelarSalir.addEventListener("click", () => {
      modalSalir.classList.add("oculto");
    });
  }

  if (confirmarSalir) {
    confirmarSalir.addEventListener("click", () => {
      mostrarMensaje("Saliendo del juego...", "info");
      setTimeout(() => {
        window.location.href = "../index.php";
      }, 1000);
    });
  }

  // Validación de cambio de contraseña en perfil
  const perfilForm = document.querySelector(".perfil__form");
  if (perfilForm) {
    perfilForm.addEventListener("submit", (e) => {
      const pass1 = document.getElementById("pass1").value.trim();
      const pass2 = document.getElementById("pass2").value.trim();

      // Solo validamos si se ha escrito algo
      if (pass1 !== "" || pass2 !== "") {
        if (pass1 !== pass2) {
          e.preventDefault();
          mostrarMensaje("Las contraseñas no coinciden.", "error");
          return;
        }

        // Opcional: validar fuerza de contraseña
        const regexPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!regexPass.test(pass1)) {
          e.preventDefault();
          mostrarMensaje(
            "La contraseña debe tener al menos 8 caracteres, incluir mayúscula, minúscula y número.",
            "error"
          );
          return;
        }
      }
    });
  }

  // --- Sonidos del menú ---
  const sonidoHover = document.getElementById("sonidoHover");
  const sonidoClick = document.getElementById("sonidoClick");

  // Seleccionar botones del menú
  const botonesMenu = document.querySelectorAll(".menu__btn");

  botonesMenu.forEach((btn) => {
    // Sonido al pasar por encima
    btn.addEventListener("mouseover", () => {
      if (sonidoHover) {
        sonidoHover.currentTime = 0;
        sonidoHover.play();
      }
    });

    // Sonido al hacer clic
    btn.addEventListener("click", () => {
      if (sonidoClick) {
        sonidoClick.currentTime = 0;
        sonidoClick.play();
      }
    });
  });

  // ---- SETTINGS: Control de volumen de música y efectos ----
  const sliderVolumen = document.getElementById("volumenMusica");

  if (sliderVolumen) {
    // Cargar valor guardado (o 0.5 por defecto)
    let vol = parseFloat(localStorage.getItem("volumenMusica")) || 0.5;
    sliderVolumen.value = vol;

    // Aplicar volumen a la música del IFRAME
    const audioFrame = document.getElementById("audioFrame");
    if (audioFrame && audioFrame.contentWindow.setVolumenMusica) {
      audioFrame.contentWindow.setVolumenMusica(vol);
    }

    // Guardar volumen y aplicarlo a efectos
    sliderVolumen.addEventListener("input", () => {
      const newVol = parseFloat(sliderVolumen.value);

      // Guardar en localStorage
      localStorage.setItem("volumenMusica", newVol);

      // Música del iframe
      if (audioFrame && audioFrame.contentWindow.setVolumenMusica) {
        audioFrame.contentWindow.setVolumenMusica(newVol);
      }

      // Efectos del menú
      if (window.actualizarVolumenEfectos) {
        window.actualizarVolumenEfectos(newVol);
      }
    });
  }
});

// --- Sistema de mensajes ---
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
}*/

document.addEventListener("DOMContentLoaded", () => {
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

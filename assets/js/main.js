document.addEventListener("DOMContentLoaded", () => {
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

  // Para el Settings del menu principal
  const sliderVolumen = document.getElementById("volumenMusica");

  if (sliderVolumen) {
    // Poner valor inicial
    sliderVolumen.value = localStorage.getItem("volumenMusica") ?? 0.5;

    sliderVolumen.addEventListener("input", () => {
      const audioFrame = document.getElementById("audioFrame");
      if (audioFrame && audioFrame.contentWindow.setVolumenMusica) {
        audioFrame.contentWindow.setVolumenMusica(sliderVolumen.value);
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
}

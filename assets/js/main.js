// js/main.js
/*
// --- Alternar entre login y registro ---
const toggleLink = document.getElementById("toggleLink");
const toggleText = document.getElementById("toggleText");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

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

// --- Login ---
loginForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const datos = new FormData(loginForm);
  const respuesta = await fetch("php/login.php", {
    method: "POST",
    body: datos,
  });

  const resultado = await respuesta.json();

  if (resultado.success) {
    mostrarMensaje("Inicio de sesión correcto. Redirigiendo...", "success");
    setTimeout(() => {
      window.location.href =
        "http://localhost/Hundir-la-flota/juego/menuJuego.php";
    }, 1500);
  } else {
    mostrarMensaje(resultado.message, "error");
  }
});

// --- Registro ---
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

// --- Sistema de mensajes ---
function mostrarMensaje(texto, tipo = "info") {
  const mensaje = document.getElementById("mensaje");
  mensaje.textContent = texto;
  mensaje.className = `mensaje ${tipo} visible`;

  setTimeout(() => {
    mensaje.classList.remove("visible");
    mensaje.classList.add("oculto");
  }, 2500);
}

// --- Botón "Salir" en menú principal ---
document.addEventListener("DOMContentLoaded", () => {
  const btnSalir = document.getElementById("btnSalir");

  if (btnSalir) {
    // para depuración: confirma que el elemento existe
    console.log("btnSalir encontrado en la página");

    btnSalir.addEventListener("click", (e) => {
      e.preventDefault();
      if (confirm("¿Seguro que deseas salir del juego y volver al inicio?")) {
        mostrarMensaje("Saliendo del juego...", "info");

        // redirige al index (ruta relativa desde /juego/)
        setTimeout(() => {
          window.location.href = "../index.php";
        }, 800);
      }
    });
  } else {
    console.log("btnSalir NO encontrado en esta página");
  }
});*/

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

  // --- Sistema de mensajes ---
  function mostrarMensaje(texto, tipo = "info") {
    const mensaje = document.getElementById("mensaje");
    if (!mensaje) return;
    mensaje.textContent = texto;
    mensaje.className = `mensaje ${tipo} visible`;

    setTimeout(() => {
      mensaje.classList.remove("visible");
      mensaje.classList.add("oculto");
    }, 2500);
  }

  // --- Botón "Salir" en menú principal ---
  const btnSalir = document.getElementById("btnSalir");

  if (btnSalir) {
    console.log("✅ Botón Salir detectado");

    btnSalir.addEventListener("click", (e) => {
      e.preventDefault();
      if (confirm("¿Seguro que deseas salir del juego y volver al inicio?")) {
        mostrarMensaje("Saliendo del juego...", "info");
        setTimeout(() => {
          window.location.href = "../index.php";
        }, 1000);
      }
    });
  } else {
    console.log("⚠️ No se encontró btnSalir (esta página no lo tiene)");
  }
});

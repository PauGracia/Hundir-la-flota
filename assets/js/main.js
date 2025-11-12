// js/main.js

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
  alert(resultado.message);

  if (resultado.success) {
    window.location.href = "/juego/juego.html";
  }
});

// --- Registro ---
registerForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const pass1 = document.getElementById("nuevoPassword").value;
  const pass2 = document.getElementById("rePassword").value;

  if (pass1 !== pass2) {
    alert("Las contraseñas no coinciden.");
    return;
  }

  const datos = new FormData(registerForm);
  const respuesta = await fetch("php/registrar.php", {
    method: "POST",
    body: datos,
  });

  const resultado = await respuesta.json();
  alert(resultado.message);
});

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hundir la Flota - Ingreso</title>

    <link
      rel="stylesheet"
      href="./assets/css/styles.css?v=<?php echo time(); ?>"
    />
  </head>

  <body class="app app--login">
    <main class="auth">
      <section class="auth__card">
        <header class="auth__header">
          <h1 class="auth__title">⚓ Hundir la Flota ⚓</h1>
          <p class="auth__subtitle">Inicia sesión para continuar</p>
        </header>

        <!-- FORM LOGIN -->
        <form id="loginForm" class="form form--login" autocomplete="off">
          <div class="form__group">
            <label for="usuario" class="form__label">Usuario</label>
            <input
              type="text"
              id="usuario"
              name="usuario"
              class="form__input"
              required
            />
          </div>

          <div class="form__group">
            <label for="password" class="form__label">Contraseña</label>
            <input
              type="password"
              id="password"
              name="password"
              class="form__input"
              required
            />
          </div>

          <button type="submit" class="btn btn--primary">
            Entrar al juego
          </button>
        </form>

        <!-- FORM REGISTER -->
        <form
          id="registerForm"
          class="form form--register hidden"
          autocomplete="off"
        >
          <div class="form__group">
            <label for="nuevoUsuario" class="form__label">Nuevo usuario</label>
            <input
              type="text"
              id="nuevoUsuario"
              name="usuario"
              class="form__input"
              required autocomplete="username"
            />
          </div>

          <div class="form__group">
            <label for="nuevoPassword" class="form__label">Contraseña</label>
            <input
              type="password"
              id="nuevoPassword"
              name="password"
              class="form__input"
              required autocomplete="current-password"
            />
          </div>

          <div class="form__group">
            <label for="rePassword" class="form__label"
              >Repite la contraseña</label
            >
            <input
              type="password"
              id="rePassword"
              name="rePassword"
              class="form__input"
              required
            />
          </div>

          <button type="submit" class="btn btn--secondary">Crear cuenta</button>
        </form>

        <footer class="auth__footer">
          <p id="toggleText" class="auth__toggle">
            ¿No eres usuario?
            <a href="#" id="toggleLink" class="auth__link">Crea tu cuenta</a>
          </p>
        </footer>
      </section>
    </main>

    <script src="assets/js/main.js"></script>

  </body>
</html>

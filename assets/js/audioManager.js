// --- SISTEMA GLOBAL DE MÚSICA DEL JUEGO ---

// Canciones del juego
const playlist = [
  "/Hundir-la-flota/assets/music/Aylex-Glorious.mp3",
  "/Hundir-la-flota/assets/music/Aylex-Storm.mp3",
  "/Hundir-la-flota/assets/music/Pufino-ThereBeDragons.mp3",
  "/Hundir-la-flota/assets/music/Walen-Champions.mp3",
];

if (!window.audioPlayer) {
  window.audioPlayer = new Audio();
  window.audioPlayer.loop = false;
  window.audioPlayer.volume = localStorage.getItem("volumenMusica")
    ? parseFloat(localStorage.getItem("volumenMusica"))
    : 0.5;

  let currentIndex = 0;

  // Cargar primera canción
  audioPlayer.src = playlist[currentIndex];
  audioPlayer.play().catch(() => {
    console.log("Esperando interacción del usuario para iniciar audio.");
  });

  // Pasar a la siguiente cuando termine
  audioPlayer.addEventListener("ended", () => {
    currentIndex = (currentIndex + 1) % playlist.length;
    audioPlayer.src = playlist[currentIndex];
    audioPlayer.play();
  });

  console.log("% Audio Manager cargado", "color: lightblue");
}

// Función global para cambiar volumen
window.setVolumenMusica = function (valor) {
  audioPlayer.volume = valor;
  localStorage.setItem("volumenMusica", valor);
};

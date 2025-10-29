function generarAudio() {
    const texto = document.getElementById("resultado").value.trim();

    if (!texto) {
        alert("No hay texto para convertir a audio.");
        return;
    }

    // Cancelar cualquier lectura anterior
    if (window.speechSynthesis.speaking || window.speechSynthesis.paused) {
        window.speechSynthesis.cancel();
    }

    const utter = new SpeechSynthesisUtterance(texto);
    utter.lang = "es-MX";   // Puedes usar "es-ES" si prefieres voz española
    utter.rate = 1;
    utter.pitch = 1;
    utter.volume = 1;

    // ✅ Reproduce el audio localmente, sin llamar a PHP ni APIs
    window.speechSynthesis.speak(utter);
}

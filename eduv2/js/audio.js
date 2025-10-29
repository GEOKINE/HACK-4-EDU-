function generarAudio() {
    const texto = document.getElementById("resultado").value.trim();

    if (!texto) {
        alert("No hay texto para convertir a audio.");
        return;
    }

    // Detener cualquier audio anterior
    if (window.speechSynthesis.speaking || window.speechSynthesis.paused) {
        window.speechSynthesis.cancel();
    }

    const utter = new SpeechSynthesisUtterance(texto);
    utter.lang = "es-ES";
    
    // ⚠️ No reproducir automáticamente: se reproducirá al presionar play en el navegador
    // Para Web Speech API, solo se reproduce al llamar speak()
    window.speechSynthesis.speak(utter);
}

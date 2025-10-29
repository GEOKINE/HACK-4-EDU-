// js/generarGuion.js

async function generarGuion() {
    const tema = document.getElementById('tema').value;
    const subtemas = document.getElementById('subtemas').value;
    const duracion = document.getElementById('duracion').value;
    const resultado = document.getElementById('resultado');

    resultado.value = "Generando guion...";

    try {
        const response = await fetch('php/gemini.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `tema=${encodeURIComponent(tema)}&subtemas=${encodeURIComponent(subtemas)}&duracion=${encodeURIComponent(duracion)}`
        });

        const data = await response.json();

        if (data.guion) {
            resultado.value = data.guion;
        } else {
            resultado.value = "No se generó guion.";
        }
    } catch (error) {
        resultado.value = "Error al generar guion: " + error;
    }
}

let utter = null;
let estado = "detenido"; // detenido, leyendo, pausado
let audioBlob = null;

const btnLectura = document.getElementById("btnLectura");
const btnGuardar = document.getElementById("btnGuardar");
const btnDescargar = document.getElementById("btnDescargar");
const mensajeLectura = document.getElementById("mensajeLectura");
const audioPlayer = document.getElementById("audioPlayer");
const modalGuardar = document.getElementById("modalGuardar");
const formGuardar = document.getElementById("formGuardar");

btnLectura.addEventListener("click", toggleLectura);
btnDescargar.addEventListener("click", descargarPodcast);
formGuardar.addEventListener("submit", guardarPodcast);

function toggleLectura() {
    const texto = document.getElementById("resultado").value.trim();
    if (!texto) return alert("No hay texto para leer.");

    if (estado === "detenido") {
        utter = new SpeechSynthesisUtterance(texto);
        utter.lang = "es-MX";
        utter.rate = 1.8;
        utter.volume = 1;

        // Configuración de AudioContext para grabación
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const dest = audioCtx.createMediaStreamDestination();
        const mediaRecorder = new MediaRecorder(dest.stream);
        let chunks = [];

        mediaRecorder.ondataavailable = e => chunks.push(e.data);
        mediaRecorder.onstop = () => {
            audioBlob = new Blob(chunks, { type: "audio/webm" });
            btnDescargar.disabled = false;
            btnGuardar.disabled = false;

            // Preparar reproducción del audio grabado
            const url = URL.createObjectURL(audioBlob);
            audioPlayer.src = url;
            audioPlayer.load();

            mensajeLectura.textContent = "✅ Lectura finalizada y audio grabado";
        };

        // Conectar SpeechSynthesis al MediaStream
        // Nota: el nodo "source" de SpeechSynthesis no es directo, usamos workaround
        const source = audioCtx.createMediaStreamSource(dest.stream);
        source.connect(audioCtx.destination);

        utter.onstart = () => {
            estado = "leyendo";
            btnLectura.textContent = "⏸️ Pausar";
            mensajeLectura.textContent = "▶️ Leyendo...";
            mediaRecorder.start();
        };

        utter.onend = () => {
            estado = "detenido";
            btnLectura.textContent = "▶️ Leer";
            mediaRecorder.stop();
            utter = null;
        };

        speechSynthesis.speak(utter);

    } else if (estado === "leyendo") {
        speechSynthesis.pause();
        estado = "pausado";
        btnLectura.textContent = "▶️ Reanudar";
        mensajeLectura.textContent = "⏸️ Lectura pausada";
    } else if (estado === "pausado") {
        speechSynthesis.resume();
        estado = "leyendo";
        btnLectura.textContent = "⏸️ Pausar";
        mensajeLectura.textContent = "▶️ Leyendo...";
    }
}

function detenerLectura() {
    speechSynthesis.cancel();
    estado = "detenido";
    utter = null;
    btnLectura.textContent = "▶️ Leer";
    mensajeLectura.textContent = "⏹️ Lectura detenida";
}

// Descargar audio grabado
function descargarPodcast() {
    if (!audioBlob) return alert("No hay audio generado aún.");
    const url = URL.createObjectURL(audioBlob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "podcast.webm";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Abrir modal para guardar
btnGuardar.addEventListener("click", () => modalGuardar.style.display = "block");
function cerrarModal() {
    modalGuardar.style.display = "none";
}

// Guardar podcast en servidor
function guardarPodcast(e) {
    e.preventDefault();
    if (!audioBlob) return alert("No hay audio para guardar.");

    const formData = new FormData();
    formData.append("autor", document.getElementById("autor").value);
    formData.append("tema", document.getElementById("temaPodcast").value);
    formData.append("categoria", document.getElementById("categoria").value);
    formData.append("audio", audioBlob, "podcast.webm");

    fetch("php/guardarPodcast.php", { method: "POST", body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                alert("✅ Podcast guardado correctamente");
                cerrarModal();
            } else {
                alert("❌ Error al guardar podcast: " + res.error);
            }
        })
        .catch(err => alert("❌ Error al guardar podcast: " + err));
}

function guardarTextoComoPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const texto = document.getElementById("resultado").value;

    if (!texto.trim()) return alert("No hay texto para guardar.");

    // Dividir texto en líneas largas
    const lineas = doc.splitTextToSize(texto, 180);
    doc.text(lineas, 15, 20);

    doc.save("podcast.pdf");
}

// Asociar al botón
btnGuardar.addEventListener("click", guardarTextoComoPDF);

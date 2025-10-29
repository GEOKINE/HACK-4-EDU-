
const inputDocumento = document.getElementById("inputDocumento");
const textoDocumento = document.getElementById("textoDocumento");
const btnLeerDoc = document.getElementById("btnLeerDoc");
const btnDetenerDoc = document.getElementById("btnDetenerDoc");

let speechUtterance;
let isReading = false;

inputDocumento.addEventListener("change", async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const extension = file.name.split('.').pop().toLowerCase();

    if (extension === "txt") {
        const reader = new FileReader();
        reader.onload = () => {
            textoDocumento.value = reader.result;
        };
        reader.readAsText(file);
    } else if (extension === "pdf") {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        let textoPDF = "";

        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const content = await page.getTextContent();
            const strings = content.items.map(item => item.str);
            textoPDF += strings.join(" ") + "\n\n";
        }

        textoDocumento.value = textoPDF;
    } else {
        alert("Solo se permiten archivos .txt o .pdf");
    }
});

btnLeerDoc.addEventListener("click", () => {
    if (!textoDocumento.value) return alert("Primero carga un documento");

    if (isReading) return; // Evita iniciar varias lecturas
    speechUtterance = new SpeechSynthesisUtterance(textoDocumento.value);
    speechUtterance.rate = 1; // velocidad de lectura
    speechUtterance.pitch = 1; // tono
    speechSynthesis.speak(speechUtterance);
    isReading = true;

    speechUtterance.onend = () => {
        isReading = false;
    };
});

btnDetenerDoc.addEventListener("click", () => {
    if (isReading) {
        speechSynthesis.cancel();
        isReading = false;
    }
});

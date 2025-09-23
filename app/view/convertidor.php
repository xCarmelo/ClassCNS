<?php require_once "../view/header.php"; ?>

<div class="container my-5">
    <h2 class="text-center">🗣️ Texto a Audio</h2>

    <div class="form-group">
        <label for="texto">Escribe el texto:</label>
        <textarea class="form-control" id="texto" rows="4" placeholder="Escribe algo..."></textarea>
    </div>

    <div class="form-group">
        <label for="buscadorVoz">Buscar voz:</label>
        <input type="text" class="form-control" id="buscadorVoz" placeholder="Ejemplo: español, Google, en-US">
    </div>

    <div class="form-group">
        <label>Voces disponibles:</label>
        <div class="form-group mt-3">
            <button class="btn btn-outline-dark btn-sm" onclick="toggleFavoritas()">🔖 Mostrar solo favoritas</button>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Idioma</th>
                    <th>Acción</th>
                    <th>Favorito</th>
                </tr>
            </thead>
            <tbody id="tablaVoces"></tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-secondary btn-sm" onclick="paginaAnterior()">⬅️ Anterior</button>
            <span id="paginadorInfo">Página 1</span>
            <button class="btn btn-secondary btn-sm" onclick="paginaSiguiente()">Siguiente ➡️</button>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-success btn-block" onclick="leerTexto()">🔊 Hablar</button>
        <button class="btn btn-warning btn-block" onclick="cancelarAudio()">❌ Cancelar Audio</button>
        <button class="btn btn-primary btn-block" onclick="descargarAudio()">⬇️ Descargar Audio</button>
        <button class="btn btn-dark btn-block" onclick="descargarDirecto()">⬇️ Descargar sin escuchar</button>
    </div>

    <p id="estado" class="mt-3 text-center text-info" style="display: none;">🎙️ Grabando audio...</p>
</div>

<?php require_once "../view/footer.php"; ?>

<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>

<script>
let recorder;
let audioBlob;
let todasLasVoces = [];
let vozSeleccionada = null;
let paginaActual = 1;
const vocesPorPagina = 10;
let soloFavoritas = false;

// Guardar favoritos en localStorage
function obtenerFavoritos() {
    return JSON.parse(localStorage.getItem("vocesFavoritas") || "[]");
}

function guardarFavoritos(favoritos) {
    localStorage.setItem("vocesFavoritas", JSON.stringify(favoritos));
}

function toggleFavorito(nombreVoz) {
    let favoritos = obtenerFavoritos();
    if (favoritos.includes(nombreVoz)) {
        favoritos = favoritos.filter(n => n !== nombreVoz);
    } else {
        favoritos.push(nombreVoz);
    }
    guardarFavoritos(favoritos);
    mostrarVoces(filtrarVoces());
}

function toggleFavoritas() {
    soloFavoritas = !soloFavoritas;
    paginaActual = 1;
    mostrarVoces(filtrarVoces());
}

// Filtro principal aplicado a todas las funciones
function filtrarVoces() {
    const filtro = document.getElementById("buscadorVoz").value.toLowerCase();
    let voces = todasLasVoces.filter(v =>
        v.name.toLowerCase().includes(filtro) || v.lang.toLowerCase().includes(filtro)
    );
    if (soloFavoritas) {
        const favs = obtenerFavoritos();
        voces = voces.filter(v => favs.includes(v.name));
    }
    return voces;
}

function cargarVoces() {
    todasLasVoces = speechSynthesis.getVoices();
    mostrarVoces(filtrarVoces());
}

function mostrarVoces(voces) {
    const tabla = document.getElementById("tablaVoces");
    tabla.innerHTML = "";

    const totalPaginas = Math.ceil(voces.length / vocesPorPagina);
    const inicio = (paginaActual - 1) * vocesPorPagina;
    const fin = inicio + vocesPorPagina;
    const vocesPagina = voces.slice(inicio, fin);
    const favoritos = obtenerFavoritos();

    vocesPagina.forEach(voz => {
        const fila = document.createElement("tr");

        const tdNombre = document.createElement("td");
        tdNombre.textContent = voz.name;

        const tdIdioma = document.createElement("td");
        tdIdioma.textContent = voz.lang;

        const tdAccion = document.createElement("td");
        const btnSeleccionar = document.createElement("button");
        btnSeleccionar.className = "btn btn-outline-primary btn-sm";
        btnSeleccionar.textContent = "Seleccionar";
        btnSeleccionar.onclick = () => {
            vozSeleccionada = voz;
            alert(`Voz seleccionada: ${voz.name} (${voz.lang})`);
        };
        tdAccion.appendChild(btnSeleccionar);

        const tdFavorito = document.createElement("td");
        const btnFavorito = document.createElement("button");
        btnFavorito.className = "btn btn-sm " + (favoritos.includes(voz.name) ? "btn-warning" : "btn-outline-secondary");
        btnFavorito.innerHTML = favoritos.includes(voz.name) ? "★" : "☆";
        btnFavorito.onclick = () => toggleFavorito(voz.name);
        tdFavorito.appendChild(btnFavorito);

        fila.appendChild(tdNombre);
        fila.appendChild(tdIdioma);
        fila.appendChild(tdAccion);
        fila.appendChild(tdFavorito);
        tabla.appendChild(fila);
    });

    document.getElementById("paginadorInfo").textContent = `Página ${paginaActual} de ${totalPaginas || 1}`;
}

function paginaSiguiente() {
    const totalPaginas = Math.ceil(filtrarVoces().length / vocesPorPagina);
    if (paginaActual < totalPaginas) {
        paginaActual++;
        mostrarVoces(filtrarVoces());
    }
}

function paginaAnterior() {
    if (paginaActual > 1) {
        paginaActual--;
        mostrarVoces(filtrarVoces());
    }
}

document.getElementById("buscadorVoz").addEventListener("input", function () {
    paginaActual = 1;
    mostrarVoces(filtrarVoces());
});

function leerTexto() {
    const texto = document.getElementById("texto").value;
    if (!texto) return alert("Escribe algún texto.");
    if (!vozSeleccionada) return alert("Selecciona una voz.");

    const mensaje = new SpeechSynthesisUtterance(texto);
    mensaje.voice = vozSeleccionada;
    mensaje.rate = 1;

    const audioContext = new AudioContext();
    const destination = audioContext.createMediaStreamDestination();
    const source = audioContext.createMediaStreamSource(destination.stream);
    recorder = new RecordRTC(destination.stream, { type: 'audio' });

    recorder.startRecording();
    document.getElementById("estado").style.display = "block";

    mensaje.onend = () => {
        recorder.stopRecording(() => {
            audioBlob = recorder.getBlob();
            document.getElementById("estado").style.display = "none";
        });
    };

    window.speechSynthesis.speak(mensaje);
}

function cancelarAudio() {
    window.speechSynthesis.cancel();
    document.getElementById("estado").style.display = "none";
}

function descargarAudio() {
    if (!audioBlob) return alert("Primero reproduce y graba el audio.");
    const url = URL.createObjectURL(audioBlob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "audio.mp3";
    a.click();
}

// Cargar voces al iniciar
if (typeof speechSynthesis !== "undefined") {
    speechSynthesis.onvoiceschanged = cargarVoces;
}


function descargarDirecto() {
    const texto = document.getElementById("texto").value;
    if (!texto) return alert("Escribe algún texto.");
    if (!vozSeleccionada) return alert("Selecciona una voz primero.");

    const mensaje = new SpeechSynthesisUtterance(texto);
    mensaje.voice = vozSeleccionada;
    mensaje.rate = 1;

    const audioContext = new AudioContext();
    const destination = audioContext.createMediaStreamDestination();
    const source = audioContext.createMediaStreamSource(destination.stream);
    recorder = new RecordRTC(destination.stream, { type: 'audio' });

    recorder.startRecording();
    document.getElementById("estado").style.display = "block";

    mensaje.onend = () => {
        recorder.stopRecording(() => {
            const blob = recorder.getBlob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "audio.mp3";
            a.click();
            document.getElementById("estado").style.display = "none";
        });
    };

    // El truco es que aún usamos speak() para forzar el proceso,
    // pero el usuario no tiene que escucharlo si baja el volumen
    speechSynthesis.speak(mensaje);
}

</script>

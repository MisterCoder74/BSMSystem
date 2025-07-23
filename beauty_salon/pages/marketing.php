<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
die('Direct access not permitted');
}
?>
<h1>Pagina Marketing</h1>

<div class="mainpanel">
<!-- Lato sinistro -->
<div class="left-side">
<div class="prompt-area">
<label for="prompt">Inserisci il prompt:</label>
<textarea id="prompt" placeholder="Scrivi qui il testo..."></textarea>
</div>
<div id="generatedtext"></div>
<button id="generateBtn">Generate</button>
<div class="bottom-buttons" style="display: none;">
<button id="generateAd">Generate Ad</button>
<button id="resetBtn">Reset</button>
</div>        
</div>

<!-- Lato destro -->
<div class="right-side">
<h3>Anteprime immagini</h3>
<div class="thumbnails">
<?php
// Sezione corretta per la gestione delle immagini
$scheme = isset($_SERVER['REQUEST_SCHEME'])
    ? $_SERVER['REQUEST_SCHEME']
    : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');

$host = $_SERVER['HTTP_HOST'];

// Percorso URL pubblico
$imagesUrl = 'assets/imglibrary/';

// percorso filesystem delle immagini
$imagesDir = BASE_PATH . '/assets/imglibrary/';
$apiKeyDir = BASE_PATH . '/data/';
$images = [];
if (is_dir($imagesDir)) {
    $images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
}

if (count($images) === 0) {
    echo '<div class="no-images">No images found</div>';
} else {
    echo '<table class="gallerytable">';
    echo '<tr class="galleryrow">';

    $count = 0;
    foreach ($images as $imgPath) {
        $imgName = basename($imgPath);
        $idRadio = 'img_' . $count;
        $imgSrc = $imagesUrl . $imgName;
        
        echo '<td class="gallerycell">';
        echo '<img src="' . htmlspecialchars($imgSrc) . '" alt="Preview" />';
        echo '<br />';
        echo '<input type="radio" name="image_select" id="' . $idRadio . '" value="' . htmlspecialchars($imgName) . '" />';
        echo '<label for="' . $idRadio . '">Seleziona</label>';
        echo '</td>';

        $count++;
        // Controlliamo se abbiamo riempito 3 colonne
        if ($count % 3 == 0 && $count < count($images)) {
            echo '</tr><tr>';
        }
    }

    echo '</tr></table>';
}
$apiKey = trim(file_get_contents($apiKeyDir . 'marketing.txt')); 
?>
</div>
</div>
</div>

<!-- Bottoni sotto tutto -->

<!-- Sezione Generato e iframe -->
<div id="iframe-container"></div>
<!-- Contenitore per la card generata -->
<div id="adContent" style="margin-top:20px;"></div>

<!-- Script JavaScript -->
<script>
// Funzione per generare il contenuto
const apiKey = "<?php echo htmlspecialchars($apiKey); ?>";

document.getElementById('generateBtn').addEventListener('click', async function() {
if (!apiKey || apiKey === "") {
alert("Nessuna API Key trovata");
return;
}
document.querySelector(".bottom-buttons").style.display = "block";
const userInput = document.getElementById('prompt').value.trim();
if (userInput === "") return;

// Mostra caricamento
document.getElementById('generatedtext').innerHTML = '<i>Generating...</i>';

try {
const response = await fetch("https://api.openai.com/v1/chat/completions", {
method: "POST",
headers: {
"Content-Type": "application/json",
"Authorization": "Bearer " + apiKey
},
body: JSON.stringify({
model: "gpt-4.1-nano",
messages: [{ role: "user", content: userInput }]
})
});

if (!response.ok) throw new Error("Errore API");

const data = await response.json();

const reply = data.choices[0].message.content.trim();

document.getElementById('generatedtext').innerText = reply;

} catch (err) {
document.getElementById('generatedtext').innerText = 'Errore: ' + err.message;
}

});

// Funzione reset
document.getElementById('resetBtn').addEventListener('click', function() {
document.getElementById('prompt').value = '';
document.getElementById('generatedtext').innerText = '';
document.getElementById('adContent').innerHTML = '';
document.getElementById('iframe-container').innerHTML = '';
});

document.getElementById('generateAd').addEventListener('click', function() {
const text = document.getElementById('generatedtext').innerText.trim();
const selectedImg = document.querySelector('input[name="image_select"]:checked');

if(!text){
alert('Per favore, scrivi un prompt.');
return;
}
if(!selectedImg){
alert('Seleziona un\'immagine.');
return;
}

// Genera pagina HTML
    const selectedImgName = selectedImg.value;
    const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
    const imgSrc = baseUrl + "/assets/imglibrary/" + selectedImgName;      

const pageContent = `
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BSMS AI Generated Ad</title>
<style>
* {
margin: 0;
padding: 0;
box-sizing: border-box;
}
body {
font-family: Arial, sans-serif;
padding: 20px;
background-color: #fff;
color: #000;
}
.container {
max-width: 800px;
margin: auto;
border: 1px solid #f0a500;
padding: 20px;
}
h1 {
color: #f0a500;
text-align: center;
}
img {
max-width: 40rem;
height: auto;
display: block;
margin: 1rem auto;
border-radius: 12px;
}
p {
font-size: 1.2em;
line-height: 1.5em;
margin-top: 20px;
text-align: center;
}
</style>
</head>
<body>
<div class="container">
    <h1>Promozione</h1>
    <img src="${imgSrc}" alt="Promozione"/>
    <p>${text}</p>
</div>
</body>
</html>
`;

 // Creare Blob e iframe
    const blob = new Blob([pageContent], {type: 'text/html'});
    const url = URL.createObjectURL(blob);

    // Inserisci iframe
    document.getElementById('iframe-container').innerHTML = '<iframe src="' + url + '" id="adIframe"></iframe>';

    // Aggiungi bottone download
    let existingDownloadBtn = document.getElementById('downloadBtn');
    if(existingDownloadBtn){
        existingDownloadBtn.remove();
    }
    const downloadBtn = document.createElement('button');
    downloadBtn.id = 'downloadBtn';
    downloadBtn.innerText = 'Download';
    downloadBtn.style.marginTop = '10px';
    downloadBtn.onclick = function() {
        const a = document.createElement('a');
        a.href = url;
        a.download = 'ad.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    document.querySelector('.bottom-buttons').appendChild(downloadBtn);
});
</script>

<style>

h1 {
text-align: center;
margin-bottom: 20px;
color: white; /* Colore di accento, esempio arancione */
}

.mainpanel {
        display: flex;
        width: 100%;
        justify-content: space-between;
        align-items: start;
        }

/* Lato sinistro */
.left-side {
width: 45%;
display: flex;
flex-direction: column;
}

/* Prompt e generazione */
.prompt-area {
display: flex;
flex-direction: column;
margin-bottom: 10px;
}

label {
margin-bottom: 5px;
}

textarea {
width: 100%;
height: 100px;
padding: 10px;
border: 2px solid #ff758c;
border-radius: 4px;
resize: vertical;
}

#generatedtext {
border: 2px solid #ff758c;
min-height: 150px;
max-height: 400px;
overflow-y: scroll;
padding: 10px;
margin-top: 10px;
background-color: #fff;
color: #000;
}

button {
background: linear-gradient(to right, #ff758c, #ff7eb3);
width: 100%;  
color: white;
border: none;
padding: 10px 20px;
border-radius: 4px;
cursor: pointer;
margin-top: 10px;
font-weight: bold;
}

button:hover {
background-color: #d18c00;
}

/* Lato destro - anteprime immagini */
.right-side {
        padding: 2px;
background-color: violet;
width: 45%;
display: flex;
flex-direction: column;
}

.thumbnails {
overflow-x: auto;
display: flex;
justify-content: space-between;        
gap: 6px;
padding-bottom: 4px;
}

.gallerytable {
width: 90%;
border-collapse: collapse;
}
.galleryrow {
padding: 4px;
}
.gallerycell {
text-align: center;
vertical-align: top;
padding: 0;
}
tr:hover {
      background-color: violet;  
        }        

img {
width: 80px;
height: 80px;
object-fit: cover;
border: 2px solid #ccc;
border-radius: 4px;
}
img:hover {
transform: scale(1.1);
        }        

/* Se non ci sono immagini */
.no-images {
text-align: center;
padding: 20px;
font-size: 1.2em;
font-weight: bold;
}

/* Pulsanti sotto tutto, centrati */
.bottom-buttons {
text-align: center;
margin-top: 20px;
}

.bottom-buttons button {
background: linear-gradient(to right, #ff758c, #ff7eb3);
width: 30%;  
color: white;
border: none;
padding: 10px 20px;
border-radius: 4px;
cursor: pointer;
margin-top: 10px;
font-weight: bold;
}

.bottom-buttons button:hover {
background-color: #d18c00;
}        
        
        

/* Div per iframe e download */
#iframe-container {
        margin: 10px auto;
        text-align: center;
}
iframe {
        margin: 10px auto;
        text-align: center;
width: 80%;
height: 600px;
border: 2px solid #ff758c;
}

</style>

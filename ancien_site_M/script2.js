// Attendre que le DOM soit chargé avant d'exécuter le script
document.addEventListener("DOMContentLoaded", function() {
    const ipCibleContainer = document.getElementById('ip-cible-container');
    const nmapContainer = document.getElementById('nmap-container');
    const validateBtn = document.getElementById('validate-btn');

    // Cacher les champs au chargement
    if (ipCibleContainer) ipCibleContainer.style.display = 'none';
    if (nmapContainer) nmapContainer.style.display = 'none';

    // Vérifier que le bouton de validation existe
    if (validateBtn) {
        validateBtn.addEventListener('click', function() {
            const pingCheckbox = document.getElementById('ping-checkbox');
            const nmapCheckbox = document.getElementById('nmap-checkbox');

            // Afficher ou cacher le champ IP Cible en fonction de la sélection
            if (pingCheckbox.checked || nmapCheckbox.checked) {
                ipCibleContainer.style.display = 'block'; // Afficher IP Cible si Ping ou Nmap est sélectionné
            } else {
                ipCibleContainer.style.display = 'none'; // Cacher IP Cible si aucune case n'est cochée
            }

            // Afficher ou cacher les champs Nmap
            if (nmapCheckbox.checked) {
                nmapContainer.style.display = 'block';
            } else {
                nmapContainer.style.display = 'none';
            }
        });
    } else {
        console.error("Erreur : Bouton validate-btn introuvable !");
    }
});

// Fonction pour lancer les tests (Ping ou Nmap)
function startPentest() {
    const ip = document.getElementById("ip").value.trim();
    const resultText = document.getElementById("result-text");
    const pingCheckbox = document.getElementById("ping-checkbox");
    const nmapCheckbox = document.getElementById("nmap-checkbox");

    // Vérifier si une IP est renseignée
    if (!ip) {
        resultText.textContent = "Veuillez entrer une IP.";
        return;
    }

    // Vérifier si au moins un test est sélectionné
    if (!pingCheckbox.checked && !nmapCheckbox.checked) {
        resultText.textContent = "Veuillez sélectionner au moins un test.";
        return;
    }

    // Exécuter le test Ping
    if (pingCheckbox.checked) {
        resultText.textContent = "En cours de ping...";
        fetch(`ping.php?ip=${ip}`)
            .then(response => response.text())
            .then(data => {
                resultText.innerHTML = data;
            })
            .catch(error => {
                console.error("Erreur lors du ping : ", error);
                resultText.textContent = "Erreur de ping.";
            });
    }

    // Exécuter le test Nmap
    if (nmapCheckbox.checked) {
        resultText.textContent = "Scan Nmap en cours...";
        const nmapPorts = document.getElementById("nmap-ports").value || "80,443";

        fetch(`nmap.php?ip=${ip}&ports=${nmapPorts}`)
            .then(response => response.text())
            .then(data => {
                resultText.innerHTML = data;
            })
            .catch(error => {
                console.error("Erreur lors du scan Nmap : ", error);
                resultText.textContent = "Erreur de scan Nmap.";
            });
    }
}

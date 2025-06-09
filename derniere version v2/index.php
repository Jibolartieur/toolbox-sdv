<?php
session_start();
require_once 'php/config.php';
requireLogin();
?>
<!DOCTYPE html>
<!-- fichier index.php -->
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Toolbox</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-info {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
            color: #6B5ECD;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Security Toolbox</h1>
            <div class="user-info">
                <span>Connecté en tant que : <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </header>
        
        <main>
            <div class="search-container">
                <div class="dynamic-inputs">
        <div id="ip-input-container" style="display: none;">
            <input type="text" id="ip-input" placeholder="Enter IP address (e.g. 8.8.8.8)" class="main-input">
        </div>

        <div id="url-input-container" style="display: none;">
            <input type="text" id="url-input" placeholder="Enter domain name (e.g. example.com) or URL (e.g. https://example.com)" class="main-input">
        </div>
    </div>

            </div>

            <div class="tools-container">
                <!-- Outils de base -->
                <div class="tool-card" data-tool="ping">
                    <div class="tool-header">
                        <h3>Ping</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Test connectivity to target</p>
                </div>
                
                <div class="tool-card" data-tool="traceroute">
                    <div class="tool-header">
                        <h3>Traceroute</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Trace network path to target</p>
                </div>

                <!-- Outils d'énumération -->
                <div class="tool-card" data-tool="nmap">
                    <div class="tool-header">
                        <h3>Nmap</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Port scanning and service detection</p>
                </div>

                <div class="tool-card" data-tool="nikto">
                    <div class="tool-header">
                        <h3>Nikto</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Web server vulnerability scanner</p>
                </div>

                <!-- Outils Web -->
                <div class="tool-card" data-tool="dirb">
                    <div class="tool-header">
                        <h3>Dirb</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Web content scanner and directory brute force</p>
                </div>

                <div class="tool-card" data-tool="webcheck">
                    <div class="tool-header">
                        <h3>Web-Check</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Comprehensive web security analysis</p>
                </div>

                <!-- Outils d'information -->
                <div class="tool-card" data-tool="whois">
                    <div class="tool-header">
                        <h3>Whois</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Domain registration information lookup</p>
                </div>

                <div class="tool-card" data-tool="dig">
                    <div class="tool-header">
                        <h3>Dig</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>DNS lookup and analysis</p>
                </div>

                <!-- Outils de sécurité supplémentaires -->
                <div class="tool-card" data-tool="sslscan">
                    <div class="tool-header">
                        <h3>SSLScan</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>SSL/TLS configuration analysis</p>
                </div>

                <div class="tool-card" data-tool="nuclei">
                    <div class="tool-header">
                        <h3>Nuclei</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Template-based vulnerability scanner</p>
                </div>

                <div class="tool-card" data-tool="subfinder">
                    <div class="tool-header">
                        <h3>Subfinder</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Subdomain discovery tool</p>
                </div>

                <div class="tool-card" data-tool="whatweb">
                    <div class="tool-header">
                        <h3>WhatWeb</h3>
                        <label class="checkbox-wrapper">
                            <input type="checkbox" class="tool-checkbox">
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                    <p>Web technology identification</p>
                </div>
            </div>

            <div class="action-buttons">
                <button id="run-selected" class="run-scan-btn">Run Selected Tools</button>
                <button id="generate-pdf-btn" class="run-scan-btn">Generate PDF Report</button>
                <button id="clear-results-btn" class="run-scan-btn">Clear Results</button>
            </div>
            
            <div class="results-container">
                <h2>Scan Results</h2>
                <div id="results-output"></div>
            </div>

            <div class="results-container">
                <h2>Scan History</h2>
                <div id="scan-history"></div>
            </div>
        </main>
    </div>
    
    <script src="js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
document.getElementById('generate-pdf-btn').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF();

  // Récupère la cible saisie (IP ou URL)
  let target = '';
  const ipInput = document.getElementById('ip-input');
  const urlInput = document.getElementById('url-input');
  
  if (ipInput && ipInput.style.display !== 'none') {
    target = ipInput.value.trim();
  } else if (urlInput && urlInput.style.display !== 'none') {
    target = urlInput.value.trim();
  }

  // Récupère les résultats affichés
  const results = document.getElementById('results-output').innerText || 'Aucun résultat disponible';

  // Ajout du titre
  pdf.setFontSize(16);
  pdf.text("Rapport d'Audit de Sécurité", 105, 20, null, null, 'center');

  // Ajout de la cible
  pdf.setFontSize(12);
  pdf.text(`Cible : ${target}`, 10, 40);

  // Ajout de la date du jour
  pdf.text(`Date : ${new Date().toLocaleDateString()}`, 10, 50);

  // Résultats
  pdf.setFontSize(10);
  pdf.text("Résultats :", 10, 65);

  // Divise les résultats en plusieurs lignes pour tenir dans la page
  const splitResults = pdf.splitTextToSize(results, 180);
  pdf.text(splitResults, 10, 75);

  // Télécharge le PDF automatiquement
  pdf.save('rapport_securite.pdf');
});
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Toolbox</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Security Toolbox</h1>
        </header>
        
        <main>
            <div class="search-container">
                <input type="text" id="target-input" placeholder="8.8.8.8" class="main-input">
            </div>

            <div class="tools-grid">
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
                <button id="generate-docx-btn" class="run-scan-btn">Generate DOCX Report</button>
            </div>
            
            <div class="results-container">
                <h2>Scan Results</h2>
                <div id="results-output"></div>
            </div>
        </main>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>

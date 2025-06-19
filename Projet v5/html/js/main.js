// Fichier : main.js

// Fonction pour formater joliment les résultats selon l'outil
function formatResult(tool, output) {
    if (!output) return '<em>Aucun résultat</em>';
    // PING
    if (tool === 'ping') {
        // Extraire les lignes de réponse
        const lines = output.split(/\r?\n/).filter(l => l.trim());
        const dataLines = lines.filter(l => l.match(/^\d+ bytes from|^64 bytes from|icmp_seq/));
        let table = '<table class="result-table"><thead><tr><th>Seq</th><th>TTL</th><th>Time</th></tr></thead><tbody>';
        dataLines.forEach(line => {
            const match = line.match(/icmp_seq=(\d+).*ttl=(\d+).*time=([\d.]+) ms/);
            if (match) {
                table += `<tr><td>${match[1]}</td><td>${match[2]}</td><td>${match[3]} ms</td></tr>`;
            }
        });
        table += '</tbody></table>';
        // Statistiques
        const stats = lines.find(l => l.includes('packet loss')) || '';
        return table + `<div class="result-stats">${stats}</div>`;
    }
    // TRACEROUTE
    if (tool === 'traceroute') {
        const hops = output.split(/\r?\n/).slice(1).filter(l => l.trim());
        let list = '<ol class="result-list">';
        hops.forEach(hop => list += `<li>${hop}</li>`);
        list += '</ol>';
        return list;
    }
    // NMAP
    if (tool === 'nmap') {
        const portLines = output.split(/\r?\n/).filter(l => l.match(/^[0-9]+\/tcp/));
        if (portLines.length) {
            let table = '<table class="result-table"><thead><tr><th>Port</th><th>State</th><th>Service</th></tr></thead><tbody>';
            portLines.forEach(line => {
                const cols = line.split(/\s+/);
                table += `<tr><td>${cols[0]}</td><td>${cols[1]}</td><td>${cols[2] || ''}</td></tr>`;
            });
            table += '</tbody></table>';
            return table;
        }
    }
    // WHOIS
    if (tool === 'whois') {
        // Mettre en gras les champs importants
        return output.replace(/(Registrar:|Creation Date:|Expiry Date:|Name Server:|Registrant Organization:|Domain Status:)/g, '<strong>$1</strong>');
    }
    // SSLSCAN
    if (tool === 'sslscan') {
        // Afficher les ciphers dans un tableau
        const cipherLines = output.split(/\r?\n/).filter(l => l.match(/^\s+TLS/));
        if (cipherLines.length) {
            let table = '<table class="result-table"><thead><tr><th>Protocol</th><th>Cipher</th><th>Bits</th></tr></thead><tbody>';
            cipherLines.forEach(line => {
                const cols = line.trim().split(/\s+/);
                table += `<tr><td>${cols[0]}</td><td>${cols[1]}</td><td>${cols[2]}</td></tr>`;
            });
            table += '</tbody></table>';
            return table;
        }
    }
    // WHATWEB
    if (tool === 'whatweb') {
        // Afficher les technologies comme badges
        const techs = output.split(',').map(t => t.trim()).filter(Boolean);
        return techs.map(t => `<span class="badge">${t}</span>`).join(' ');
    }
    // Résultat brut par défaut
    return `<pre class="result-output">${output}</pre>`;
}

// Fonction pour charger l'historique des scans
async function loadScanHistory() {
    try {
        const response = await fetch('php/get_results.php');
        const data = await response.json();
        
        if (data.success) {
            const historyContainer = document.getElementById('scan-history');
            historyContainer.innerHTML = ''; // Vider le conteneur
            
            data.results.forEach(result => {
                const resultElement = document.createElement('div');
                resultElement.className = 'result-block';
                resultElement.innerHTML = `
                    <div class="result-header">
                        <span class="tool-name">${result.tool_name}</span>
                        <span class="target">${result.target}</span>
                        <span class="date">${new Date(result.created_at).toLocaleString()}</span>
                    </div>
                    <div class="result-formatted">${formatResult(result.tool_name, result.output)}</div>
                `;
                historyContainer.appendChild(resultElement);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement de l\'historique:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Sélection des éléments DOM principaux
    const toolCheckboxes = document.querySelectorAll('.tool-checkbox');
    const runSelectedBtn = document.getElementById('run-selected');
    const resultsOutput = document.getElementById('results-output');
    const generatePdfButton = document.getElementById('generate-pdf-btn');
    const generateDocxButton = document.getElementById('generate-docx-btn');

    const ipInput = document.getElementById('ip-input');
    const urlInput = document.getElementById('url-input');
    const ipContainer = document.getElementById('ip-input-container');
    const urlContainer = document.getElementById('url-input-container');

    // Définition des outils qui utilisent soit l'IP soit l'URL
    const ipTools = ['ping', 'traceroute', 'nmap', 'nikto', 'dig', 'sslscan', 'nuclei'];
    const urlTools = ['gobuster', 'webcheck', 'whatweb', 'whois', 'subfinder'];

    // Active ou désactive le bouton "Run" selon les cases cochées
    function updateRunButton() {
        const hasChecked = Array.from(toolCheckboxes).some(cb => cb.checked);
        runSelectedBtn.disabled = !hasChecked;
    }

    // Affiche ou cache les champs IP/URL en fonction des outils sélectionnés
    function updateInputVisibility() {
        const selectedTools = Array.from(toolCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.closest('.tool-card').dataset.tool);

        const showIP = selectedTools.some(tool => ipTools.includes(tool));
        const showURL = selectedTools.some(tool => urlTools.includes(tool));

        ipContainer.style.display = showIP ? 'block' : 'none';
        urlContainer.style.display = showURL ? 'block' : 'none';
    }

    // Écoute les changements de sélection d'outil
    toolCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateRunButton();
            updateInputVisibility();
        });
    });

    updateRunButton();

    // Charger l'historique au chargement de la page
    loadScanHistory();

    // Exécute les outils sélectionnés
    runSelectedBtn.addEventListener('click', async () => {
        const selectedTools = Array.from(toolCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.closest('.tool-card').dataset.tool);

        const ip = ipInput?.value.trim();
        const url = urlInput?.value.trim();

        const needsIP = selectedTools.some(tool => ipTools.includes(tool));
        const needsURL = selectedTools.some(tool => urlTools.includes(tool));

        // Validation des champs requis
        if (needsIP && !ip) {
            alert('Please enter an IP address.');
            return;
        }

        if (needsURL && !url) {
            alert('Please enter a URL.');
            return;
        }

        // Désactive le bouton pendant l'exécution
        runSelectedBtn.disabled = true;
        runSelectedBtn.textContent = 'Scanning...';

        if (!resultsOutput.textContent) {
            resultsOutput.textContent = 'Starting scans...\n';
        }

        try {
            // Lance chaque outil en parallèle
            const promises = selectedTools.map(async tool => {
                let target = '';
                if (ipTools.includes(tool)) {
                    target = ip;
                } else if (urlTools.includes(tool)) {
                    target = url;
                }
            
                
            
                const response = await fetch('php/execute.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tool: tool,
                        target: target
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // ======= Ici, il manque cette ligne =======
                const result = await response.json();
                
                if (tool === 'gobuster') {
                    // Nettoyage des séquences d'effacement et des couleurs ANSI
                    let cleanOutput = result.output.replace(/\[\d+K/g, '');
                    cleanOutput = cleanOutput.replace(/\x1b\[[0-9;]*m/g, '');
                    
                    // Ligne de commande affichée en entête
                    const commandLine = `gobuster dir -u ${target} -w /usr/share/dirb/wordlists/common.txt -q`;
                    
                    // Mettre chaque résultat sur une nouvelle ligne
                    cleanOutput = cleanOutput.replace(/\]\[/g, ']\n');
                    
                    return {
                        tool: tool,
                        output: `${commandLine}\n${cleanOutput}`
                    };
                    
                } else {
                    return {
                        tool: tool,
                        output: result.output
                    };
                }
                
                
            });
            

            const results = await Promise.all(promises);

            // Affiche les résultats
            const newContent = results.map(result =>
                `<pre><strong>=== ${result.tool.toUpperCase()} Results ===</strong>\n${result.output}</pre>`
            ).join('');
            
            // Ajoute les nouveaux résultats en haut
            resultsOutput.innerHTML = newContent + resultsOutput.innerHTML;

            // Recharger l'historique après le scan
            await loadScanHistory();

        } catch (error) {
            resultsOutput.textContent += `\nError: ${error.message}\n`;
        } finally {
            runSelectedBtn.disabled = false;
            runSelectedBtn.textContent = 'Run Selected Tools';
            updateRunButton();
        }
    });

    const clearResultsBtn = document.getElementById('clear-results-btn');
    clearResultsBtn.addEventListener('click', function () {
        const confirmed = confirm("Voulez-vous vraiment effacer tous les résultats ?");
        if (confirmed) {
            resultsOutput.innerHTML = '';
        }
    });

    // Boutons pour génération de rapports
    generatePdfButton.addEventListener('click', function () {
        const ip = ipInput?.value.trim();
        const url = urlInput?.value.trim();
        const target = ip || url;
        if (!target) {
            alert('Please enter a target to generate the report.');
            return;
        }
        generateReport('pdf', target);
    });

    generateDocxButton.addEventListener('click', function () {
        const ip = ipInput?.value.trim();
        const url = urlInput?.value.trim();
        const target = ip || url;
        if (!target) {
            alert('Please enter a target to generate the report.');
            return;
        }
        generateReport('docx', target);
    });

    // Fonction commune de génération de rapport
    function generateReport(format, target) {
        console.log('Generating', format.toUpperCase(), 'report for target:', target);
        fetch(`php/generate_report.php?format=${format}&target=${encodeURIComponent(target)}`)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `security_audit_report.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error generating report:', error));
    }


    function displayResults(tool, target, output) {
        const resultsContainer = document.getElementById('results-output');
        if (resultsContainer) {
            resultsContainer.innerHTML = `<pre><strong>=== ${tool.toUpperCase()} Results for ${target} ===</strong>\n${output}</pre>` + resultsContainer.innerHTML;
        }
    }

    
});

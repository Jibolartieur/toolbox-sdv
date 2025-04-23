// Fichier : main.js

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
    const urlTools = ['dirb', 'webcheck', 'whatweb', 'whois', 'subfinder'];

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

    // Écoute les changements de sélection d’outil
    toolCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateRunButton();
            updateInputVisibility();
        });
    });

    updateRunButton();

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

                const result = await response.json();
                return {
                    tool: tool,
                    output: result.output
                };
            });

            const results = await Promise.all(promises);

            // Affiche les résultats
            resultsOutput.textContent += results.map(result =>
                `\n=== ${result.tool.toUpperCase()} Results ===\n${result.output}\n`
            ).join('');

            resultsOutput.scrollTop = resultsOutput.scrollHeight;

        } catch (error) {
            resultsOutput.textContent += `\nError: ${error.message}\n`;
        } finally {
            runSelectedBtn.disabled = false;
            runSelectedBtn.textContent = 'Run Selected Tools';
            updateRunButton();
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
});

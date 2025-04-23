document.addEventListener('DOMContentLoaded', () => {
    const targetInput = document.getElementById('target-input');
    const resultsOutput = document.getElementById('results-output');
    const runSelectedBtn = document.getElementById('run-selected');
    const toolCheckboxes = document.querySelectorAll('.tool-checkbox');
    const generatePdfButton = document.getElementById('generate-pdf-btn');
    const generateDocxButton = document.getElementById('generate-docx-btn');
    
    // Mettre à jour l'état du bouton en fonction des cases cochées
    function updateRunButton() {
        const hasChecked = Array.from(toolCheckboxes).some(cb => cb.checked);
        runSelectedBtn.disabled = !hasChecked;
    }
    
    // Ajouter les écouteurs d'événements aux cases à cocher
    toolCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateRunButton);
    });
    
    // Initialiser l'état du bouton
    updateRunButton();
    
    runSelectedBtn.addEventListener('click', async () => {
        const target = targetInput.value.trim();
        
        if (!target) {
            alert('Please enter a target IP or hostname.');
            targetInput.focus();
            return;
        }
        
        const selectedTools = Array.from(toolCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.closest('.tool-card').dataset.tool);
        
        if (selectedTools.length === 0) {
            alert('Please select at least one tool.');
            return;
        }
        
        runSelectedBtn.disabled = true;
        runSelectedBtn.textContent = 'Scanning...';
        
        if (!resultsOutput.textContent) {
            resultsOutput.textContent = 'Starting scans...\n';
        }
        
        try {
            const promises = selectedTools.map(async tool => {
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
    
    // Permettre l'utilisation de la touche Enter dans le champ de saisie
    targetInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !runSelectedBtn.disabled) {
            runSelectedBtn.click();
        }
    });

    generatePdfButton.addEventListener('click', function() {
        const target = targetInput.value;
        if (!target) {
            alert('Please enter a target to generate the report.');
            return;
        }
        generateReport('pdf', target);
    });

    generateDocxButton.addEventListener('click', function() {
        const target = targetInput.value;
        if (!target) {
            alert('Please enter a target to generate the report.');
            return;
        }
        generateReport('docx', target);
    });

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

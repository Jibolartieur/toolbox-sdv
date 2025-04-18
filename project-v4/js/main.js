async function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorElement = document.getElementById('loginError');

    try {
        const response = await fetch('/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        
        if (response.ok) {
            window.location.reload();
        } else {
            errorElement.textContent = data.error || 'Login failed';
        }
    } catch (error) {
        errorElement.textContent = 'An error occurred. Please try again.';
    }
}

async function handleRegister(event) {
    event.preventDefault();
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;
    const errorElement = document.getElementById('registerError');

    try {
        const response = await fetch('/api/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        
        if (response.ok) {
            // Auto-login after successful registration
            const loginResponse = await fetch('/api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            if (loginResponse.ok) {
                window.location.reload();
            }
        } else {
            errorElement.textContent = data.error || 'Registration failed';
        }
    } catch (error) {
        errorElement.textContent = 'An error occurred. Please try again.';
    }
}

function toggleForms() {
    const loginBox = document.querySelector('.login-box');
    const registerBox = document.getElementById('registerBox');
    
    if (registerBox.style.display === 'none') {
        loginBox.style.display = 'none';
        registerBox.style.display = 'block';
    } else {
        loginBox.style.display = 'block';
        registerBox.style.display = 'none';
    }
}

function handleLogout() {
    window.location.href = '/api/logout.php';
}

async function runScan(tool) {
    const target = document.getElementById('target').value;
    if (!target) {
        alert('Please enter a target IP address');
        return;
    }

    try {
        const response = await fetch('/api/scan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ tool, target })
        });

        const data = await response.json();
        
        if (response.ok) {
            await loadScanHistory();
        } else {
            alert(data.error || 'Scan failed');
        }
    } catch (error) {
        alert('An error occurred while running the scan');
    }
}

async function loadScanHistory() {
    try {
        const response = await fetch('/api/history.php');
        const results = await response.json();
        
        const container = document.getElementById('scanResults');
        container.innerHTML = results.map(result => `
            <div class="scan-result">
                <h4>${result.tool} - ${result.target}</h4>
                <p>Time: ${new Date(result.timestamp).toLocaleString()}</p>
                <pre>${result.output}</pre>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load scan history:', error);
    }
}

// Load scan history when dashboard is loaded
if (document.querySelector('.dashboard')) {
    loadScanHistory();
}
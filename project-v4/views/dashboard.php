<div class="dashboard">
    <nav class="top-nav">
        <h1>Security Toolbox</h1>
        <button onclick="handleLogout()">Logout</button>
    </nav>
    
    <main class="main-content">
        <div class="scan-controls">
            <input type="text" id="target" placeholder="Enter target IP address" class="target-input">
            
            <div class="tools-grid">
                <div class="tool-card">
                    <h3>Ping</h3>
                    <p>Test connectivity to target</p>
                    <button onclick="runScan('ping')">Run Scan</button>
                </div>
                
                <div class="tool-card">
                    <h3>Nmap</h3>
                    <p>Port scanning and service detection</p>
                    <button onclick="runScan('nmap')">Run Scan</button>
                </div>
                
                <div class="tool-card">
                    <h3>Nikto</h3>
                    <p>Web server scanner</p>
                    <button onclick="runScan('nikto')">Run Scan</button>
                </div>
            </div>
        </div>

        <div class="results-section">
            <h2>Scan Results</h2>
            <div id="scanResults" class="results-container"></div>
        </div>
    </main>
</div>
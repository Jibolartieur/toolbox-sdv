<div class="login-container">
    <div class="login-box">
        <h1>Security Toolbox</h1>
        <form id="loginForm" onsubmit="return handleLogin(event)">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="form-footer">
            <p>Don't have an account? <a href="#" onclick="toggleForms()">Register</a></p>
        </div>
        <div id="loginError" class="error-message"></div>
    </div>

    <div class="login-box" id="registerBox" style="display: none;">
        <h1>Register</h1>
        <form id="registerForm" onsubmit="return handleRegister(event)">
            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input type="email" id="registerEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="registerPassword">Password</label>
                <input type="password" id="registerPassword" name="password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <div class="form-footer">
            <p>Already have an account? <a href="#" onclick="toggleForms()">Login</a></p>
        </div>
        <div id="registerError" class="error-message"></div>
    </div>
</div>
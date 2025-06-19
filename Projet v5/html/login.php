<?php

session_start();
require_once 'php/config.php';


session_start();
require_once 'php/config.php';

// Si déjà connecté, rediriger vers index.php
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Mise à jour de la dernière connexion
            $stmt = $pdo->prepare('UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$user['id']]);

            // Création de la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Security Toolbox</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .auth-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #6B5ECD;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .auth-form input {
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
        }

        .auth-form input:focus {
            outline: none;
            border-color: #6B5ECD;
            box-shadow: 0 0 0 2px rgba(107, 94, 205, 0.1);
        }

        .auth-form button {
            background-color: #6B5ECD;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .auth-form button:hover {
            background-color: #5a4eb8;
        }

        .auth-links {
            margin-top: 1rem;
            text-align: center;
        }

        .auth-links a {
            color: #6B5ECD;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Connexion</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form class="auth-form" method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <div class="auth-links">
            <a href="register.php">Créer un compte</a>
        </div>
    </div>
</body>
</html> 

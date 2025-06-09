<?php
session_start();
require_once 'php/config.php';

// Si déjà connecté, rediriger vers index.php
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères';
    } elseif (strlen($username) < 3) {
        $error = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
    } else {
        // Vérifier si le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Ce nom d\'utilisateur est déjà pris';
        } else {
            // Créer le nouvel utilisateur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            
            try {
                $stmt->execute([$username, $hashed_password]);
                $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
            } catch (PDOException $e) {
                $error = 'Une erreur est survenue lors de la création du compte';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Security Toolbox</title>
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

        .success-message {
            color: #28a745;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Inscription</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form class="auth-form" method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required minlength="3">
            <input type="password" name="password" placeholder="Mot de passe" required minlength="8">
            <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required minlength="8">
            <button type="submit">Créer un compte</button>
        </form>
        <div class="auth-links">
            <a href="login.php">Déjà un compte ? Se connecter</a>
        </div>
    </div>
</body>
</html> 
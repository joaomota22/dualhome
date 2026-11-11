<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funcoes.php';

$feedback = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $feedback = 'E-mail ou palavra-passe inválidos.';
    } else {
        try {
            $statement = ligarBaseDados()->prepare(
                'SELECT id, nome, email, password, tipo_perfil FROM Utilizadores WHERE email = :email LIMIT 1'
            );
            $statement->execute([':email' => $email]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $feedback = 'E-mail ou palavra-passe inválidos.';
            } else {
                $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
                iniciarSessaoUtilizador($user, $remember);

                header('Location: painel.php');
                exit;
            }
        } catch (PDOException $exception) {
            $feedback = 'Não foi possível iniciar sessão. Confirma que o MySQL do WAMP está ativo e que a base de dados dualhome_db existe.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f6fb">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="styles/style.css">

    <title>Entrar | DualHome</title>
</head>

<body>

    <div class="page-shell">

        <?php $paginaAtiva = 'login'; require __DIR__ . '/includes/navbar.php'; ?>

        <!-- Conteúdo principal -->
        <main class="login-layout">

            <!-- Apresentação -->
            <section class="login-copy">

                <p class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    Bem-vindo de volta ao DualHome
                </p>

                <h1>
                    Duas casas.<br>
                    <span>Uma rotina organizada.</span>
                </h1>

                <p class="hero-description">
                    Entra na tua conta para acompanhares as tuas
                    checklists, preparares as mudanças entre casas
                    e gerires os teus pedidos de acesso.
                </p>

                <div class="login-benefits">

                    <div class="login-benefit">
                        <span class="benefit-check">✓</span>

                        <div>
                            <h3>Tudo organizado</h3>
                            <p>Consulta as tuas checklists num só lugar.</p>
                        </div>
                    </div>

                    <div class="login-benefit">
                        <span class="benefit-check">✓</span>

                        <div>
                            <h3>Transições mais simples</h3>
                            <p>Prepara tudo antes de mudares de casa.</p>
                        </div>
                    </div>

                    <div class="login-benefit">
                        <span class="benefit-check">✓</span>

                        <div>
                            <h3>Mais tranquilidade</h3>
                            <p>Menos esquecimentos e mal-entendidos.</p>
                        </div>
                    </div>

                </div>

            </section>

            <!-- Formulário -->
            <section class="login-card">

                <div class="login-card-header">

                    <div class="login-icon">⌂</div>

                    <h2>Iniciar sessão</h2>

                    <p>
                        Introduz os teus dados para acederes à tua conta.
                    </p>

                </div>

                <?php if ($feedback !== ''): ?>
                    <p class="signup-message" role="alert"><?= htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form action="login.php" method="post">

                    <div class="login-field">

                        <label for="email">
                            Endereço de email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="exemplo@email.com"
                            autocomplete="email"
                            maxlength="150"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >

                    </div>

                    <div class="login-field">

                        <div class="password-heading">

                            <label for="password">
                                Palavra-passe
                            </label>

                        </div>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Introduz a tua palavra-passe"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                    <div class="login-options">

                        <label>
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                            >
                            Manter sessão iniciada
                        </label>

                    </div>

                    <button type="submit" class="button login-submit">
                        Entrar na minha conta
                        <span aria-hidden="true">↗</span>
                    </button>

                </form>

                <div class="login-register">

                    <p>
                        Ainda não tens conta?
                        <a href="registar.php">Criar conta grátis</a>
                    </p>

                </div>

                <div class="login-privacy">
                    <span>✓</span>
                    A tua privacidade é importante para nós.
                </div>

            </section>

        </main>

      <?php require __DIR__ . '/includes/footer.php'; ?>

    </div>

</body>
</html>

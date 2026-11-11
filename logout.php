<?php
require_once __DIR__ . '/includes/funcoes.php';

iniciarSessaoSeNecessario();

// Sem sessão iniciada não há nada para terminar.
if (!utilizadorAutenticado()) {
  header('Location: index.php');
  exit;
}

// Só o envio do formulário de confirmação termina a sessão.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION = [];

  if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();
    setcookie(session_name(), '', [
      'expires' => time() - 42000,
      'path' => $parametros['path'],
      'domain' => $parametros['domain'],
      'secure' => $parametros['secure'],
      'httponly' => $parametros['httponly'],
      'samesite' => 'Lax',
    ]);
  }

  session_destroy();

  header('Location: index.php');
  exit;
}
?>
<!doctype html>
<html lang="pt-PT">
  <head>
    <meta charset="UTF-8" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" type="text/css" href="styles/style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#f5f6fb" />
    <title>DualHome - Terminar sessão</title>
  </head>
  <body>
    <div class="page-shell">
      <?php $paginaAtiva = 'logout'; require __DIR__ . '/includes/navbar.php'; ?>

      <main id="inicio">
        <section class="hero signup-layout" aria-labelledby="logout-title">
          <div class="hero-copy signup-copy">
            <p class="eyebrow">
              <span class="eyebrow-dot"></span> Até à próxima
            </p>
            <h1 id="logout-title">
              Queres mesmo<br />terminar <span>a sessão?</span>
            </h1>
            <p class="hero-description">
              Vais precisar de iniciar sessão outra vez para aceder ao teu painel.
              Se quiseres continuar aqui, volta ao painel sem alterar nada.
            </p>
          </div>

          <section class="signup-card" aria-labelledby="form-title">
            <div class="signup-card-heading">
              <div>
                <h2 id="form-title">
                  Terminar sessão <span aria-hidden="true">🔒</span>
                </h2>
                <p>Confirma para saíres da tua conta neste dispositivo.</p>
              </div>
            </div>

            <form class="logout-actions" method="post" action="logout.php">
              <button class="button signup-submit" type="submit">
                Sim, terminar sessão
              </button>
              <a class="button button-outline signup-submit" href="painel.php">
                Cancelar, voltar ao painel
              </a>
            </form>
          </section>
        </section>
      </main>

      <?php require __DIR__ . '/includes/footer.php'; ?>
    </div>
  </body>
</html>

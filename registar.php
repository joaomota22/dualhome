<?php
require_once __DIR__ . '/config/database.php';

$feedback = '';
$feedbackType = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = isset($_POST['nome']) && is_string($_POST['nome']) ? trim($_POST['nome']) : '';
  $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
  $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
  $profile = isset($_POST['tipo_perfil']) && is_string($_POST['tipo_perfil']) ? $_POST['tipo_perfil'] : '';
  $validProfiles = ['jovem', 'pai', 'mae'];
  $nameLength = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);

  if ($name === '' || $nameLength > 100) {
    $feedback = 'Indica um nome válido (até 100 caracteres).';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    $feedback = 'Indica um endereço de e-mail válido.';
  } elseif (strlen($password) < 8) {
    $feedback = 'A palavra-passe deve ter pelo menos 8 caracteres.';
  } elseif (!in_array($profile, $validProfiles, true)) {
    $feedback = 'Seleciona um perfil válido.';
  } elseif (!isset($_POST['terms'])) {
    $feedback = 'Tens de aceitar os termos e a política de privacidade.';
  } else {
    try {
      $pdo = ligarBaseDados();
      $statement = $pdo->prepare(
        'INSERT INTO Utilizadores (nome, email, password, tipo_perfil) VALUES (:nome, :email, :password, :tipo_perfil)'
      );
      $statement->execute([
        ':nome' => $name,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':tipo_perfil' => $profile,
      ]);
      $feedback = 'Conta criada com sucesso. Já podes iniciar sessão.';
      $feedbackType = 'success';
      $name = '';
      $email = '';
    } catch (PDOException $exception) {
      if ($exception->getCode() === '23000') {
        $feedback = 'Este e-mail já está registado.';
      } else {
        $feedback = 'Não foi possível criar a conta. Confirma que o MySQL do WAMP está ativo e que a base de dados dualhome_db existe.';
      }
    }
  }
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
    <title>DualHome - Comece Agora</title>
  </head>
  <body>
    <div class="page-shell">
      <?php $paginaAtiva = 'registar'; require __DIR__ . '/includes/navbar.php'; ?>

      <main id="inicio">
        <section class="hero signup-layout" aria-labelledby="signup-title">
          <div class="hero-copy signup-copy">
            <p class="eyebrow">
              <span class="eyebrow-dot"></span> Um espaço para as duas casas
            </p>
            <h1 id="signup-title">
              Criar conta.<br />Logística <span>resolvida.</span>
            </h1>
            <p class="hero-description">
              O DualHome ajuda jovens e famílias em residência alternada a gerir
              checklists de transição, pedir acesso a pertences esquecidos e
              organizar o dia a dia. Sem stress, sem mal-entendidos.
            </p>
            <div class="trust-note">
              <span class="trust-check" aria-hidden="true">✓</span>
              Privado, simples e feito para a tua família
            </div>
          </div>

          <section class="signup-card" aria-labelledby="form-title">
            <div class="signup-card-heading">
              <div>
                <h2 id="form-title">
                  Junta-te à tua família <span aria-hidden="true">🚀</span>
                </h2>
                <p>Cria o teu perfil para começar.</p>
              </div>
            </div>
            <?php if ($feedback !== ''): ?>
              <p class="signup-message<?= $feedbackType === 'success' ? ' success' : '' ?>" role="status"><?= htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <form class="signup-form" method="post" action="registar.php">
              <label class="form-field">
                <span>Nome completo</span>
                <input
                  name="nome"
                  type="text"
                  autocomplete="name"
                  placeholder="O teu nome completo"
                  maxlength="100"
                  value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                  required
                />
              </label>
              <label class="form-field">
                <span>E-mail de contacto</span>
                <input
                  name="email"
                  type="email"
                  autocomplete="email"
                  placeholder="E-mail de contacto"
                  maxlength="150"
                  value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                  required
                />
              </label>
              <label class="form-field">
                <span>Palavra-passe</span>
                <input
                  name="password"
                  type="password"
                  autocomplete="new-password"
                  minlength="8"
                  placeholder="Palavra-passe (mín. 8 caracteres)"
                  required
                />
              </label>

              <fieldset class="role-fieldset">
                <legend>Quem és tu no DualHome?</legend>
                <div class="role-options">
                  <label class="role-option">
                    <input type="radio" name="tipo_perfil" value="jovem" checked />
                    <span>Filho(a) <span aria-hidden="true">🙂</span></span>
                  </label>
                  <label class="role-option">
                    <input type="radio" name="tipo_perfil" value="pai" />
                    <span>Pai <span aria-hidden="true">👨</span></span>
                  </label>
                  <label class="role-option">
                    <input type="radio" name="tipo_perfil" value="mae" />
                    <span>Mãe <span aria-hidden="true">👩</span></span>
                  </label>
                </div>
              </fieldset>

              <label class="terms-field">
                <input type="checkbox" name="terms" value="1" required />
                <span
                  >Aceito os <a href="#termos">Termos</a> e a
                  <a href="#privacidade">Política de Privacidade</a>.</span
                >
              </label>
              <button class="button signup-submit" type="submit">
                Avançar para a plataforma <span aria-hidden="true">→</span>
              </button>
            </form>
          </section>
        </section>
        <section class="features" aria-label="Funcionalidades do DualHome">
          <article class="feature-item">
            <span class="feature-icon icon-blue" aria-hidden="true">✓</span>
            <div>
              <h2>Checklists inteligentes</h2>
              <p>Nada fica para trás na mudança.</p>
            </div>
            <span class="feature-emoji" aria-hidden="true">🎒</span>
          </article>
          <article class="feature-item">
            <span class="feature-icon icon-green" aria-hidden="true">✓</span>
            <div>
              <h2>Pedidos de acesso</h2>
              <p>Pertences pedidos sem confusão.</p>
            </div>
            <span class="feature-emoji" aria-hidden="true">🔑</span>
          </article>
          <article class="feature-item">
            <span class="feature-icon icon-yellow" aria-hidden="true">€</span>
            <div>
              <h2>Rotina mais leve</h2>
              <p>Mais clareza para toda a família.</p>
            </div>
            <span class="feature-emoji" aria-hidden="true">＋</span>
          </article>
        </section>
      </main>
    </div>
  </body>
</html>

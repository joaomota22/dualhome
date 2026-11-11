<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirSessao();

$idUtilizador = (int) $_SESSION['user_id'];

if (obterFamiliaDoUtilizador($idUtilizador) !== null) {
  header('Location: painel.php');
  exit;
}

$feedback = '';
$modo = 'criar';
$codigo = '';
$nomeFamilia = '';
$nomeCasaA = '';
$nomeCasaB = '';
$inicioAlternancia = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $modo = isset($_POST['modo']) && $_POST['modo'] === 'entrar' ? 'entrar' : 'criar';

  $tamanho = static function (string $texto): int {
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
  };

  if ($modo === 'entrar') {
    $codigo = isset($_POST['codigo']) && is_string($_POST['codigo']) ? trim($_POST['codigo']) : '';
    $codigoNormalizado = normalizarCodigoConvite($codigo);

    if (!preg_match('/^[A-Z0-9]{8}$/', $codigoNormalizado)) {
      $feedback = 'O código tem 8 caracteres (letras e números).';
    } else {
      try {
        $familiaAlvo = obterFamiliaPorCodigo($codigoNormalizado);

        if ($familiaAlvo === null) {
          $feedback = 'Não encontrámos nenhuma família com esse código. Confirma com quem te convidou.';
        } else {
          $statement = ligarBaseDados()->prepare('UPDATE Utilizadores SET id_familia = :id_familia WHERE id = :id');
          $statement->execute([
            ':id_familia' => (int) $familiaAlvo['id'],
            ':id' => $idUtilizador,
          ]);

          header('Location: painel.php');
          exit;
        }
      } catch (PDOException $exception) {
        $feedback = 'Não foi possível entrar na família. Confirma que o MySQL do WAMP está ativo e que a base de dados dualhome_db está atualizada.';
      }
    }
  } else {
    $nomeFamilia = isset($_POST['nome_familia']) && is_string($_POST['nome_familia']) ? trim($_POST['nome_familia']) : '';
    $nomeCasaA = isset($_POST['nome_casa_a']) && is_string($_POST['nome_casa_a']) ? trim($_POST['nome_casa_a']) : '';
    $nomeCasaB = isset($_POST['nome_casa_b']) && is_string($_POST['nome_casa_b']) ? trim($_POST['nome_casa_b']) : '';
    $inicioAlternancia = isset($_POST['inicio_alternancia']) && is_string($_POST['inicio_alternancia']) ? trim($_POST['inicio_alternancia']) : '';

    $dataAlternancia = DateTimeImmutable::createFromFormat('Y-m-d', $inicioAlternancia);
    $dataValida = $dataAlternancia !== false && $dataAlternancia->format('Y-m-d') === $inicioAlternancia;

    if ($nomeFamilia === '' || $tamanho($nomeFamilia) > 100) {
      $feedback = 'Indica o nome da família (até 100 caracteres).';
    } elseif ($nomeCasaA === '' || $tamanho($nomeCasaA) > 100) {
      $feedback = 'Indica o nome da primeira casa (até 100 caracteres).';
    } elseif ($nomeCasaB === '' || $tamanho($nomeCasaB) > 100) {
      $feedback = 'Indica o nome da segunda casa (até 100 caracteres).';
    } elseif (mb_strtolower($nomeCasaA, 'UTF-8') === mb_strtolower($nomeCasaB, 'UTF-8')) {
      $feedback = 'As duas casas têm de ter nomes diferentes.';
    } elseif (!$dataValida) {
      $feedback = 'Indica uma data válida para o fim de semana de referência.';
    } else {
      $pdo = null;

      try {
        $pdo = ligarBaseDados();
        $pdo->beginTransaction();

        // Gera um código novo se, por azar, já existir outro igual (coluna UNIQUE).
        $idFamilia = null;
        for ($tentativa = 0; $tentativa < 5 && $idFamilia === null; $tentativa++) {
          try {
            $statement = $pdo->prepare(
              'INSERT INTO Familias (nome_familia, nome_casa_a, nome_casa_b, inicio_alternancia, codigo_convite)
               VALUES (:nome_familia, :nome_casa_a, :nome_casa_b, :inicio_alternancia, :codigo_convite)'
            );
            $statement->execute([
              ':nome_familia' => $nomeFamilia,
              ':nome_casa_a' => $nomeCasaA,
              ':nome_casa_b' => $nomeCasaB,
              ':inicio_alternancia' => $inicioAlternancia,
              ':codigo_convite' => gerarCodigoConvite(),
            ]);
            $idFamilia = (int) $pdo->lastInsertId();
          } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
              throw $exception;
            }
          }
        }

        if ($idFamilia === null) {
          throw new PDOException('Não foi possível gerar um código de convite único.');
        }

        $statement = $pdo->prepare('UPDATE Utilizadores SET id_familia = :id_familia WHERE id = :id');
        $statement->execute([
          ':id_familia' => $idFamilia,
          ':id' => $idUtilizador,
        ]);

        $pdo->commit();

        header('Location: painel.php');
        exit;
      } catch (PDOException $exception) {
        if ($pdo !== null && $pdo->inTransaction()) {
          $pdo->rollBack();
        }

        $feedback = 'Não foi possível guardar a família. Confirma que o MySQL do WAMP está ativo e que a base de dados dualhome_db está atualizada.';
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
    <title>DualHome - Configurar família</title>
  </head>
  <body>
    <div class="page-shell">
      <?php $paginaAtiva = 'familia'; require __DIR__ . '/includes/navbar.php'; ?>

      <main id="inicio">
        <section class="hero signup-layout" aria-labelledby="family-title">
          <div class="hero-copy signup-copy">
            <p class="eyebrow">
              <span class="eyebrow-dot"></span> Último passo antes do painel
            </p>
            <h1 id="family-title">
              Configura a tua<br />família <span>no DualHome.</span>
            </h1>
            <p class="hero-description">
              Cria uma família nova para as tuas duas casas ou entra numa família
              existente com o código que recebeste. A alternância de fins de semana
              fica sempre visível no painel.
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
                  Família <span aria-hidden="true">🏡</span>
                </h2>
                <p>Escolhe como queres começar.</p>
              </div>
            </div>

            <?php if ($feedback !== ''): ?>
              <p class="signup-message" role="alert"><?= escapar($feedback) ?></p>
            <?php endif; ?>

            <form class="signup-form family-form" method="post" action="configurar_familia.php">
              <fieldset class="role-fieldset">
                <legend>Como queres começar?</legend>
                <div class="role-options">
                  <label class="role-option">
                    <input type="radio" name="modo" value="criar"<?= $modo === 'criar' ? ' checked' : '' ?> />
                    <span>Criar família</span>
                  </label>
                  <label class="role-option">
                    <input type="radio" name="modo" value="entrar"<?= $modo === 'entrar' ? ' checked' : '' ?> />
                    <span>Tenho um código</span>
                  </label>
                </div>
              </fieldset>

              <div class="family-section family-criar">
                <div class="family-field">
                  <label class="field-label" for="nome_familia">Nome da família</label>
                  <input
                    id="nome_familia"
                    name="nome_familia"
                    type="text"
                    placeholder="Ex.: Família Silva"
                    maxlength="100"
                    value="<?= escapar($nomeFamilia) ?>"
                  />
                </div>

                <div class="family-field">
                  <label class="field-label" for="nome_casa_a">Primeira casa</label>
                  <input
                    id="nome_casa_a"
                    name="nome_casa_a"
                    type="text"
                    placeholder="Ex.: Casa da mãe"
                    maxlength="100"
                    value="<?= escapar($nomeCasaA) ?>"
                  />
                </div>

                <div class="family-field">
                  <label class="field-label" for="nome_casa_b">Segunda casa</label>
                  <input
                    id="nome_casa_b"
                    name="nome_casa_b"
                    type="text"
                    placeholder="Ex.: Casa do pai"
                    maxlength="100"
                    value="<?= escapar($nomeCasaB) ?>"
                  />
                </div>

                <div class="family-field">
                  <label class="field-label" for="inicio_alternancia">Um fim de semana na primeira casa</label>
                  <input
                    id="inicio_alternancia"
                    name="inicio_alternancia"
                    type="date"
                    value="<?= escapar($inicioAlternancia) ?>"
                  />
                  <p class="field-hint">
                    Indica qualquer dia de um fim de semana em que o jovem fica na primeira casa.
                    A alternância segue a partir daí, semana a semana.
                  </p>
                </div>
              </div>

              <div class="family-section family-entrar">
                <div class="family-field">
                  <label class="field-label" for="codigo">Código da família</label>
                  <input
                    class="codigo-input"
                    id="codigo"
                    name="codigo"
                    type="text"
                    placeholder="Ex.: K7P2QM9D"
                    maxlength="12"
                    autocapitalize="characters"
                    autocomplete="off"
                    value="<?= escapar($codigo) ?>"
                  />
                  <p class="field-hint">
                    Pede o código a quem já tem a família criada no DualHome. Podes ver o código no painel dessa pessoa.
                  </p>
                </div>
              </div>

              <button class="button signup-submit" type="submit">
                Continuar para o painel <span aria-hidden="true">→</span>
              </button>
            </form>
          </section>
        </section>
      </main>

      <?php require __DIR__ . '/includes/footer.php'; ?>
    </div>
  </body>
</html>

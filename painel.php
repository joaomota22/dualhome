<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funcoes.php';

exigirSessao();

$idUtilizador = (int) $_SESSION['user_id'];
$familia = obterFamiliaDoUtilizador($idUtilizador);

if ($familia === null) {
  header('Location: configurar_familia.php');
  exit;
}

$primeiroNome = explode(' ', trim((string) $_SESSION['user_name']))[0];

$hoje = new DateTimeImmutable('today');
$sextaAtual = sextaDoFimDeSemana($hoje);
$sextaSeguinte = $sextaAtual->modify('+7 days');
$casaAtual = casaDaSexta($familia, $sextaAtual);
$casaSeguinte = casaDaSexta($familia, $sextaSeguinte);

$funcionalidades = [
  [
    'titulo' => 'Checklist de transição',
    'descricao' => 'Prepara a mala antes de mudares de casa, com os itens essenciais.',
    'icone' => '✓',
    'classe' => 'icon-blue',
  ],
  [
    'titulo' => 'Pedidos de acesso',
    'descricao' => 'Pede a recolha de um pertence esquecido sem trocar mensagens.',
    'icone' => '↗',
    'classe' => 'icon-green',
  ],
  [
    'titulo' => 'Despesas partilhadas',
    'descricao' => 'Regista despesas com comprovativo e acompanha a divisão.',
    'icone' => '€',
    'classe' => 'icon-yellow',
  ],
];
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
    <title>DualHome - O meu painel</title>
  </head>
  <body>
    <div class="page-shell">
      <?php $paginaAtiva = 'painel'; require __DIR__ . '/includes/navbar.php'; ?>

      <main class="dashboard-main">
        <section class="dashboard-header" aria-labelledby="dashboard-title">
          <p class="eyebrow">
            <span class="eyebrow-dot"></span> <?= escapar($familia['nome_familia']) ?>
          </p>
          <h1 id="dashboard-title">
            Olá, <?= escapar($primeiroNome) ?>.<br />
            <span>Tudo pronto para a semana.</span>
          </h1>
        </section>

        <section class="rotation-card" aria-label="Calendário de fins de semana">
          <div class="rotation-item">
            <small>Este fim de semana</small>
            <strong><?= escapar($casaAtual) ?></strong>
            <p>Sexta-feira, <?= escapar($sextaAtual->format('d/m/Y')) ?></p>
          </div>
          <div class="rotation-item">
            <small>Fim de semana seguinte</small>
            <strong><?= escapar($casaSeguinte) ?></strong>
            <p>Sexta-feira, <?= escapar($sextaSeguinte->format('d/m/Y')) ?></p>
          </div>
          <div class="rotation-item">
            <small>As tuas casas</small>
            <strong><?= escapar($familia['nome_casa_a']) ?></strong>
            <p>e <?= escapar($familia['nome_casa_b']) ?></p>
          </div>
        </section>

        <section class="tool-grid" aria-label="Funcionalidades">
          <?php foreach ($funcionalidades as $funcionalidade): ?>
            <article class="tool-card">
              <div class="tool-card-top">
                <span class="feature-icon <?= escapar($funcionalidade['classe']) ?>" aria-hidden="true"><?= escapar($funcionalidade['icone']) ?></span>
                <span class="tool-badge">Em breve</span>
              </div>
              <h2><?= escapar($funcionalidade['titulo']) ?></h2>
              <p><?= escapar($funcionalidade['descricao']) ?></p>
            </article>
          <?php endforeach; ?>
        </section>
      </main>
    </div>
  </body>
</html>

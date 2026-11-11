<?php
require_once __DIR__ . '/funcoes.php';
$utilizadorAutenticado = utilizadorAutenticado();
?>
<header class="site-header">
  <a class="brand" href="index.php#inicio" aria-label="DualHome, início">
    <span class="brand-mark" aria-hidden="true">⌂</span>
    <span>Dual<span class="brand-light">Home</span></span>
  </a>

  <nav class="navbar" aria-label="Navegação principal">
    <a class="nav-link<?= $paginaAtiva === 'inicio' ? ' active' : '' ?>" href="index.php#inicio">Início</a>
    <a class="nav-link<?= $paginaAtiva === 'como' ? ' active' : '' ?>" href="como.php">Como funciona</a>
    <a class="nav-link<?= $paginaAtiva === 'sobre' ? ' active' : '' ?>" href="sobrenos.php">Sobre nós</a>
  </nav>

  <div class="header-actions">
    <?php if ($utilizadorAutenticado): ?>
      <a class="login-link" href="logout.php">Terminar sessão</a>
      <a class="button button-small" href="painel.php">O meu painel <span aria-hidden="true">↗</span></a>
    <?php else: ?>
      <a class="login-link" href="login.php">Entrar</a>
      <a class="button button-small" href="registar.php">Começar grátis <span aria-hidden="true">↗</span></a>
    <?php endif; ?>
  </div>
</header>
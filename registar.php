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
      <header class="site-header">
        <a class="brand" href="index.html#inicio" aria-label="DualHome, início">
          <span class="brand-mark" aria-hidden="true">⌂</span>
          <span>Dual<span class="brand-light">Home</span></span>
        </a>

        <nav class="navbar" aria-label="Navegação principal">
          <a class="nav-link" href="index.html#inicio">Início</a>
          <a class="nav-link" href="index.html#funcionalidades"
            >Como funciona</a
          >
          <a class="nav-link" href="index.html#sobre">Sobre nós</a>
        </nav>

        <div class="header-actions">
          <a class="login-link" href="entrar.html">Entrar</a>
          <a class="button button-small" href="registar.html"
            >Começar grátis <span aria-hidden="true">↗</span></a
          >
        </div>
      </header>

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
            <form class="signup-form">
              <label class="form-field">
                <span>Nome completo</span>
                <input
                  name="name"
                  type="text"
                  autocomplete="name"
                  placeholder="O teu nome completo"
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
                    <input type="radio" name="role" value="child" checked />
                    <span>Filho(a) <span aria-hidden="true">🙂</span></span>
                  </label>
                  <label class="role-option">
                    <input type="radio" name="role" value="father" />
                    <span>Pai <span aria-hidden="true">👨</span></span>
                  </label>
                  <label class="role-option">
                    <input type="radio" name="role" value="mother" />
                    <span>Mãe <span aria-hidden="true">👩</span></span>
                  </label>
                </div>
              </fieldset>

              <label class="terms-field">
                <input type="checkbox" name="terms" required />
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
      </main>
    </div>
  </body>
</html>

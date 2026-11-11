<?php
date_default_timezone_set('Europe/Lisbon');

function escapar(string $valor): string
{
  return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

/* ---------- Sessão ---------- */

function iniciarSessaoSeNecessario(): void
{
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

function utilizadorAutenticado(): bool
{
  iniciarSessaoSeNecessario();

  return isset($_SESSION['user_id']);
}

function exigirSessao(): void
{
  if (!utilizadorAutenticado()) {
    header('Location: login.php');
    exit;
  }
}

function iniciarSessaoUtilizador(array $utilizador, bool $lembrar): void
{
  $duracao = $lembrar ? 2592000 : 0;

  if ($lembrar) {
    ini_set('session.gc_maxlifetime', (string) $duracao);
  }

  if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
      'lifetime' => $duracao,
      'path' => '/',
      'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
    session_start();
  }

  session_regenerate_id(true);
  $_SESSION['user_id'] = (int) $utilizador['id'];
  $_SESSION['user_name'] = $utilizador['nome'];
  $_SESSION['user_email'] = $utilizador['email'];
  $_SESSION['user_role'] = $utilizador['tipo_perfil'];
}

/* ---------- Família ---------- */

function obterFamiliaDoUtilizador(int $idUtilizador): ?array
{
  $statement = ligarBaseDados()->prepare(
    'SELECT f.id, f.nome_familia, f.nome_casa_a, f.nome_casa_b, f.inicio_alternancia, f.codigo_convite
       FROM Familias f
       INNER JOIN Utilizadores u ON u.id_familia = f.id
      WHERE u.id = :id
      LIMIT 1'
  );
  $statement->execute([':id' => $idUtilizador]);
  $familia = $statement->fetch(PDO::FETCH_ASSOC);

  return $familia ?: null;
}

function obterFamiliaPorCodigo(string $codigo): ?array
{
  $statement = ligarBaseDados()->prepare(
    'SELECT id, nome_familia, nome_casa_a, nome_casa_b, inicio_alternancia, codigo_convite
       FROM Familias
      WHERE codigo_convite = :codigo
      LIMIT 1'
  );
  $statement->execute([':codigo' => $codigo]);
  $familia = $statement->fetch(PDO::FETCH_ASSOC);

  return $familia ?: null;
}

/* Alfabeto sem caracteres ambíguos (0/O, 1/I/L) para o código ser fácil de ler e digitar. */
function gerarCodigoConvite(): string
{
  $alfabeto = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
  $codigo = '';

  for ($i = 0; $i < 8; $i++) {
    $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
  }

  return $codigo;
}

function normalizarCodigoConvite(string $texto): string
{
  return strtoupper(preg_replace('/\s+/', '', $texto));
}

/* Devolve a sexta-feira que abre o fim de semana a que a data pertence. */
function sextaDoFimDeSemana(DateTimeImmutable $data): DateTimeImmutable
{
  $diaDaSemana = (int) $data->format('N'); // 1 = segunda ... 7 = domingo

  if ($diaDaSemana >= 6) {
    return $data->modify('-' . ($diaDaSemana - 5) . ' days');
  }

  return $data->modify('+' . (5 - $diaDaSemana) . ' days');
}

/* Alterna semana a semana a partir do fim de semana de referência da família. */
function casaDaSexta(array $familia, DateTimeImmutable $sexta): string
{
  $ancora = sextaDoFimDeSemana(new DateTimeImmutable($familia['inicio_alternancia']));
  $diferenca = $ancora->diff($sexta);
  $dias = $diferenca->days * ($diferenca->invert ? -1 : 1);
  $semanas = (int) floor($dias / 7);

  return $semanas % 2 === 0 ? $familia['nome_casa_a'] : $familia['nome_casa_b'];
}

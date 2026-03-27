# Zadanie 1

1. AuthController podatny na SQL Injection.
2. HomeController i PhotoController - tworzenie repozytorium po pierwsze powinno odbywać się w warstwie serwisu(
   logicznej), a po drugie zamiast za pomocą new - powinno być wstrzykiwane przez DI.
3. LikeRepositoryInterface - brakuje deklaracji metody setUser.
4. LikeRepository - brakuje sprawdzenia czy user nie jest nullem w momencie używania $this->user.
5. composer.json - nieprawidłowa wartość dla PSR-4 i brakowało autoload dla tests.

# Zadanie 2

## Jakość kodu i testy (Symfony)

- Skrypt `symfony-app/scripts/quality.sh` uruchamia po kolei: **PHPStan**, **PHPCS**, **phpcpd** (PHAR w
  `tools/phpcpd.phar`), migracje Doctrine na `env=test` (gdy ustawione jest `DATABASE_URL`) oraz **PHPUnit**.
- Lokalnie (bez Dockera): z katalogu `symfony-app` ustaw `DATABASE_URL` na Postgresa i uruchom `./scripts/quality.sh`.
- W **Docker Compose** (z katalogu głównego repozytorium) — usługa `symfony` ma domyślny entrypoint uruchamiający serwer
  PHP, więc do samej analizy i testów trzeba go pominąć i nadpisać `DATABASE_URL` na format `postgresql://` (Doctrine):

```bash
docker compose run --rm --entrypoint "" symfony bash -ec '
cd /app
composer install --no-interaction --prefer-dist
export DATABASE_URL="postgresql://postgres:postgres@symfony-db:5432/instashot?serverVersion=15&charset=utf8"
./scripts/quality.sh
'
```

- Skrót przez Composera (bez migracji w jednym poleceniu): `composer quality` w `symfony-app` — przed smoke testami
  migracje trzeba wykonać samodzielnie, jeśli są potrzebne.
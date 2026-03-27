## Uruchomienie całości (Docker Compose)

Z katalogu głównego repozytorium (tam, gdzie leży `docker-compose.yml`):

```bash
`docker compose up --build -d
```

- **Symfony (Insta Shot)**: [http://localhost:8000](http://localhost:8000) — kontener `symfony` uruchamia `composer install`, czeka na Postgresa (`symfony-db`), wykonuje migracje Doctrine i `app:seed`, potem serwer PHP na porcie 8000.
- **Phoenix API**: [http://localhost:4000](http://localhost:4000) — kontener `phoenix` uruchamia `mix ecto.migrate`, seed z `priv/repo/seeds.exs` i `mix phx.server`.

Symfony wewnątrz sieci Docker łączy się z Phoenix przez `PHOENIX_BASE_URL=http://phoenix:4000` (ustawione w `docker-compose.yml`).

Jeśli kiedyś trzeba ręcznie powtórzyć migracje lub seed tylko dla Symfony:

```bash
docker compose exec symfony php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec symfony php bin/console app:seed --no-interaction
```

## Dane logowania

W polu **token** wpisujesz **64-znakowy token hex** z seeda (nie jest to klasyczne hasło). Wartości są stałe i zdefiniowane w `symfony-app/src/Shared/Command/SeedDatabaseCommand.php` (`SEED_AUTH_TOKENS_BY_USERNAME`).

| Username           | Token (hex)                                                                                    |
| ------------------ | ---------------------------------------------------------------------------------------------- |
| `nature_lover`     | `0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef`                             |
| `wildlife_pro`     | `fedcba9876543210fedcba9876543210fedcba9876543210fedcba9876543210`                             |
| `landscape_dreams` | `aaaabbbbccccddddeeeeffff00001111aaaabbbbccccddddeeeeffff00001111`                             |
| `animal_eyes`      | `111122223333444455556666777788889999aaaabbbbccccddddeeeeffff0000`                             |

## Tokeny Phoenix API

Wartości pochodzą z `phoenix-api/priv/repo/seeds.exs` (uruchamiane przy starcie kontenera `phoenix`). Żądania do API (np. `GET /api/photos`) wymagają nagłówka **`access-token`** z jednym z poniższych tokenów — każdy odpowiada innemu użytkownikowi Phoenix i innemu zestawowi zdjęć w seedzie.

| Użytkownik w seedzie | `api_token` (nagłówek `access-token`)   |
| -------------------- | ---------------------------------------- |
| user 1               | `test_token_user1_abc123`                |
| user 2               | `test_token_user2_def456`                |

## Jakość kodu i testy (Symfony)

- Skrypt `symfony-app/scripts/quality.sh` uruchamia po kolei: **PHPStan**, **PHPCS**, **phpcpd** (PHAR w `tools/phpcpd.phar`), migracje Doctrine na `env=test` (gdy ustawione jest `DATABASE_URL`) oraz **PHPUnit**.
- Lokalnie (bez Dockera): z katalogu `symfony-app` ustaw `DATABASE_URL` na Postgresa i uruchom `./scripts/quality.sh`.
- Skrót `composer quality` w `symfony-app` uruchamia PHPStan, PHPCS, phpcpd i PHPUnit, ale **bez** migracji — przed smoke testami ustaw `DATABASE_URL` i wykonaj migracje (`doctrine:migrations:migrate --env=test`) albo użyj `./scripts/quality.sh`.


## Zadanie 1

1. AuthController podatny na SQL Injection.
2. HomeController i PhotoController - tworzenie repozytorium za pomocą new - powinno być wstrzykiwane przez DI.
3. LikeRepositoryInterface - brakuje deklaracji metody setUser.
4. LikeRepository - brakuje sprawdzenia czy user nie jest nullem w momencie używania $this->user.
5. composer.json - nieprawidłowa wartość dla PSR-4 i brakowało autoload dla tests.

## Zadanie 2

Zrobione.

## Zadanie 3

Zrobione.

## Zadanie 4

Zrobione.
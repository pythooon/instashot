# Zadanie 1

1. AuthController podatny na SQL Injection.
2. HomeController i PhotoController - tworzenie repozytorium za pomocą new - powinno być wstrzykiwane przez DI.
3. LikeRepositoryInterface - brakuje deklaracji metody setUser.
4. LikeRepository - brakuje sprawdzenia czy user nie jest nullem w momencie używania $this->user.
5. composer.json - nieprawidłowa wartość dla PSR-4 i brakowało autoload dla tests.
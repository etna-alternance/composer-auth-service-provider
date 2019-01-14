auth-service-provider
=====================

[![Build Status](http://drone.etna-alternance.net/api/badge/github.com/etna-alternance/composer-auth-service-provider/status.svg?branch=v3)](http://drone.etna-alternance.net/github.com/etna-alternance/composer-auth-service-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/etna-alternance/composer-auth-service-provider/badges/quality-score.png?b=v3)](https://scrutinizer-ci.com/g/etna-alternance/composer-auth-service-provider/?branch=v3)
[![Coverage Status](https://coveralls.io/repos/github/etna-alternance/composer-auth-service-provider/badge.svg?branch=v3)](https://coveralls.io/github/etna-alternance/composer-auth-service-provider?branch=v3)

## Installation

### Composer :

```
composer require etna/auth-service-provider:^3.0
```

## Utilisation

1. Ajouter dans le fichier `bundles.php`

    ```
    return [
        ...
        ETNA\Auth\AuthBundle::class => ['all' => true],
    ];
    ```

2. Configurer le bundle pour chacun des envs :

    Créer `config/packages/{env}/auth.{php,yml}` et y mettre :

    - En yml :

        ```
        auth:
            authenticator_url: "l'url de l'api auth"
            api_path: "^/$"
            cookie_expiration: "<expiration>"
        ```

    - En PHP :

        ```
        <?php

        use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

        return function (ContainerConfigurator $container) {
                $container->extension("auth", array(
                    "authenticator_url" => __DIR__ . "/../../../tmp/keys/",
                    "cookie_expiration" => "+10minutes"
                ));
        };
        ```

3. La fonction authBeforeFunction

    Il ne reste plus que deux étapes avant d'avoir des routes sécurisées :

     - Implémenter l'interface `EtnaCookieAuthenticatedController` dans le controlleur à sécuriser
     - Créer une classe qui implémente la classe abstraite `ETNA\Auth\Services\AuthCheckingService` :

        ```
        <?php

        namespace TestApp\Services;

        use Symfony\Component\HttpFoundation\Request;
        use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
        use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

        use ETNA\Auth\Services\AuthCheckingService as BaseAuthCheckingService;

        class AuthCheckingService extends BaseAuthCheckingService
        {
            public function authBeforeFunction(Request $req): void
            {
                //Ici je peux vérifier comme je veux mon user ($req->attributes->get("auth.user"))
                //Et throw des exceptions
            }
            // On peut aussi ne pas implémenter cette fonction pour garder celle de base
        }
        ```

## Documentation

Il existe une documentation basée qui est générable en utilisant `composer phing -- doc`
Cela va générer une documentation dans le dossier `doc`, il suffit d'ouvrir le fichier `index.html` qui s'y trouve

<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->parameters()->set("application_name", "another_app_name");

    $container->extension("framework", [
        "secret" => getenv("APP_SECRET"),
        "test"   => true
    ]);

    $container->extension("auth", [
        "authenticator_url" => "http://auth.etna.localhost"
    ]);
};

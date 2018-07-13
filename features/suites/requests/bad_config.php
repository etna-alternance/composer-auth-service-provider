<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->parameters()->set("application_name", "that's mah nem");

    $container->extension("framework", [
        "secret" => getenv("APP_SECRET"),
        "test"   => true
    ]);

    $container->extension("auth", [
        "cookie_expiration" => "+10minutes"
    ]);
};

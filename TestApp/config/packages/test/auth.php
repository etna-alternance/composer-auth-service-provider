<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension("auth", array(
        "authenticator_url" => __DIR__ . "/../../../tmp/keys/",
        "cookie_expiration" => "+10minutes"
    ));
};

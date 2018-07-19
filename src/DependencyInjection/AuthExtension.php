<?php
/**
 * Définition de la classe AuthExtension.
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
 */

declare(strict_types=1);

namespace ETNA\Auth\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * On définit cette classe pour personnaliser le processus de parsing de la configuration de notre bundle.
 *
 * Entre autres on ajoute la configuration dans les paramêtres du container Symfony
 */
class AuthExtension extends Extension
{
    /**
     * Cette fonction est appelée par symfony et permet le chargement de la configuration du bundle
     * Ici on va chercher la config des services dans le dossier Resources/config.
     *
     * @param array            $configs   Les éventuels paramètres
     * @param ContainerBuilder $container Le container de la configuration
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        foreach ($config as $config_name => $config_value) {
            $container->setParameter("auth.{$config_name}", $config_value);
        }

        $root_dir     = realpath($container->getParameter('kernel.root_dir') . '/../');
        $tmp_key_path = "{$root_dir}/tmp/public-{$container->getParameter('kernel.environment')}.key";

        $container->setParameter('auth.app_name', $container->getParameter('application_name'));
        $container->setParameter('auth.public_key.tmp_path', $tmp_key_path);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}

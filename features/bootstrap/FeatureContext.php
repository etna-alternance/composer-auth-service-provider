<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Symfony\Component\HttpFoundation\Request;

use ETNA\Auth\Services\AuthCheckingService;
use ETNA\Auth\Services\AuthCookieService;

use ETNA\FeatureContext\BaseContext;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BaseContext
{
    private static $good_key;
    private static $bad_key;

    private $cookie_service;
    private $checking_service;

    private $request;
    private $exception;

    private $identity;

    private $test_kernel;

    public function __construct(AuthCookieService $cookie_service, AuthCheckingService $checking_service)
    {
        $this->cookie_service   = $cookie_service;
        $this->checking_service = $checking_service;
        $this->request          = new Request();
        $this->exception        = null;
        $this->identity         = null;
        $this->test_kernel      = null;
    }

    private function generateCookie($string_identity, $key)
    {
        $identity = json_decode($string_identity, true);

        if (!$identity) {
            throw new \Exception(__FILE__ . ":" . __LINE__ . " json_decode error");
        }

        if (!openssl_sign(base64_encode(json_encode($identity)), $signature, $key)) {
            throw new \Exception(__FILE__ . ":" . __LINE__ . " Error signing cookie");
        }

        $identity = [
            "identity"  => base64_encode(json_encode($identity)),
            "signature" => base64_encode($signature),
        ];

        $this->request->cookies->set("authenticator", base64_encode(json_encode($identity)));

        $request = $this->getContext('ETNA\\FeatureContext\\ApiContext')->getRequest();
        $request["cookies"]["authenticator"] = base64_encode(json_encode($identity));
        $this->getContext('ETNA\\FeatureContext\\ApiContext')->setRequest($request);
    }

    /**
     * @Given je suis authentifié avec :
     */
    public function jeSuisAuthentifieAvec($string_identity)
    {
        $this->generateCookie($string_identity, self::$good_key);
    }

    /**
     * @Given je suis faussement authentifié avec :
     */
    public function jeSuisFaussementAuthentifieAvec($string_identity)
    {
        $this->generateCookie($string_identity, self::$bad_key);
    }

    /**
     * @Given je crée un cookie invalide
     */
    public function jeCreeUnCookieInvalide()
    {
        $this->request->cookies->set("authenticator", "1234");

        $request = $this->getContext('ETNA\\FeatureContext\\ApiContext')->getRequest();
        $request["cookies"]["authenticator"] = "1234";
        $this->getContext('ETNA\\FeatureContext\\ApiContext')->setRequest($request);
    }

    /**
     * @Given je crée un cookie contenant une identité invalide
     */
    public function jeCreeUnCookieContenantUneIdentiteInvalide()
    {
        if (!openssl_sign(base64_encode("foot"), $signature, self::$good_key)) {
            throw new \Exception(__FILE__ . ":" . __LINE__ . " Error signing cookie");
        }

        $identity = [
            "identity"  => base64_encode("foot"),
            "signature" => base64_encode($signature),
        ];

        $this->request->cookies->set("authenticator", base64_encode(json_encode($identity)));

        $request = $this->getContext('ETNA\\FeatureContext\\ApiContext')->getRequest();
        $request["cookies"]["authenticator"] = base64_encode(json_encode($identity));
        $this->getContext('ETNA\\FeatureContext\\ApiContext')->setRequest($request);
    }

    /**
     * @Given j'ajoute mon user a la request
     */
    public function jAjouteMonUserALaRequest()
    {
        $this->exception = null;

        try {
            $this->cookie_service->addUserToRequest($this->request);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given je tente d'extraire les informations du cookie
     */
    public function jeTenteDExtraireLesInformationsDuCookie()
    {
        $this->exception = null;

        try {
            $this->cookie_service->extract($this->request->cookies->get("authenticator"));
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given le user de la request devrait être :
     */
    public function leUserDeLaRequestDevraitEtre($expected)
    {
        $request_user = $this->request->attributes->get("auth.user");
        $result       = json_decode($expected);

        if (null === $result) {
            throw new \Exception(__FILE__ . ":" . __LINE__ . " json_decode error");
        }

        $this->check($result, $request_user, "result", $errors);
        $this->handleErrors($request_user, $errors);
    }

    /**
     * @Given je sette l'expiration du cookie à :value
     */
    public function jeSetteLExpirationDuCookieA($value)
    {
        $this->cookie_service->setCookieExpiration($value);
    }

    /**
     * @Given je ne devrais pas avoir de user dans la request
     */
    public function jeNeDevraisPasAvoirDeUserDansLaRequest()
    {
        if (null !== $this->request->attributes->get("auth.user")) {
            throw new \Exception("Expected auth.user to be null");
        }
    }

    /**
     * @Given je supprime la clé stockée localement
     */
    public function jeSupprimeLaCleStockeeLocalement()
    {
        $tmp_path = $this->getContainer()->getParameter("auth.public_key.tmp_path");
        @unlink($tmp_path);
    }

    /**
     * @Given je réinstancie le cookie service
     */
    public function jeReinstancieLeCookieService()
    {
        $this->cookie_service->handleRSA($this->getContainer());
    }

    /**
     * @Given je devrais avoir la cle en local
     */
    public function jeDevraisAvoirLaCleEnLocal()
    {
        $tmp_path = $this->getContainer()->getParameter("auth.public_key.tmp_path");
        if (!file_exists($tmp_path)) {
            throw new Exception("Expecting public testing key to exist");
        }
    }

    /**
     * @Given /^je vérifie que je suis authentifié$/
     */
    public function jeVerifieQueJeSuisAuthentifie()
    {
        $this->exception = null;

        try {
            $this->cookie_service->authenticated($this->request);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given /^je vérifie que je dispose du rôle "([^"]*)"$/
     */
    public function jeVerifieQueJeDisposeDuRole($role)
    {
        $this->exception = null;

        try {
            $this->cookie_service->userHasGroup($this->request, $role);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given /^ca devrait s'être bien déroulé$/
     */
    public function caDevraitSEtreBienDeroule()
    {
        if (null !== $this->exception) {
            throw new \Exception("Expecting last action to went well");
        }
    }

    /**
     * @Given /^ca ne devrait pas s'être bien déroulé$/
     */
    public function caNeDevraitPasBienSEtreDeroule()
    {
        if (null === $this->exception) {
            throw new \Exception("Was not expecting last action to went well");
        }
    }

    /**
     * @Given /^l'exception devrait avoir comme message "(.*)"$/
     */
    public function lExceptionDevraitAvoirCommeMessage($message)
    {
        if (null === $this->exception) {
            throw new \Exception("Was expecting an exception to check message");
        }

        $expected = $this->exception->getMessage();
        if ($message !== $expected) {
            throw new Exception("Expecting exception to have {$message} but got {$expected}");
        }
    }

    /**
     * @Given j'override la clé
     */
    public function jOverrideLaCle()
    {
        $path       = getcwd() . "/TestApp/tmp";
        $this->cookie_service->setRSA(\ETNA\RSA\RSA::loadPrivateKey("file://{$path}/keys/private.key"));
    }

    /**
     * @Given je génère le cookie pour l'identité :
     */
    public function jeGenereLeCookiePourLIdentite($string_identity)
    {
        $identity = json_decode($string_identity, true);

        if (!$identity) {
            throw new \Exception(__FILE__ . ":" . __LINE__ . " json_decode error");
        }

        $this->identity  = $identity;
        $this->exception = null;

        try {
            $this->request->cookies->set("authenticator", $this->cookie_service->generate($this->identity));
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given je remet l'ancienne clé
     */
    public function jeRemetLAncienneCle()
    {
        $this->cookie_service->__destruct();
        $this->cookie_service->handleRSA($this->getContainer());
    }

    /**
     * @Given je crée un nouveau kernel de test
     */
    public function jeCreeUnNouveauKernelDeTest()
    {
        $this->test_kernel = new class('test', true) extends TestApp\Kernel {
            public static $config_path;

            public function getCacheDir()
            {
                return $this->getProjectDir().'/TestApp/tmp/cache/behat-env';
            }

            public function registerBundles()
            {
                $bundles = [
                    ETNA\Auth\AuthBundle::class,
                    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class,
                    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class,
                ];

                foreach ($bundles as $bundle) {
                    yield new $bundle();
                }
            }


            protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader) {
                $loader->load(self::$config_path);
            }

            public function shutdown() {
                // Je suis obligé de faire ca pour pouvoir tester plusieurs configurations dans la même suite
                // Merci Symfony d'être aussi flexible sur le cache :)
                exec("rm -rf {$this->getCacheDir()}");
                parent::shutdown();
            }
        };
    }

    /**
     * @Given /^je configure le kernel avec le fichier "([^"]*)"$/
     */
    public function jeConfigureLeKernelAvecLeFichier($config_file)
    {
        $this->test_kernel::$config_path = $this->requests_path . $config_file;
    }

    /**
     * @Given je boot le kernel
     */
    public function jeBootLeKernel()
    {
        $this->exception = null;

        try {
            $this->test_kernel->boot();
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Given je n'ai plus besoin du kernel de test
     */
    public function jeNAiPlusBesoinDuKernelDeTest()
    {
        if (null === $this->test_kernel) {
            throw new \Exception("No test kernel to remove");
        }
        $this->test_kernel->shutdown();
        $this->test_kernel = null;
    }

    /**
     * @Given /^les paramêtres de mon application devraient être :$/
     */
    public function lesParametresDeMonApplicationDevraientEtre($expected_params)
    {
        $expected = json_decode($expected_params);

        if (null === $expected) {
            throw new \Exception("json_decode error");
        }
        if (null === $this->test_kernel) {
            throw new Exception("No test_kernel to check params on");
        }

        $actual_params = new \stdClass();
        foreach ($expected as $param_key => $param_value) {
            $actual_params->$param_key = $this->test_kernel->getContainer()->getParameter($param_key);
        }

        $this->check($expected, $actual_params, "result", $errors);
        $this->handleErrors($actual_params, $errors);
    }

    /**
     * @BeforeSuite
     */
    public static function setUpRsa()
    {
        $path       = getcwd() . "/TestApp/tmp";
        $public_key = "{$path}/public-" . getenv("APP_ENV") . ".key";

        if (true === file_exists($public_key)) {
            unlink($public_key);
        }

        // La vraie clé
        passthru("bash -c '[ -d {$path}/keys ] || mkdir -p {$path}/keys'");
        passthru("bash -c '[ -f {$path}/keys/private.key ] || openssl genrsa  -out {$path}/keys/private.key 2048'");
        passthru("bash -c '[ -f {$path}/keys/public.key ]  || openssl rsa -in {$path}/keys/private.key -pubout -out {$path}/keys/public.key'");

        // ... la fausse
        passthru("bash -c '[ -f {$path}/keys/bad_private.key ] || openssl genrsa  -out {$path}/keys/bad_private.key 2048'");
        passthru(
            "bash -c '[ -f {$path}/keys/bad_public.key ]  || openssl rsa -in {$path}/keys/bad_private.key -pubout -out {$path}/keys/bad_public.key'"
        );

        self::$good_key = openssl_pkey_get_private("file://{$path}/keys/private.key");
        self::$bad_key  = openssl_pkey_get_private("file://{$path}/keys/bad_private.key");
    }

    /**
     * @AfterSuite
     */
    public static function tearDownRsa()
    {
        $file = getcwd() . "/TestApp/tmp/public.key";
        if (true === file_exists($file)) {
            unlink($file);
        }
    }
}

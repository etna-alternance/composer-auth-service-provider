<?php

namespace ETNA\Auth\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;

use ETNA\Auth\EtnaCookieAuthenticatedController;
use ETNA\RSA\RSA;

class AuthCookieService implements EventSubscriberInterface
{
    private $rsa;
    private $expiration;

    public function __construct(ContainerInterface $container)
    {
        $this->setCookieExpiration($container->getParameter("auth.cookie_expiration"));

        if (!isset($this->rsa)) {
            $this->handleRSA($container);
        }
    }

    /**
     * On détruit la clé RSA
     */
    public function __destruct()
    {
        unset($this->rsa);
    }

    public function setRSA(RSA $rsa)
    {
        $this->rsa = $rsa;
    }

    public function handleRSA(ContainerInterface $container)
    {
        $rsa_filepath = $container->getParameter("auth.public_key.tmp_path");
        $auth_url     = $container->getParameter("auth.authenticator_url");

        if (!file_exists($rsa_filepath) || filemtime($rsa_filepath) < strtotime("-30seconds")) {
            $key = file_get_contents("{$auth_url}/public.key");

            file_put_contents($rsa_filepath, $key);
        }

        $this->rsa = \ETNA\RSA\RSA::loadPublicKey("file://" . $rsa_filepath);
    }

    /**
     * S'assure que la requête contient bien des informations concernant une authentification
     *
     * @param  Request $req La requête HTTP
     *
     * @return null
     */
    public function authenticated(Request $req)
    {
        $user = $req->attributes->get("auth.user", null);

        if (null === $user || null === $user->login_date) {
            throw new HttpException(401, "Unauthorized");
        }
    }

    /**
     * Vérifie la présence du rôle dans le user connecté
     *
     * @param  Request $req   La requête HTTP
     * @param  string  $group Le groupe à vérifier
     *
     * @return null
     */
    public function userHasGroup(Request $req, $group)
    {
        $user = $req->attributes->get("auth.user", null);

        $this->authenticated($req);

        if (!in_array($group, $user->groups)) {
            throw new HttpException(403, "Forbidden");
        }
    }

    /**
     * Ajoute l'utilisateur à la requête HTTP
     *
     * @param Request $req La requête HTTP
     */
    public function addUserToRequest(Request $req)
    {
        $user = null;

        if ($req->cookies->has("authenticator")) {
            $user = $this->extract($req->cookies->get("authenticator"));

            // Je suis authentifié depuis trop longtemps
            if ($this->expiration && strtotime("{$user->login_date}{$this->expiration}") < strtotime("now")) {
                $user = null;
            }
        }

        $req->attributes->set("auth.user", $user);
    }

    /**
     * C'est la fonction qui sera appelée par symfony lors d'un des events indiqué par getSubscribedEvents
     *
     * @param  FilterControllerEvent $event L'évènement
     *
     * @return null
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * cf la doc de symfony :
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        $this->addUserToRequest($event->getRequest());
    }

    /**
     * Generate a new cookie with the identity $identity
     *
     * @param array $identity
     * @return string
     */
    public function generate($identity)
    {
        $cookie    = base64_encode(json_encode($identity));
        $signature = $this->rsa->sign($cookie);

        if ($signature === null) {
            throw new \Exception("Error signing cookie");
        }

        $cookie = base64_encode(json_encode([
            "identity"  => $cookie,
            "signature" => $signature,
        ]));

        return $cookie;
    }


    /**
     * Extract cookie information
     */
    public function extract($cookie_string)
    {
        switch (true) {
            case false === ($cookie = base64_decode($cookie_string)):
            case null === ($cookie = json_decode($cookie)):
                throw new HttpException(401, "Cookie decode failed");
                break;
            case !$this->rsa->verify($cookie->identity, $cookie->signature):
                throw new HttpException(401, "Bad Cookie Signature");
                break;
            case false === ($user = base64_decode($cookie->identity)):
            case null === ($user = json_decode($user)):
                throw new HttpException(401, "Identity decode failed");
                break;
        }

        return $user;
    }

    /**
     * Retourne la liste des différents events sur lesquels cette classe va intervenir
     * En l'occurence, avant d'accéder à une des fonction d'un des controlleurs
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // 255 correspond à la plus haute priorité
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 255],
        ];
    }

    /**
     * Sette la valeur de l'expiration du cookie
     *
     * @param string $expiration Durée de vie du cookie généré
     */
    public function setCookieExpiration($expiration)
    {
        $this->expiration = $expiration;
    }
}

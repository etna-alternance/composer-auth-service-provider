<?php
/**
 * Définition de la classe AuthCheckingService.
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
 */

namespace ETNA\Auth\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use ETNA\Auth\EtnaCookieAuthenticatedController;

/**
 * Cette classe est surtout un modèle de service permettant d'ajouter la logique du bundle.
 * Il faut créer un service dans l'application qui extends cette classe et le spécifier dans la configuration.
 *
 * Cela nous permet de pouvoir override la fonction authBeforeFunction comme on le souhaite
 *
 * @abstract
 */
class AuthCheckingService implements EventSubscriberInterface
{
    /**
     * C'est la fonction qui sera appelée par symfony lors d'un des events indiqué par getSubscribedEvents.
     *
     * @param FilterControllerEvent $event L'évènement
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

        if ($controller[0] instanceof EtnaCookieAuthenticatedController) {
            $this->authBeforeFunction($event->getRequest());
        }
    }

    /**
     * Fonction générique pour checker la bonne authentication de la request
     * On peut l'override en héritant de cette classe afin d'avoir d'autres comportements.
     *
     * @abstract
     *
     * @param Request $req La requête HTTP à examiner
     */
    public function authBeforeFunction(Request $req)
    {
        // On autorise les OPTIONS sans auth
        if ('OPTIONS' === $req->getMethod()) {
            return;
        }

        $user = $req->attributes->get('auth.user', null);

        if (!isset($user)) {
            throw new HttpException(401, 'Authorization Required');
        }
    }

    /**
     * Retourne la liste des différents events sur lesquels cette classe va intervenir
     * En l'occurence, avant d'accéder à une des fonction d'un des controlleurs.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => ['onKernelController', 0],
        );
    }
}

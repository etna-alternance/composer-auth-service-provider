<?php
/**
 * Définition de l'interface EtnaCookieAuthenticatedController.
 *
 * @author BLU <dev@etna-alternance.net>
 *
 * @version 3.0.0
 */

declare(strict_types=1);

namespace ETNA\Auth;

/**
 * Cette interface n'éxiste que pour définir les controlleurs qui seront soumis à la fonction authBeforeFunction.
 *
 * Il suffit de l'implémenter dans la classe du controlleur pour voir la magie opérer.
 *
 * Exemple:
 *
 * <pre>
 * class MonControlleur extends Controller implements EtnaCookieAuthenticatedController
 * </pre>
 *
 * @abstract
 */
interface EtnaCookieAuthenticatedController
{
}

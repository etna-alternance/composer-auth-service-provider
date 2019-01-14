# language: fr
Fonctionnalité: Récupérer la clé publique du serveur d'authentification

@foot
Scénario: Si je n'ai pas la clé en local je suis capable d'aller la chercher
    Etant donné que je supprime la clé stockée localement
    Quand       je réinstancie le cookie service
    Alors       je devrais avoir la cle en local

# language: fr
Fonctionnalité: Je génère un cookie depuis une identité

Scénario: Générer un cookie avec toutes les informations nécessaires
    Etant donné que j'override la clé
    Quand       je génère le cookie pour l'identité :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:42"
    }
    """
    Alors       ca devrait s'être bien déroulé
    Et          j'ajoute mon user a la request
    Quand       je vérifie que je suis authentifié
    Alors       ca devrait s'être bien déroulé
    Et          je remet l'ancienne clé

Scénario: Générer un cookie sans clé privée
    Quand       je génère le cookie pour l'identité :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:42"
    }
    """
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Undefined Private Key"

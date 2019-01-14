# language: fr
Fonctionnalité: La fonction authBeforeFunction se déclenche en temps voulu

Scénario: Appeler une route protégée et valider les prérequis
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["adm"]
    }
    """
    Quand       je fais un GET sur /restricted
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["adm"]
    }
    """

Scénario: Appeler une route protégé et ne pas valider les prérequis
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["student"]
    }
    """
    Quand       je fais un GET sur /restricted
    Alors       le status HTTP devrait être 403
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Forbidden"
    """

Scénario: Appeler une route protégée sans être authentifié
    Quand       je fais un GET sur /restricted
    Alors       le status HTTP devrait être 401
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Authorization Required"
    """

Scénario: Mes requêtes options devraient passer quoi qu'il arrive
    Quand       je fais un OPTIONS sur /restricted
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {}
    """

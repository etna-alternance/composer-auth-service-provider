# language: fr
Fonctionnalité: Décoder le cookie et l'ajouter à la request

Scénario: Avec un bon cookie tout devrait bien se passer
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00"
    }
    """
    Quand       j'ajoute mon user a la request
    Alors       le user de la request devrait être :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00"
    }
    """

Scénario: Avec un cookie expiré je ne dois pas avoir de user dans la request
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-07-01 14:35:00"
    }
    """
    Quand       j'ajoute mon user a la request
    Alors       je ne devrais pas avoir de user dans la request

Scénario: Avoir un cookie longue durée
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2010-01-01 10:00:00"
    }
    """
    Et          je sette l'expiration du cookie à "+5years"
    Quand       j'ajoute mon user a la request
    Alors       le user de la request devrait être :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2010-01-01 10:00:00"
    }
    """

Scénario: Tenter de chiffrer le cookie avec une autre clé
    Etant donné que je suis faussement authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2010-01-01 10:00:00"
    }
    """
    Quand       j'ajoute mon user a la request
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Bad Cookie Signature"

Scénario: Tenter un cookie frauduleux
    Etant donné je crée un cookie invalide
    Quand       je tente d'extraire les informations du cookie
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Cookie decode failed"

Scénario: Tenter une identité mal formattée
    Etant donné je crée un cookie contenant une identité invalide
    Quand       je tente d'extraire les informations du cookie
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Identity decode failed"

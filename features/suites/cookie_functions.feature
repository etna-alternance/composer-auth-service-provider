# language: fr
Fonctionnalité: Tester le cookie avec des fonctions du CookieService

Scénario: Je suis authentifié et tout va bien se passer
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["student"]
    }
    """
    Et          j'ajoute mon user a la request
    Quand       je vérifie que je suis authentifié
    Alors       ca devrait s'être bien déroulé

Scénario: Je ne suis pas authentifié et tout ne va pas bien se passer
    Etant donné que j'ajoute mon user a la request
    Quand       je vérifie que je suis authentifié
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Unauthorized"

Scénario: J'ai les bons rôles et tout va bien se passer
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["student"]
    }
    """
    Et          j'ajoute mon user a la request
    Quand       je vérifie que je dispose du rôle "student"
    Alors       ca devrait s'être bien déroulé

Scénario: Je n'ai pas les bons rôles et tout ne va pas bien se passer
    Etant donné que je suis authentifié avec :
    """
    {
        "id": 42,
        "login": "crivis_s",
        "login_date": "2013-08-13 14:35:00",
        "groups": ["student"]
    }
    """
    Et          j'ajoute mon user a la request
    Quand       je vérifie que je dispose du rôle "adm"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Forbidden"

Scénario: Je ne suis pas authentifié et tout ne va pas bien se passer (v2)
    Etant donné que j'ajoute mon user a la request
    Quand       je vérifie que je dispose du rôle "adm"
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "Unauthorized"

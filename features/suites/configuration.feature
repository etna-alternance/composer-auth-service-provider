# language: fr
Fonctionnalité: J'instancie mon bundle puis le configure

Scénario: Configurer le AuthBundle comme il faut
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "good_config.php"
    Et          je boot le kernel
    Alors       ca devrait s'être bien déroulé
    Et          les paramêtres de mon application devraient être :
    """
    {
        "auth.authenticator_url": "#^.*features/suites/requests/../../../tmp/keys/$#",
        "auth.cookie_expiration": "+10minutes",
        "auth.api_path": "^/?",
        "auth.app_name": "that's mah nem",
        "auth.public_key.tmp_path": "#^.*/tmp/public-test.key$#"
    }
    """
    Et          je n'ai plus besoin du kernel de test

Scénario: Configurer le AuthBundle comme il le faut (2)
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "good_config_2.php"
    Et          je boot le kernel
    Alors       ca devrait s'être bien déroulé
    Et          les paramêtres de mon application devraient être :
    """
    {
        "auth.authenticator_url": "http://auth.etna.localhost",
        "auth.cookie_expiration": false,
        "auth.api_path": "^/?",
        "auth.app_name": "another_app_name",
        "auth.public_key.tmp_path": "#^.*/tmp/public-test.key$#"
    }
    """
    Et          je n'ai plus besoin du kernel de test

Scénario: Configurer le AuthBundle avec une conf erronée
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "bad_config.php"
    Et          je boot le kernel
    Alors       ca ne devrait pas s'être bien déroulé
    Et          l'exception devrait avoir comme message "The child node "authenticator_url" at path "auth" must be configured."
    Et          je n'ai plus besoin du kernel de test

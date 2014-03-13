Compte rendu Hotspot Map
=====================


Les développeurs sont souvent amenés à voyager et à travailler loin de leur bureau. C'est pourquoi, un site regroupant des lieux agréables (appelés Hotspots) et disposant d'une connexion internet serait un atout considérable. Un tel site a été développé en binôme dans le cadre d'un projet PHP à l'Institut Supérieur d'Informatique de Modélisation et de leurs Applications (ISIMA).

----------


Outils
---------
Pour la réalisation de ce projet, nous avons utilisé les technologies suivantes :

- Le [micro-framework Silex][1] basé sur les composants de Symfony 2 
- Le gestionnaire de dépendances [Composer][2]
- [Geocoder][3], une bibliothèque de géolocalisation PHP
- [php-cs-fixer][4], un correcteur de standards PHP (PSR-1 & PSR-2)
- [Negociation][5], une bibliothèque PHP qui permet d'obtenir le format de données le plus adapté à la requête du client (voir [Content Negociation][6])

Mise en place du projet
---------
Tout d'abord, il est indispensable de procéder au téléchargement des dépendances du projet. Pour cela, à la racine du projet :

	./composer.phar install
	
Importez le fichier de base de données dans votre base locale :

	/HotspotMap/app/Resources/sql/HotspotMap.sql
	
Définissez les informations de connexion de la base de données dans le fichier `index.php` :
	
	$GLOBALS['DB_DSN'] = 'mysql:host=localhost:3306;dbname=HotspotMap';
    $GLOBALS['DB_USER'] = 'root';
    $GLOBALS['DB_PASSWD'] = 'root';

Dans le répertoire du projet, lancer le serveur php avec le dossier `web` comme racine :

	php -S localhost:8090 -t web/
	
Accédez au site avec l'url `localhost:8090`

Fonctionnalités
---------
Les fonctionnalités implémentées sont les suivantes :

- Implémentation d'une API REST avec des réponses disponibles en xml, json et html
- Authentification d'un administrateur (utilisation de Silex pour l'authentification, avec gestion de rôles)
- Recherche de la position actuelle et affichage des lieux les plus proches
- Utilisation d'une carte Google Map (JavaScript) afin d'afficher les Hotspots
- Interface utilisateur avec page dynamique par l'utilisation de l'API REST (requêtes AJAX)
- Ajout d'un commentaire sur les Hotspots
- Ajout / modification d'un Hotspot
- Un administrateur authentifié peut/doit administrer les lieux et les commentaires ajouté ou mis à jour par un utilisateur
- Bouton *follow* Twitter
- Bouton *tweet* 
- Recherche d'un lieu par l'adresse ou les coordonnées géographiques

Les fonctionnalités manquantes à implémenter sont :

- Filtrage des lieux par critères
- Par souplesse d'utilisation, un utilisateur ne doit pas être authentifié pour consulter les Hotspot ou pour en ajouter

Tests unitaires
---------
Certaines dépendances sont indispensables telles que `phpunit` pour lancer les tests unitaires. Pensez à éxécuter composer :

	./composer.phar install
	
Importez le fichier de base de données dans votre base locale (malgré que les données de test soient automatiquement importée par `phpunit`, il est important de fournir la structure de la base de donnée) :

	/HotspotMap/app/Resources/sql/HotspotMapTest.sql
	
Définissez les informations de connexion à la base de données de test dans le fichier xml `dbconfig.xml` :

	/HotspotMap/tests/config/dbconfig.xml
	
Lors de l'éxécution des tests unitaires, la base de donnée de test est construite avec les fichiers xml placés dans le dossier :

	/HotspotMap/tests/mock
	
Executez le script shell `startTests.sh` pour lancer les tests unitaires :

	./startTests.sh 

	PHPUnit : vendor/bin/phpunit
	Database configuration file : tests/config/dbconfig.xml
	Autoloader : vendor/autoload.php
	Directory to test : tests/
	
	PHPUnit 3.7.32 by Sebastian Bergmann.
	
	Configuration read from /Users/drusy/Dropbox/ISIMA/ZZ3/PHP/HotspotMap/tests/config/dbconfig.xml
	
	.........................
	
	Time: 7.24 seconds, Memory: 13.50Mb
	
	OK (25 tests, 38 assertions)
	
Les tests éxécuté sont présents dans le dossier `/HotspotMap/tests`

  [1]: http://silex.sensiolabs.org/
  [2]: https://getcomposer.org/
  [3]: https://github.com/geocoder-php/Geocoder
  [4]: http://cs.sensiolabs.org/
  [5]: https://github.com/willdurand/Negotiation
  [6]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec12.html
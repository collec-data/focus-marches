# Focus-marche
Le projet Focus Marchés a été conçu par le service Données Ouvertes de Ternum BFC pour faciliter l'exploitation des informations contenues dans les données essentielles de la commande publique.
Il est accessible via plusieurs URLs :
- http://focus-marches.ternum-bfc.fr/
- http://141.95.150.34/

Le projet se découpe en 2 sous-projets :
- Une interface web
- Des scripts d'import

## Installation et configuration


### Installation avec Docker

Si vous disposez de docker et de docker-compose, vous pouvez lancer le serveur en local avec les commandes suivantes :

Une fois cloné ce répertoire à l'aide de

    git clone https://gitlab.csm.ovh/csm/focus-marche.git && cd focus-marche

Construisez le container avec `docker-compose build` et lancez-le avec `docker-compose up`.

Vous pouvez effectuer les migrations (nécessaire seulement au premier lancement) à l'aide de :

    docker run --network focus-marche_default registry.csm.ovh:443/csm/focus-marches-batch/master:latest python import_decp.py
    docker-compose run sirene bundle exec rails db:migrate

La base de donnée sera persistée dans le dossier `/var/lib/mysql` par défaut. Il est possible  de changer l'emplacement d'installation des données ou d'indiquer un emplacement d'installation existante en modifiant la variable d'environnement `MYSQL_DATA` dans le fichier `.env`.

La base de donnée msql de docker se link sur le port 3306, donc assurez vous de ne pas avoir mysql qui tourne déjà sur ce port, ou bien modifiez `docker-compose.yml`

Si votre machine comprend déjà une base installée sur `/var/lib/postgresql`, libre à vous de modifier le fichier `.env` :
```yml
MARIA_DATA=/path/to/other/data_folder
```

Lancez les imports à l'aide des commandes rake (Cf plus bas, partie Mises à jour / Administration) précédées par `docker-compose run sirene`. Par exemple :

    docker-compose run sirene rake sirene_as_api:populate_database






















Il faut maintenant préparer la base de données postgres :

    sudo -u postgres -i
    cd /path/vers/dossier/sirene_as_api
    psql -f postgresql_setup.txt

Assurez vous que tout s'est bien passé :

    bundle exec rails db:create

Puis éxécutez les migrations :

    bundle exec rails db:migrate

Si vous souhaitez utiliser les tests :

    RAILS_ENV=test bundle exec rails db:migrate

Vous pouvez maintenant lancer Solr :

    RAILS_ENV=production bundle exec rake sunspot:solr:start

Peuplez la base de données : Cette commande importe le dernier fichier stock mensuel
ainsi que les mises à jour quotidiennes. Attention, cette commande s'éxécute sur
une base vide.

    bundle exec rake sirene_as_api:populate_database

Si la commande précédente échoue en cours de route ou si la base n'est pas vide,
éxecutez plutôt :

    bundle exec rake sirene_as_api:update_database

C'est prêt ! vous pouvez lancer le serveur :

    bundle exec rails server

## Mises à jour / Administration

Les données ayant changé de format en 2019, nous assurons une continuité de service en convertissant les fichiers du nouveau format vers l'ancien. Il est donc pour le moment toujours possible d'utiliser les endpoints V1 et V2, bien qu'ils soient dépréciés.

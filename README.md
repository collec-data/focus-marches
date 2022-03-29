# Focus-marche
Le projet Focus Marchés a été conçu par le service Données Ouvertes de Ternum BFC pour faciliter l'exploitation des informations contenues dans les données essentielles de la commande publique.
Il est accessible via plusieurs URLs :
- http://focus-marches.ternum-bfc.fr/
- http://141.95.150.34/

Le projet se découpe en 2 sous-projets :
- Une interface web (dossier ui)
- Des scripts d'import (dossier batch)

## Installation et configuration


### Installation avec Docker

Si vous disposez de docker et de docker-compose, vous pouvez lancer le serveur avec les commandes suivantes :

Une fois cloné ce répertoire à l'aide de:

    git clone https://gitlab.csm.ovh/csm/focus-marche.git && cd focus-marche

Construisez les container avec `docker-compose build && docker build batch/. -t batch ` et lancez-le avec `docker-compose -d up`.

La base de donnée sera persistée dans le dossier `/var/lib/mysql` par défaut. Il est possible  de changer l'emplacement d'installation des données ou d'indiquer un emplacement d'installation existante en modifiant la variable d'environnement `MYSQL_DATA` dans le fichier `.env`.

La base de donnée msql de docker se link sur le port 3306, donc assurez vous de ne pas avoir mysql qui tourne déjà sur ce port, ou bien modifiez `docker-compose.yml`

Si votre machine comprend déjà une base installée sur `/var/lib/mysql`, libre à vous de modifier le fichier `.env` :
```yml
MARIA_DATA=/path/to/other/data_folder
```

C'est prêt ! vous pouvez accéder au service sur http://HOSTNAME:80


### Mises à jour / Administration
#### Importer les données



**_A partir de fichier au format XML_**

Déposer les fichiers XML dans le répertoire d'entrée ```$DECP_IN```, libre à vous de valoriser la variable d'env  :
```bash
export DECP_IN=/path/to/DECP_IN
```
puis lancez-les imports des fichiers avec
```bash
docker run -it --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch bash
```

#### A partir de l'API ATEXO
    todo

#### A partir d'une URL (exemple url datagouv)
    todo

#### Mise à jour des données entreprise (API SIREN)
lancez la mise à jour des infos 
```bash
docker run -d --network focus-marche_default docker.csm.ovh/csm/focus-marches-batch/master:latest python maj_table_sirene.py
```

#### Mise à jour des données infogreffe (API infogreffe)
    todo
#### Mise à jour des données INSEE (information population)
    todo

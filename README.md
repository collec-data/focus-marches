# Focus-marche
Le projet Focus Marchés a été conçu par le service Données Ouvertes de Ternum BFC pour faciliter l'exploitation des informations contenues dans les données essentielles de la commande publique.
Il est accessible via plusieurs URLs :
- http://focus-marches.ternum-bfc.fr/
- https://focus.recia.solutions/
- https://data.megalis.bretagne.bzh/focus-marches/

Le projet se découpe en 2 sous-projets :
- Une interface web (dossier ui)
- Des scripts d'import (dossier batch)

## Installation et configuration


### Installation avec Docker

Si vous disposez de docker et de docker-compose, vous pouvez lancer le serveur avec les commandes suivantes :

Une fois cloné ce répertoire à l'aide de:

    git clone https://gitlab.csm.ovh/csm/focus-marche.git && cd focus-marche

Construisez les container avec docker-compose build && docker build batch/. -t batch ` et lancez-le avec `docker-compose -d up`.

La base de donnée sera persistée dans le dossier `/var/lib/mysql` par défaut. Il est possible  de changer l'emplacement d'installation des données ou d'indiquer un emplacement d'installation existante en modifiant la variable d'environnement `MYSQL_DATA` dans le fichier `.env`.

La base de donnée msql de docker se link sur le port 3306, donc assurez vous de ne pas avoir mysql qui tourne déjà sur ce port, ou bien modifiez `docker-compose.yml`

Si votre machine comprend déjà une base installée sur `/var/lib/mysql`, libre à vous de modifier le fichier `.env` :
```yml
MARIA_DATA=/path/to/other/data_folder
```

C'est prêt ! vous pouvez accéder au service sur http://HOSTNAME:80


### Personaliser son instance focus marchés
Dupliquer un dossier de personalisation pour creer le votre, par exemple nous allons dupliquer` cp ui/personalisation/arnia  ui/personalisation/monorga`

Dans notre exemple, 
- pour modifier les textes je dois renommer le fichier arnia.po en monorga.po et éditer le fichier avec l'utilitaire poedit (Poedit Translation Editor — https://poedit.net) afin de creer mon catalogue de texte.
- remplacer les différents logos par ceux de mon organisation
- adapter le css si besoin (modification est couleur et police par exemple)
- Enfin, déployer mon instance de focus marche avec la variable **ENVP=monorga**




### Base de données

Les fichiers de création de la base de données sont présent dans le répertoire `sql`. Ces fichiers seront importés automatiquement lors du premier déploiment docker dans l'ordre suivant:
- schema.sql
- data.sql
- index.sql

_cf volumes dans le fichier_ `docker-compose.yml`

Le fichier `.env` contient les informations de connexion à la BDD.


### Paramétrage
Un exemple de fichier de configuration est présent dans le répertoire `batch/scripts/settings/`, fichier `settings.py`

Mettre en place votre propre fichier de configuration à partir de cet exemple. Ci-dessous une description des différents paramètres:

| Paramètre| Valeur possible | Description |
| :-------------|:------------:| :-----|
| DIRECTORY_DECP_IN | ``/decp ``|  Répertoire des fichiers decps à importer  |
| IMPORT_FROM_DIRECTORY |0 ou 1| Active ou non l'import des fichiers decp présent dans  le repertoire DIRECTORY_DECP_IN|
| ATEXO_IMPORT_FROM_API | 0 ou 1 |   Active ou non l'import des marchés depuis l'api Atexo |
| ATEXO_API_URL | https://marches-url.bzh/app.php/api/v1/donnees-essentielles/contrat/format-pivot  |Url de l'API atexo à utliser lors des imports,  ,obligatoire si ATEXO_IMPORT_FROM_API=1 |
| ATEXO_API_TOKEN |https://marches-url.bzhh/auth/aaa_xxxx/xxxxxxxxx|    Url d'obtention d'un token pour l'API atexo ,,obligatoire si ATEXO_IMPORT_FROM_API=1 |
| ATEXO_START_YEAR |2022|  Première année à importer, utilisé en cas d'import avec l'api Atexo, ,obligatoire si ATEXO_IMPORT_FROM_API=1 |
| URL_API_SIREN |2022|  Première année à importer, ,obligatoire si ATEXO_IMPORT_FROM_API=1 |
| URL_FICHIER_INFOS_GREFFE |https://www.data.gouv.fr/fr/datasets/r/8d5774e7-8106-427b-bb6b-790a59d272bd| URL de téléchargement du dataset info greffe |
| DOWNLOAD_INFOS_GREFFE | 0 ou 1| Active ou non le téléchargement du fichier info greffe, si 0 alors le fichier info greffe doit déja étre pésent|
| enable_http_proxy | True ou False         | Active ou non l'utilisation d'un proxy http  |
| proxyDict | ```proxyDict```|   Parémétrage du proxy http |

### Import des données
Lancez l'import avec la commande suivante:
```bash
docker run --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch
```

_TODO expliquer les volumes docker (-v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv)_


### Autres commandes ...

purger les toutes les données:
```bash
docker run --rm ---network focus-marche_default batch python truncate_all.py
```

purger les marchés
```bash
docker run --rm ---network focus-marche_default batch python truncate_table_marche.py
```

purger la table sirene
```bash
docker run --rm ---network focus-marche_default batch python truncate_table_marche.py_sirene.py
```

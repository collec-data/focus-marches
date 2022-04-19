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


### Paramétrage

Editez le fichier de paramétrage : `focus-marche/batch/scripts/settings/settings.py`

| Paramètre| Valeur possible | Description |
| :-------------|:------------:| :-----|
| IMPORT_FROM_DIRECTORY |0 ou 1| Active ou non l'import des fichiers decp présent dans  le repertoire DIRECTORY_DECP_IN|
| DIRECTORY_DECP_IN | 0 ou 1 |  Répertoire des fichiers decps à importer  |
| IMPORT_FROM_API | 0 ou 1 |   Active ou non l'import des marchés depuis l'api Atexo |
| API_URL | https://marches-url.bzh/app.php/api/v1/donnees-essentielles/contrat/format-pivot  |    Url de l'API atexo à utliser lors des imports |
| API_TOKEN |https://marches-url.bzhh/auth/aaa_xxxx/xxxxxxxxx|    Url d'obtention d'un token pour l'API atexo |
| START_YEAR |2022|  Première année à importer, utilisé en cas d'import avec l'api Atexo |
| URL_FICHIER_INFOS_GREFFE |https://www.data.gouv.fr/fr/datasets/r/8d5774e7-8106-427b-bb6b-790a59d272bd| URL de téléchargement du dataset info greffe |
| DOWNLOAD_INFOS_GREFFE | 0 ou 1| Active ou non le téléchargement du fichier info greffe|
| URL_INFO_GREFFE |https://opendata.datainfogreffe.fr/api/records/1.0/search/?dataset=chiffres-cles-2020&q=| URL api info greffe|
| TEMPO_CALL_INFO_GREFFE | entier         |    Valeur de ma temposiration entre les appels à l'api info greffe |
| enable_http_proxy | True ou False         | Active ou non l'utilisation d'un proxy http  |
| proxyDict | ```proxyDict```|   Parémétrage du proxy http |

### Import des données
Lancez l'import avec la commande suivante:
```bash
docker run -it --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch
```


TODO expliquer les volumes docker (-v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv)


### Autres commandes ...

purger les toutes les données:
```bash
docker run -it --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch python 
```


```bash
docker run -it --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch python 
```


```bash
docker run -it --rm -v /home/debian/IN:/decp -v /home/debian/settings.py:/appli/scripts/settings/settings.py -v /home/debian/chiffres-cles-2020.csv:/workdir/chiffres-cles-2020.csv --network focus-marche_default batch python 
```

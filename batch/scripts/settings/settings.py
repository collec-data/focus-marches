import logging

# *****************************
# Environment specific settings
# *****************************

#BASE DE DONNEE
SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://user:password@database:3306/marches_publics?charset=utf8'

#URL
URL_FICHIER_INFOS_GREFFE='https://www.data.gouv.fr/fr/datasets/r/a7790997-0a6a-48fa-b86b-1ef15f2a7552'
URL_INFO_GREFFE='https://opendata.datainfogreffe.fr/api/records/1.0/search/?dataset=chiffres-cles-2020&q='
TEMPO_CALL_INFO_GREFFE=5
URL_API_SIREN_PERSO='http://152.228.212.208:3000/v3'

#LOG
logging.basicConfig(
     filename='../batch-focus.log',
     level=logging.INFO,
     format= '[%(asctime)s] {%(pathname)s:%(lineno)d} %(levelname)s - %(message)s',
     datefmt='%H:%M:%S'
 )

enable_http_proxy=False
proxyDict = {
              "http"  : "http://xxx:xxxx",
              "https" : "http://xxx:xxxx"
            }

WORKDIR='/workdir'

DIRECTORY_DECP_IN='/decp'
IMPORT_FROM_DIRECTORY=1
IMPORT_FROM_API=0
API_URL='https://marches.megalis.bretagne.bzh/app.php/api/v1/donnees-essentielles/contrat/format-pivot'
API_TOKEN='*******************************************************************************************'

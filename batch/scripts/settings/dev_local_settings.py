import logging

# *****************************
# Environment specific settings
# *****************************

#BASE DE DONNEE
SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://user:password@141.95.150.34:3306/marches_publics?charset=utf8'

#URL
URL_INFO_GREFFE='https://opendata.datainfogreffe.fr/api/records/1.0/search/?dataset=chiffres-cles-2020&q='
TEMPO_CALL_INFO_GREFFE=5
URL_API_SIREN_PERSO='http://152.228.212.208:3000/v3'

#LOG
logging.basicConfig(
     filename='batch-focus.log',
     level=logging.INFO,
     format= '[%(asctime)s] {%(pathname)s:%(lineno)d} %(levelname)s - %(message)s',
     datefmt='%H:%M:%S'
 )
enable_http_proxy=False
proxyDict = {
              "http"  : "http://pro01.sib.fr:3128",
              "https" : "http://pro01.sib.fr:3128"
            }



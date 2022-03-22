from decp import import_one_file, recuperer_all_decp_from_api
from settings.settings import IMPORT_FROM_DIRECTORY, DIRECTORY_DECP_IN, IMPORT_FROM_API,WORKDIR
from os import listdir
from os.path import isfile, join

# files = ['../IN/decp-2022.xml', '../IN/decp-2021.xml','../IN/decp-2020.xml','../IN/decp-2019.xml']


#liste des id titulaire et acheteur deja présent en bdd
dict_titu = []
dict_acheteur = []


if IMPORT_FROM_DIRECTORY == 1:
    files = [f for f in listdir(DIRECTORY_DECP_IN) if isfile(join(DIRECTORY_DECP_IN, f))]
    for file in files:
        import_one_file(file,dict_titu,dict_acheteur)


if IMPORT_FROM_API ==1:
    recuperer_all_decp_from_api()
    files = [f for f in listdir(WORKDIR) if isfile(join(WORKDIR, f))]
    for file in files:
        import_one_file(file,dict_titu,dict_acheteur)



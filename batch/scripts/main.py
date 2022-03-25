from decp import importer_decp
from sirene import maj_table_sirene,maj_info_greffe



#ETAPE 1: Import des données essentielles de la commandes publiques
print("DEBUT : ETAPE 1")
importer_decp()

print("DEBUT : ETAPE 2")
#ETAPE 2: mise à jour de la table sirenne
maj_table_sirene()

print("DEBUT : ETAPE 3")
#ETAPE 3 : mise à jour des infos greffe dans la table sirene
maj_info_greffe()

print("DEBUT : ETAPE 4")
#ETAPE 4 : mise à jour table organisme
# TODO Mettre à jour la table organisme à partir des dataset de l'insee
# https://www.insee.fr/fr/statistiques/5009218#consulter ==> médiane niveau de vie
# https://www.insee.fr/fr/statistiques/5395819?sommaire=5395886#consulter => ménage ( agri / artisants ...)
# https://www.insee.fr/fr/information/5369871 => lien vers les data du Recensement 2018
#
#maj_organisme

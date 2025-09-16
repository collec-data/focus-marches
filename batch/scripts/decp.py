import errno
import json
import logging
import os
from functools import lru_cache
from os import listdir
from os.path import isfile, join
from model.object import Lieu, db_session, Titulaire, Acheteur, Marche_titulaires, Marche, engine
from settings.settings import WORKDIR, IMPORT_FROM_DIRECTORY, DIRECTORY_DECP_IN_2022, PURGE_MARCHE


@lru_cache(maxsize=10)
def getLieu(param):
    return Lieu.query.filter(Lieu.code == param).one_or_none()


def get_or_create_workdir():
    # create workdir
    try:
        os.mkdir(WORKDIR)
    except OSError as exc:
        if exc.errno != errno.EEXIST:
            raise
        pass
    return WORKDIR

def isBlank (myString):
    return not (myString and myString.strip())





def import_one_file(file, dict_titu, dict_acheteur):
    print('DEBUT import decp :' + file)
    marche_mappings = []
    marche_titulaire_mappings = []
    titu_mappings = []
    acheteur_mappings = []
    cpt = 0

    with open(file, encoding='utf-8') as fd:
        doc = json.load(fd)
        for marcheJson in doc['marches']['marche']:

            if cpt > 100:
                db_session.bulk_insert_mappings(Titulaire, titu_mappings)
                db_session.bulk_insert_mappings(Acheteur, acheteur_mappings)
                db_session.bulk_insert_mappings(Marche_titulaires, marche_titulaire_mappings)
                db_session.bulk_insert_mappings(Marche, marche_mappings)
                db_session.commit()
                marche_mappings = []
                marche_titulaire_mappings = []
                titu_mappings = []
                acheteur_mappings = []
                cpt = 0


            marche = Marche()

            if ('acheteur' in marcheJson):
                acheteurJson = marcheJson['acheteur']
                if 'id' in acheteurJson:
                    if str(acheteurJson['id'])[0:14] not in dict_acheteur:
                        acheteur = Acheteur()
                        acheteur.id_acheteur = str(acheteurJson['id'])[0:14]
                        acheteur.nom_acheteur = acheteurJson['nom'] if 'nom' in acheteurJson else ''
                        acheteur.nom_ui = acheteurJson['nom'] if 'nom' in acheteurJson else ''
                        dict_acheteur.append(str(acheteurJson['id'])[0:14])
                        acheteur_mappings.append(acheteur.serialize)

                    marche.id_acheteur = str(acheteurJson['id'])[0:14]
                else:
                    logging.error("Pas d'id acheteur, on l'ignore")
                    continue
            else:
                logging.warning("pas d'acheteur")
                continue


            if 'id' in marcheJson:
                if isBlank(marcheJson['id']):
                    logging.error("Pas d'id de marche, on ignore le marche")
                    continue
                marche.id_marche = marche.id_acheteur + "-" + marcheJson['id']
            elif 'uuid' in marcheJson:
                if isBlank(marcheJson['uuid']):
                    logging.error("Pas d'id de marche, on ignore le marche")
                    continue
                marche.id_marche = marcheJson['uuid']
            else:
                logging.error("Pas d'id de marche, on l'ignore")
                continue


            if 'codeCPV' not in marcheJson:
                logging.warning(str(marche.id_marche) + " : pas de code cpv, on l'ignore")
                continue

            try:
                marcheBDD = Marche.query.filter(Marche.id_marche == marche.id_marche).one_or_none()
            except Exception as e:
                logging.exception(e)
                continue

            if marcheBDD is not None:
                logging.debug("Existe deja " + str(marcheBDD.id))
                continue

            if 'montant' in marcheJson:
                marche.montant = float(marcheJson['montant'])
                if marche.montant < 0:
                    marche.montant = marche.montant * -1
            else:
                logging.warning("pas de montant on zappe le marche " + str(marche.id_marche))
                continue

            if marche.montant > 2147483646:
                logging.warning("montant trop eleve " + str(marche.id_marche))
                continue

            if 'objet' in marcheJson:
                marche.objet = marcheJson['objet']

            if 'dureeMois' in marcheJson:
                if str(marcheJson['dureeMois']).isnumeric() and int(marcheJson['dureeMois']) < 255:
                    marche.duree_mois = marcheJson['dureeMois']

            marche.date_notification = marcheJson['dateNotification'] if 'dateNotification' in marcheJson else None
            marche.date_publication_donnees = marcheJson['datePublicationDonnees'] if 'datePublicationDonnees' in marcheJson else None
            marche.date_transmission_etalab = marcheJson['dateTransmissionDonneesEtalab'] if 'dateTransmissionDonneesEtalab' in marcheJson else None

            if 'procedure' in marcheJson:
                if marcheJson['procedure'] == 'Procédure adaptée':
                    marche.id_procedure = 1
                elif marcheJson['procedure'] == "Appel d'offres ouvert":
                    marche.id_procedure = 2
                elif marcheJson['procedure'] == "Appel d'offres restreint":
                    marche.id_procedure = 3
                elif marcheJson['procedure'] == "Procédure concurrentielle avec négociation":
                    marche.id_procedure = 4
                elif marcheJson['procedure'] == "Procédure négociée avec mise en concurrence préalable":
                    marche.id_procedure = 5
                elif marcheJson['procedure'] == "Marché négocié sans publicité ni mise en concurrence préalable":
                    marche.id_procedure = 6
                elif marcheJson['procedure'] == "Dialogue compétitif":
                    marche.id_procedure = 7
                else:
                    marche.id_procedure = 1
            else:
                marche.id_procedure = 1

            if 'formePrix' in marcheJson:
                if marcheJson['formePrix'] == 'Ferme':
                    marche.id_forme_prix = 1
                elif marcheJson['formePrix'] == 'actualisable':
                    marche.id_forme_prix = 2
                elif marcheJson['formePrix'] == 'Révisable':
                    marche.id_forme_prix = 3
                else:
                    marche.id_forme_prix = 1
            else:
                marche.id_forme_prix = 1

            if 'nature' in marcheJson:
                if marcheJson['nature'] == 'Marché':
                    marche.id_nature = 1
                elif marcheJson['nature'] == 'Marché de partenariat':
                    marche.id_nature = 2
                elif marcheJson['nature'] == 'Accord-cadre':
                    marche.id_nature = 3
                elif marcheJson['nature'] == 'Marché subséquent':
                    marche.id_nature = 4
                else:
                    marche.id_nature = 1
            else:
                marche.id_nature = 1

            if isinstance(marcheJson['codeCPV'], str):
                if marcheJson['codeCPV'].isnumeric():
                    marche.code_cpv = int(marcheJson['codeCPV'])
                elif '-' in marcheJson['codeCPV']:
                    tab = marcheJson['codeCPV'].split("-")
                    marche.code_cpv = int(tab[0])
            else:
                marche.code_cpv = int(marcheJson['codeCPV'])

            if marche.code_cpv:
                cpv_deb=str(marche.code_cpv)[0:2]
                if (cpv_deb == '45' ) :
                    marche.categorie = "travaux"
                elif (cpv_deb in ['50','60','70','80','90']) :
                    marche.categorie = 'fournitures'
                else:
                    marche.categorie = "services"
            else:
                #default
                print(f' id_marche{marche.id_marche}  : code cpv non renseigne')
                marche.categorie = 'services'

            if ('titulaires' not in marcheJson or marcheJson['titulaires'] == None or len(marcheJson['titulaires']) < 1):
                logging.error(marche.id_marche + " : pas de titulaire, on l'ignore")
                continue
            elif (len(marcheJson['titulaires']) == 1):
                titulaireJson = marcheJson['titulaires'][0]['titulaire']
                if ('id' in titulaireJson and isinstance(titulaireJson['id'],str)):
                    if str(titulaireJson['id'])[0:14] not in dict_titu:
                        titulaire = Titulaire()
                        titulaire.id_titulaire = str(titulaireJson['id'])[0:14]
                        titulaire.type_identifiant = titulaireJson['typeIdentifiant'] if 'typeIdentifiant' in titulaireJson else ''
                        try:
                            if 'denominationSociale' in titulaireJson:
                                titulaire.denomination_sociale = titulaireJson['denominationSociale'][0:249]
                            else:
                                titulaire.denomination_sociale = ''
                        except Exception:
                            logging.error(marche.id_marche + " : mauvais format denomination_sociale du titulaire")
                            titulaire.denomination_sociale = ''

                        dict_titu.append(str(titulaireJson['id'])[0:14])
                        titu_mappings.append(titulaire.serialize)

                    marche_titulaire = Marche_titulaires()
                    marche_titulaire.id_titulaires = str(titulaireJson['id'])[0:14]
                    marche_titulaire.id_marche = marche.id_marche
                    marche_titulaire_mappings.append(marche_titulaire.serialize)
                else:
                    logging.error(marche.id_marche + " : mauvais format titulaire, on l'ignore")
                    continue

            else:
                for titulaireJson in marcheJson['titulaires']:
                    if ('id' in titulaireJson and isinstance(titulaireJson['id'],str)):
                        if str(titulaireJson['id'])[0:14] not in dict_titu:
                            titulaire = Titulaire()
                            titulaire.id_titulaire = str(titulaireJson['id'])[0:14]
                            titulaire.type_identifiant = titulaireJson['typeIdentifiant'] if 'typeIdentifiant' in titulaireJson else ''
                            try:
                                titulaire.denomination_sociale = titulaireJson['denominationSociale'][0:249] if 'denominationSociale' in titulaireJson else ''
                            except Exception:
                                logging.error(marche.id_marche + " : mauvais format denomination_sociale du titulaire")
                                titulaire.denomination_sociale = ''
                            dict_titu.append(str(titulaireJson['id'])[0:14])
                            titu_mappings.append(titulaire.serialize)

                        marche_titulaire = Marche_titulaires()
                        marche_titulaire.id_titulaires = str(titulaireJson['id'])[0:14]
                        marche_titulaire.id_marche = marche.id_marche
                        marche_titulaire_mappings.append(marche_titulaire.serialize)
                    else:
                        logging.error(marche.id_marche + " : mauvais format titulaire, on l'ignore")
                        continue



            if ('lieuExecution' in marcheJson):
                lieuExecutionJson = marcheJson['lieuExecution']
                lieuBDD = getLieu(lieuExecutionJson['code'])
                if lieuBDD is None:
                    getLieu.cache_clear()
                    lieu = Lieu()
                    lieu.code = lieuExecutionJson['code']
                    lieu.type_code = lieuExecutionJson['typeCode']
                    db_session.add(lieu)
                    #lieu.nom_lieu = lieuExecutionJson['nom']


                lieuBDD = Lieu.query.filter(Lieu.code == lieuExecutionJson['code']).one_or_none()
                if lieuBDD is not None:
                    marche.id_lieu_execution = lieuBDD.id_lieu
            else:
                logging.warning("pas de lieuExecution")

            marche_mappings.append(marche.serialize)
            cpt = cpt + 1


    db_session.bulk_insert_mappings(Titulaire, titu_mappings)
    db_session.bulk_insert_mappings(Acheteur, acheteur_mappings)
    db_session.bulk_insert_mappings(Marche_titulaires, marche_titulaire_mappings)
    db_session.bulk_insert_mappings(Marche, marche_mappings)
    db_session.commit()
    print('FIN import decp :' + file)






def importer_decp_2022():
    dict_titu = []
    dict_acheteur = []

    # PURGE DE LA TABLE MARCHE EN DEBUT D'IMPORT
    if PURGE_MARCHE == 1:
        engine.execute("truncate table marche")
        engine.execute("truncate table marche_titulaires")

    with engine.connect() as con:
        result = con.execute("select id_titulaire from titulaire")
        for row in result:
            dict_titu.append(str(row[0])[0:14])
        result = con.execute("select id_acheteur from acheteur")
        for row in result:
            dict_acheteur.append(str(row[0])[0:14])

    if IMPORT_FROM_DIRECTORY == 1:
        files = [f for f in listdir(DIRECTORY_DECP_IN_2022) if isfile(join(DIRECTORY_DECP_IN_2022, f))]
        for file in files:
            import_one_file(DIRECTORY_DECP_IN_2022 + "/" + file, dict_titu, dict_acheteur)

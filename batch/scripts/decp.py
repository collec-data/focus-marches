import calendar, time
import errno
import logging
import os
from functools import lru_cache
from xml.dom import minidom
from xml.etree import ElementTree
from os import listdir
from os.path import isfile, join
import requests
import xmltodict
from model.object import Lieu, db_session, Titulaire, Acheteur, Marche_titulaires, Marche
from settings.settings import WORKDIR, API_TOKEN, IMPORT_FROM_DIRECTORY, IMPORT_FROM_API, DIRECTORY_DECP_IN, API_URL, \
    START_YEAR


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


def clear_wordir():
    DIR = get_or_create_workdir()
    filelist = [f for f in os.listdir(DIR)]
    for f in filelist:
        os.remove(os.path.join(DIR, f))


def recuperer_decp_in_workdir(annee):
    ANNEE = str(annee)
    print("DEBUT recuperer_decp_in_workdir pour " + ANNEE)
    clear_wordir()
    url_jeton_sdm = API_TOKEN
    try:
        response = requests.get(url_jeton_sdm)
        doc = minidom.parseString(response.text)
        jeton = doc.getElementsByTagName("ticket")[0].firstChild.data

        url_format_pivot = API_URL

        # generation annee
        t = time.localtime()
        ANNEE_EN_COURS = time.strftime('%Y', t)
        MOIS_EN_COURS = time.strftime('%m', t)

        xml_data = None
        month = 1

        # Récupération par mois pour éviter des timeouts
        while month <= 12:
            monthStr = "{:02d}".format(month)
            maxDay = calendar.monthrange(int(annee), month)[1]

            if int(annee) == int(ANNEE_EN_COURS):
                if (month > int(MOIS_EN_COURS)):
                    break

            reponse_export_pivot = requests.post(url_format_pivot, json={
                'token': jeton,
                'format': 'xml',
                'date_notif_min': '01-' + monthStr + '-' + str(ANNEE),
                'date_notif_max': str(maxDay) + '-' + monthStr + '-' + str(ANNEE)
            })

            data = ElementTree.fromstring(reponse_export_pivot.text)
            if xml_data is None:
                xml_data = data
            else:
                xml_data.extend(data)
            month = month + 1

        # Ecriture du fichier dans dossier workdir
        print("ecrire le fichier dans " + get_or_create_workdir() + '/decp-' + str(ANNEE) + '.xml')
        f = open(get_or_create_workdir() + '/decp-' + str(ANNEE) + '.xml', 'w', encoding='utf8')
        if xml_data is not None:
            xmlstr = ElementTree.tostring(xml_data, encoding='utf8', method='xml')
            f.write(xmlstr.decode("utf8"))
        f.close()

    except Exception as e:
        print("Erreur lors de la récupération du jeton SDM", e)

    print("END recuperer_decp_in_workdir pour " + ANNEE)


def recuperer_all_decp_from_api():
    annee_debut = START_YEAR
    # generation annee
    t = time.localtime()
    annee_courante = int(time.strftime('%Y', t))
    annee_a_generer = annee_debut

    while annee_courante >= annee_a_generer:
        recuperer_decp_in_workdir(str(annee_a_generer))
        print("END " + str(annee_a_generer))
        annee_a_generer += 1
    print("END recuperer_all_decp_from_api")


def import_one_file(file, dict_titu, dict_acheteur):
    logging.info('DEBUT fichier :' + file)
    print('DEBUT fichier :' + file)
    marche_mappings = []
    marche_titulaire_mappings = []
    titu_mappings = []
    acheteur_mappings = []
    cpt = 0

    #  Filtrer les marchés
    #  --------------------
    #  * acheteur sans NOM
    #  * titulaires sans SIRET ou sans DENOMINATION SOCIALE
    #  * date antérieure au 1er janvier 2019
    #  * sans montant
    with open(file, encoding='utf-8') as fd:
        doc = xmltodict.parse(fd.read())
        for marcheXml in doc['marches']['marche']:
            if cpt > 1000:
                logging.info("INSERT bulk")
                print("INSERT bulk")
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
            if 'uid' in marcheXml:
                marche.id_marche = marcheXml['uid']
            elif 'uuid' in marcheXml:
                marche.id_marche = marcheXml['uuid']
            else:
                logging.error("Pas d'id de marche, on ignore le marche")

            if 'codeCPV' not in marcheXml:
                logging.warning(str(marche.id_marche) + " : pas de code cpv, on l'ignore")
                continue

            marcheBDD = Marche.query.filter(Marche.id_marche == marche.id_marche).one_or_none()

            if marcheBDD is not None:
                logging.debug("Existe deja " + str(marcheBDD.id))
                continue
            if 'montant' in marcheXml:
                marche.montant = float(marcheXml['montant'])
                if marche.montant < 0:
                    marche.montant = marche.montant * -1
                marche.objet = marcheXml['objet']
            else:
                logging.debug("pas de montant on zappe le marche " + str(marcheBDD.id))
                continue

            if 'dureeMois' in marcheXml:
                if len(str(marcheXml['dureeMois'])) < 4:
                    marche.duree_mois = marcheXml['dureeMois']

            marche.date_notification = marcheXml['dateNotification'] if 'dateNotification' in marcheXml else None
            marche.date_publication_donnees = marcheXml[
                'datePublicationDonnees'] if 'datePublicationDonnees' in marcheXml else None
            marche.date_transmission_etalab = marcheXml[
                'dateTransmissionDonneesEtalab'] if 'dateTransmissionDonneesEtalab' in marcheXml else None

            if 'procedure' in marcheXml:
                if marcheXml['procedure'] == 'Procédure adaptée':
                    marche.id_procedure = 1
                elif marcheXml['procedure'] == "Appel d'offres ouvert":
                    marche.id_procedure = 2
                elif marcheXml['procedure'] == "Appel d'offres restreint":
                    marche.id_procedure = 3
                elif marcheXml['nature'] == "Procédure concurrentielle avec négociation":
                    marche.id_procedure = 4
                elif marcheXml['nature'] == "Procédure négociée avec mise en concurrence préalable":
                    marche.id_procedure = 5
                elif marcheXml['nature'] == "Marché négocié sans publicité ni mise en concurrence préalable":
                    marche.id_procedure = 6
                elif marcheXml['nature'] == "Dialogue compétitif":
                    marche.id_procedure = 7
                else:
                    marche.id_procedure = 1
            else:
                marche.id_procedure = 1

            if 'formePrix' in marcheXml:
                if marcheXml['formePrix'] == 'Ferme':
                    marche.id_forme_prix = 1
                elif marcheXml['formePrix'] == 'actualisable':
                    marche.id_forme_prix = 2
                elif marcheXml['formePrix'] == 'Révisable':
                    marche.id_forme_prix = 3
                else:
                    marche.id_forme_prix = 1
            else:
                marche.id_forme_prix = 1

            if 'nature' in marcheXml:
                if marcheXml['nature'] == 'Marché':
                    marche.id_nature = 1
                elif marcheXml['nature'] == 'Marché de partenariat':
                    marche.id_nature = 2
                elif marcheXml['nature'] == 'Accord-cadre':
                    marche.id_nature = 3
                elif marcheXml['nature'] == 'Marché subséquent':
                    marche.id_nature = 4
                else:
                    marche.id_nature = 1
            else:
                marche.id_nature = 1

            if isinstance(marcheXml['codeCPV'], str):
                if marcheXml['codeCPV'].isnumeric():
                    marche.code_cpv = int(marcheXml['codeCPV'])
                elif '-' in marcheXml['codeCPV']:
                    tab = marcheXml['codeCPV'].split("-")
                    marche.code_cpv = int(tab[0])
            else:
                marche.code_cpv = int(marcheXml['codeCPV'])

            if marche.code_cpv:
                if marche.code_cpv > 49999999:
                    marche.categorie = 'services'
                elif marche.code_cpv < 45000000:
                    marche.categorie = 'fournitures'
                else:
                    marche.categorie = "travaux"

            if ('titulaires' not in marcheXml or marcheXml['titulaires'] == None or len(marcheXml['titulaires']) < 1):
                logging.error(marche.id_marche + " : pas de titulaire, on l'ignore")
                continue
            elif (len(marcheXml['titulaires']) == 1):
                titulaireXml = marcheXml['titulaires']['titulaire']
                # titulaireBDD = Titulaire.query.filter(Titulaire.id_titulaire == titulaireXml['id']).one_or_none()
                # Le titulaire existe t'il déja en bdd ?
                if str(titulaireXml['id'])[0:14] not in dict_titu:
                    titulaire = Titulaire()
                    titulaire.id_titulaire = str(titulaireXml['id'])[0:14]
                    titulaire.type_identifiant = titulaireXml['typeIdentifiant']
                    titulaire.denomination_sociale = titulaireXml['denominationSociale']
                    dict_titu.append(str(titulaireXml['id'])[0:14])
                    titu_mappings.append(titulaire.serialize)

                marche_titulaire = Marche_titulaires()
                marche_titulaire.id_titulaires = str(titulaireXml['id'])[0:14]
                marche_titulaire.id_marche = marche.id_marche
                marche_titulaire_mappings.append(marche_titulaire.serialize)

            else:
                for titulaireXml in marcheXml['titulaires']['titulaire']:
                    # titulaireBDD = Titulaire.query.filter(Titulaire.id_titulaire == titulaireXml['id']).one_or_none()
                    # Le titulaire existe t'il déja en bdd ?
                    if str(titulaireXml['id'])[0:14] not in dict_titu:
                        titulaire = Titulaire()
                        titulaire.id_titulaire = str(titulaireXml['id'])[0:14]
                        titulaire.type_identifiant = titulaireXml['typeIdentifiant']
                        titulaire.denomination_sociale = titulaireXml['denominationSociale']
                        dict_titu.append(str(titulaireXml['id'])[0:14])
                        titu_mappings.append(titulaire.serialize)

                    marche_titulaire = Marche_titulaires()
                    marche_titulaire.id_titulaires = str(titulaireXml['id'])[0:14]
                    marche_titulaire.id_marche = marche.id_marche
                    marche_titulaire_mappings.append(marche_titulaire.serialize)

            if ('acheteur' in marcheXml):
                acheteurXml = marcheXml['acheteur']
                # acheteurBDD = Acheteur.query.filter(Acheteur.id_acheteur == acheteurXml['id']).one_or_none()
                # L'acheteur existe t'il déja en bdd ?
                if str(acheteurXml['id'])[0:14] not in dict_acheteur:
                    # creation acheteur
                    acheteur = Acheteur()
                    acheteur.id_acheteur = str(acheteurXml['id'])[0:14]
                    acheteur.nom_acheteur = acheteurXml['nom']
                    acheteur.nom_ui = acheteurXml['nom']
                    dict_acheteur.append(str(acheteurXml['id'])[0:14])
                    acheteur_mappings.append(acheteur.serialize)

                # on valorise l'acheteur_id sur le marche
                marche.id_acheteur = str(acheteurXml['id'])[0:14]
            else:
                logging.warning("pas d'acheteur")
                continue

            if ('lieuExecution' in marcheXml):
                lieuExecutionXml = marcheXml['lieuExecution']
                Lieu.query.filter(Lieu.code == lieuExecutionXml['code']).one_or_none()
                lieuBDD = getLieu(lieuExecutionXml['code'])
                # L'acheteur existe t'il déja en bdd ?
                if lieuBDD is None:
                    getLieu.cache_clear()
                    lieu = Lieu()
                    lieu.code = lieuExecutionXml['code']
                    lieu.type_code = lieuExecutionXml['typeCode']
                    lieu.nom_lieu = lieuExecutionXml['nom']
                    db_session.add(lieu)
                    db_session.commit()

                lieuBDD = Lieu.query.filter(Lieu.code == lieuExecutionXml['code']).one_or_none()
                marche.id_lieu_execution = lieuBDD.id_lieu
            else:
                logging.warning("pas de lieuExecution")

            marche_mappings.append(marche.serialize)
            cpt = cpt + 1
    logging.info("INSERT bulk")
    print('LAST INSERT bulk for ' + file)
    db_session.bulk_insert_mappings(Titulaire, titu_mappings)
    db_session.bulk_insert_mappings(Acheteur, acheteur_mappings)
    db_session.bulk_insert_mappings(Marche_titulaires, marche_titulaire_mappings)
    db_session.bulk_insert_mappings(Marche, marche_mappings)
    db_session.commit()


def importer_decp():
    dict_titu = []
    dict_acheteur = []

    if IMPORT_FROM_DIRECTORY == 1:
        files = [f for f in listdir(DIRECTORY_DECP_IN) if isfile(join(DIRECTORY_DECP_IN, f))]
        for file in files:
            import_one_file(DIRECTORY_DECP_IN+"/"+file, dict_titu, dict_acheteur)

    if IMPORT_FROM_API == 1:
        recuperer_all_decp_from_api()
        files = [f for f in listdir(WORKDIR) if isfile(join(WORKDIR, f))]
        for file in files:
            import_one_file(WORKDIR+"/"+file, dict_titu, dict_acheteur)

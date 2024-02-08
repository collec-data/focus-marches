from model.object import InfoEtablissement, Sirene, db_session, engine
from settings.settings import enable_http_proxy, proxyDict, URL_FICHIER_INFOS_GREFFE, WORKDIR, \
    DOWNLOAD_INFOS_GREFFE, URL_API_OPENDATASOFT
import datetime, logging, requests, sqlalchemy, urllib
from sqlalchemy import text
import pandas as pd

request_acheteur = text("""select id_acheteur FROM acheteur WHERE id_acheteur NOT IN  (SELECT id_sirene FROM sirene)""")
request_titulaire = text(
    """select id_titulaire FROM titulaire WHERE id_titulaire NOT IN  (SELECT id_sirene FROM sirene)""")
request_infogreffe = text("""select siren,nic FROM sirene where fiche_identite is null""")

sql_insert_sirene = """INSERT INTO `sirene` (`id_sirene`, `statut`, `date`, `siren`,`nic`, `siret`, `dateCreationEtablissement`, `trancheEffectifsEtablissement`,
           `anneeEffectifsEtablissement`, `activitePrincipaleRegistreMetiersEtablissement`, `etatAdministratifUniteLegale`, `statutDiffusionUniteLegale`,
           `dateCreationUniteLegale`, `categorieJuridiqueUniteLegale`, `denominationUniteLegale`, `sigleUniteLegale`,`activitePrincipaleUniteLegale`,
           `nomenclatureActivitePrincipaleUniteLegale`, `caractereEmployeurUniteLegale`, `trancheEffectifsUniteLegale`,`anneeEffectifsUniteLegale`, `nicSiegeUniteLegale`,
           `categorieEntreprise`, `anneeCategorieEntreprise`,`complementAdresseEtablissement`, `numeroVoieEtablissement`, `indiceRepetitionEtablissement`, `typeVoieEtablissement`,
           `libelleVoieEtablissement`, `codePostalEtablissement`,`libelleCommuneEtablissement`, `codeCommuneEtablissement`, `codeCedexEtablissement`, `libelleCedexEtablissement`,
           `codePaysEtrangerEtablissement`, `libellePaysEtrangerEtablissement`, `latitude`, `longitude`) 
            VALUE(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"""
sql_insert_sirene_ko = """INSERT INTO `sirene` (`id_sirene`, `statut`, `date`) VALUE(%s,%s,%s)"""
sql_update_siren_with_infogreffe = """UPDATE sirene 
                                        SET millesime_1='%s', millesime_2='%s', millesime_3=R,
                                            ca_1='%s', ca_2='%s', ca_3='%s',
                                            resultat_1='%s', resultat_2='%s', resultat_3='%s',
                                            effectif_1='%s', effectif_2='%s', effectif_3='%s',
                                            fiche_identite='%s',
                                        WHERE siren='%s'"""


def insert_info_api_siren(con, request):
    print("DEBUT insert_info_api_siren")

    result = con.execute(request);
    today = datetime.date.today()
    todayStr = today.isoformat()

    for id_siret in result.cursor:

        siret = str(id_siret[0])
        infoEtablissement = None

        # Recherche via siret dans l'api SIRENE V3 consolidée - France
        if enable_http_proxy:
            r = requests.get(f"{URL_API_OPENDATASOFT}/records?where=siret%3D{siret}&limit=20", proxies=proxyDict)
        else:
            r = requests.get(f"{URL_API_OPENDATASOFT}/records?where=siret%3D{siret}&limit=20")

        try:
            #on parse la reponse
            reponse = r.json()

            if (r.status_code == 200):
                # si la reponse contient des données

                if reponse['total_count'] > 0:
                    infoEtablissement = valorisation_infoEtablissement(reponse)
                # si la reponse ne contient pas de données
                # on recherche avec le siren dans l'api SIRENE V3 consolidée - France
                else:
                    siren = id_siret[0][0:9]
                    if (siren.isnumeric()):
                        if enable_http_proxy:
                            r = requests.get(
                                f"{URL_API_OPENDATASOFT}/records?where=siren%3D{siren}%20and%20etablissementsiege%3D\"oui\"&limit=20",
                                proxies=proxyDict)
                        else:
                            r = requests.get(
                                f"{URL_API_OPENDATASOFT}/records?where=siren%3D{siren}%20and%20etablissementsiege%3D\"oui\"&limit=20")

                        reponse = r.json()
                        # si la reponse est ok
                        if (r.status_code == 200):
                            # si la reponse ne contient pas de données
                            if reponse['total_count'] > 0:
                                infoEtablissement = valorisation_infoEtablissement(reponse)

            # mise à jour de la table sirene si on a réussi à récupérer des données
            if infoEtablissement is not None:
                print("Mise à jour de la table sirene pour le siret : " + infoEtablissement.siret)
                update_table_sirene(con, id_siret, infoEtablissement, r, todayStr)
            else:
                print("Aucune information trouvée pour le siret : " + id_siret[0])

        except sqlalchemy.exc.IntegrityError as e:
            logging.info(id_siret[0] + ' deja présent')


def update_table_sirene(con, id_siret, infoEtablissement, r, todayStr):
    try:
        record = (
            id_siret[0],
            str(r.status_code),
            todayStr,
            infoEtablissement.siren,
            infoEtablissement.nic,
            infoEtablissement.siret,
            infoEtablissement.dateCreationEtablissement,
            infoEtablissement.trancheEffectifsEtablissement[
            0:9] if infoEtablissement.trancheEffectifsEtablissement != None else '',
            infoEtablissement.anneeEffectifsEtablissement,
            infoEtablissement.activitePrincipaleRegistreMetiersEtablissement if infoEtablissement.activitePrincipaleRegistreMetiersEtablissement != None else '',
            infoEtablissement.etatAdministratifUniteLegale,
            infoEtablissement.statutDiffusionUniteLegale,
            infoEtablissement.dateCreationUniteLegale,
            infoEtablissement.categorieJuridiqueUniteLegale,
            infoEtablissement.denominationUniteLegale,
            infoEtablissement.sigleUniteLegale if infoEtablissement.sigleUniteLegale != None else '',
            infoEtablissement.activitePrincipaleUniteLegale,
            infoEtablissement.nomenclatureActivitePrincipaleUniteLegale,
            infoEtablissement.caractereEmployeurUniteLegale,
            infoEtablissement.trancheEffectifsUniteLegale[
            0:9] if infoEtablissement.trancheEffectifsUniteLegale != None else '',
            infoEtablissement.anneeEffectifsUniteLegale,
            infoEtablissement.nicSiegeUniteLegale,
            infoEtablissement.categorieEntreprise,
            infoEtablissement.anneeCategorieEntreprise,
            infoEtablissement.complementAdresseEtablissement if infoEtablissement.complementAdresseEtablissement != None else '',
            infoEtablissement.numeroVoieEtablissement,
            infoEtablissement.indiceRepetitionEtablissement if infoEtablissement.indiceRepetitionEtablissement != None else '',
            infoEtablissement.typeVoieEtablissement,
            infoEtablissement.libelleVoieEtablissement,
            infoEtablissement.codePostalEtablissement,
            infoEtablissement.libelleCommuneEtablissement,
            infoEtablissement.codeCommuneEtablissement,
            infoEtablissement.codeCedexEtablissement if infoEtablissement.codeCedexEtablissement != None else '',
            infoEtablissement.libelleCedexEtablissement if infoEtablissement.libelleCedexEtablissement != None else '',
            infoEtablissement.codePaysEtrangerEtablissement if infoEtablissement.codePaysEtrangerEtablissement != None else '',
            infoEtablissement.libellePaysEtrangerEtablissement if infoEtablissement.libellePaysEtrangerEtablissement != None else '',
            infoEtablissement.latitude if infoEtablissement.latitude != None else '',
            infoEtablissement.longitude if infoEtablissement.longitude != None else '')
        con.execute(sql_insert_sirene, record)

    except sqlalchemy.exc.IntegrityError:
        logging.info(id_siret[0] + ' deja présent')


def valorisation_infoEtablissement(reponse):
    infoEtablissement = InfoEtablissement()
    result = reponse['results'][0]
    infoEtablissement.siret = result.get('siret')
    infoEtablissement.siren = result.get('siren')
    infoEtablissement.nic = result.get('nic')
    infoEtablissement.dateCreationEtablissement = result.get('datecreationetablissement')
    infoEtablissement.trancheEffectifsEtablissement = result.get('trancheeffectifsetablissement')
    infoEtablissement.anneeEffectifsEtablissement = result.get('anneeeffectifsetablissement')
    infoEtablissement.activitePrincipaleRegistreMetiersEtablissement = result.get(
        'activiteprincipaleregistremetiersetablissement')
    infoEtablissement.etatAdministratifUniteLegale = result.get('etatadministratifunitelegale')
    infoEtablissement.statutDiffusionUniteLegale = result.get('statutdiffusionunitelegale')
    infoEtablissement.dateCreationUniteLegale = result.get('datecreationunitelegale')
    infoEtablissement.categorieJuridiqueUniteLegale = result.get('categoriejuridiqueunitelegale')
    infoEtablissement.denominationUniteLegale = result.get('denominationunitelegale')
    infoEtablissement.sigleUniteLegale = result.get('sigleunitelegale')
    infoEtablissement.activitePrincipaleUniteLegale = result.get('activiteprincipaleunitelegale')
    infoEtablissement.nomenclatureActivitePrincipaleUniteLegale = result.get(
        'nomenclatureactiviteprincipaleunitelegale')
    infoEtablissement.caractereEmployeurUniteLegale = result.get('caractereemployeurunitelegale')
    infoEtablissement.trancheEffectifsUniteLegale = result.get('trancheeffectifsunitelegale')
    infoEtablissement.anneeEffectifsUniteLegale = result.get('anneeeffectifsunitelegale')
    infoEtablissement.nicSiegeUniteLegale = result.get('nicsiegeunitelegale')
    infoEtablissement.categorieEntreprise = result.get('categorieentreprise')
    infoEtablissement.anneeCategorieEntreprise = result.get('anneecategorieentreprise')
    infoEtablissement.complementAdresseEtablissement = result.get('complementadresseetablissement')
    infoEtablissement.numeroVoieEtablissement = result.get('numerovoieetablissement')
    infoEtablissement.indiceRepetitionEtablissement = result.get('indicerepetitionetablissement')
    infoEtablissement.typeVoieEtablissement = result.get('typevoieetablissement')
    infoEtablissement.libelleVoieEtablissement = result.get('libellevoieetablissement')
    infoEtablissement.codePostalEtablissement = result.get('codepostaletablissement')
    infoEtablissement.libelleCommuneEtablissement = result.get('libellecommuneetablissement')
    infoEtablissement.codeCommuneEtablissement = result.get('codecommuneetablissement')
    infoEtablissement.codeCedexEtablissement = result.get('codecedexetablissement')
    infoEtablissement.libelleCedexEtablissement = result.get('libellecedexetablissement')
    infoEtablissement.codePaysEtrangerEtablissement = result.get('codepaysetrangeretablissement')
    infoEtablissement.libellePaysEtrangerEtablissement = result.get(
        'libellepaysetrangeretablissement')

    geo_info = result.get('geolocetablissement', {})
    if geo_info is not None:
        infoEtablissement.latitude = geo_info.get('lat')
        infoEtablissement.longitude = geo_info.get('lon')
    else:
        infoEtablissement.latitude = ""
        infoEtablissement.longitude = ""
    return infoEtablissement


def complete_with_infogreffe(con, request, df):
    print("DEBUT complete_with_infogreffe")
    result = con.execute(request)
    nb_maj = 0
    nb_not_found = 0
    cpt_update = 0
    for siren, nic in result.cursor:
        cpt_update = cpt_update + 1
        if cpt_update > 499:
            db_session.commit()
            cpt_update = 0
            print('maj:+' + str(nb_maj) + ' / notFound:' + str(nb_not_found))
        try:
            info = df.query("(siren=='" + siren + "') and (nic=='" + nic + "')")
            sirene_record = Sirene.query.filter(Sirene.siret == siren + nic).one()
            if not info.empty:
                if str(info['millesime_1'].values[0]) != 'nan':
                    sirene_record.millesime_1 = info['millesime_1'].values[0]
                if str(info['millesime_2'].values[0]) != 'nan':
                    sirene_record.millesime_2 = info['millesime_2'].values[0]
                if str(info['millesime_3'].values[0]) != 'nan':
                    sirene_record.millesime_3 = info['millesime_3'].values[0]

                if str(info['ca_1'].values[0]) != 'nan':
                    sirene_record.ca_1 = info['ca_1'].values[0]
                if str(info['ca_2'].values[0]) != 'nan':
                    sirene_record.ca_2 = info['ca_2'].values[0]
                if str(info['ca_3'].values[0]) != 'nan':
                    sirene_record.ca_3 = info['ca_3'].values[0]

                if str(info['resultat_1'].values[0]) != 'nan':
                    sirene_record.resultat_1 = info['resultat_1'].values[0]
                if str(info['resultat_2'].values[0]) != 'nan':
                    sirene_record.resultat_2 = info['resultat_2'].values[0]
                if str(info['resultat_3'].values[0]) != 'nan':
                    sirene_record.resultat_3 = info['resultat_3'].values[0]

                if str(info['effectif_1'].values[0]) != 'nan':
                    sirene_record.effectif_1 = info['effectif_1'].values[0]
                if str(info['effectif_2'].values[0]) != 'nan':
                    sirene_record.effectif_2 = info['effectif_2'].values[0]
                if str(info['effectif_3'].values[0]) != 'nan':
                    sirene_record.effectif_3 = info['effectif_3'].values[0]

                if str(info['fiche_identite'].values[0]) != 'nan':
                    sirene_record.fiche_identite = info['fiche_identite'].values[0]
                db_session.add(sirene_record)
                nb_maj = nb_maj + 1
            else:
                sirene_record.fiche_identite = "https://www.infogreffe.fr/recherche-siret-entreprise/chercher-siret-entreprise.html"
                db_session.add(sirene_record)
                nb_not_found = nb_not_found + 1

        except Exception as e:
            logging.exception(e)
            print(e)

    db_session.commit()

    print("FIN  complete_with_infogreffe")


def load_infogreffe():
    if DOWNLOAD_INFOS_GREFFE != 0:
        print('Debut du telechargement du fichier ...' + URL_FICHIER_INFOS_GREFFE)
        urllib.request.urlretrieve(URL_FICHIER_INFOS_GREFFE, WORKDIR + '/chiffres-cles-2020.csv')
        print('fin du telechargement du fichier ...' + URL_FICHIER_INFOS_GREFFE)

    print('Parsing info greffe...')
    return pd.read_csv(WORKDIR + '/chiffres-cles-2020.csv', sep=';', index_col='siren',
                       usecols=['siren', 'nic', 'millesime_1', 'millesime_2', 'millesime_3', 'ca_1', 'ca_2', 'ca_3',
                                'resultat_1', 'resultat_2', 'resultat_3', 'effectif_1', 'effectif_2', 'effectif_3',
                                'fiche_identite'],
                       dtype={'siren': 'str', 'nic': 'str', 'effectif_1': 'str', 'effectif_2': 'str',
                              'effectif_3': 'str', 'resultat_1': 'str', 'resultat_2': 'str', 'resultat_3': 'str',
                              'ca_1': 'str', 'ca_2': 'str', 'ca_3': 'float64', 'millesime_1': 'str',
                              'millesime_2': 'str',
                              'millesime_3': 'str'})


def maj_info_greffe():
    # chargement du fichier info greffe
    df = load_infogreffe()
    with engine.connect() as con:
        complete_with_infogreffe(con, request_infogreffe, df)


def maj_table_sirene():
    with engine.connect() as con:
        # result = con.execute("truncate table sirene");
        insert_info_api_siren(con, request_titulaire)
        insert_info_api_siren(con, request_acheteur)

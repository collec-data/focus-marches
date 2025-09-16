from model.object import InfoEtablissement, Sirene, db_session, engine
from settings.settings import enable_http_proxy, proxyDict, URL_FICHIER_INFOS_GREFFE, WORKDIR, \
    DOWNLOAD_INFOS_GREFFE, URL_API_OPENDATASOFT, URL_API_SIREN_PERSO, TOKEN_API_SIREN_PERSO
import datetime, logging, requests, sqlalchemy, urllib
from sqlalchemy import text
import pandas as pd
from stdnum.fr import siret as siretValidator
from stdnum.fr import siren as sirenValidator

request_acheteur = text("""select id_acheteur FROM acheteur WHERE id_acheteur NOT IN  (SELECT id_sirene FROM sirene)""")
request_titulaire = text(
    """select id_titulaire FROM titulaire WHERE id_titulaire NOT IN  (SELECT id_sirene FROM sirene)""")
request_infogreffe = text("""select siren,nic FROM sirene where fiche_identite is null""")

request_update_titulaire = text(
    "UPDATE titulaire SET denomination_sociale=:denomination WHERE id_titulaire=:id_titulaire"
)
request_update_acheteur = text(
    "UPDATE acheteur  SET nom_acheteur=:denomination,nom_ui=:denomination  WHERE id_acheteur=:id_acheteur"
)


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
    logging.info("DEBUT insert_info_api_siren")

    result = con.execute(request)
    today = datetime.date.today()
    todayStr = today.isoformat()

    for id_siret in result.cursor:

        try:
            siretValidator.validate(id_siret [0])
        except Exception:
            logging.error(f"{id_siret[0]} n'est pas un siret valide")
            continue

        if '00000000000000' == id_siret[0]:
            logging.error(f"{id_siret[0]} n'est pas un siret valide")
            continue

        siret = id_siret[0]

        headers = {
            "Authorization": f"Bearer {TOKEN_API_SIREN_PERSO}"
        }

        # Recherche via siret dans l'api SIRENE V3 consolidée - France
        if enable_http_proxy:
            r = requests.get(f"{URL_API_SIREN_PERSO}/insee/sirene/etablissements/{siret}",headers=headers,proxies=proxyDict,
                             timeout=5)
        else:
            r = requests.get(f"{URL_API_SIREN_PERSO}/insee/sirene/etablissements/{siret}",headers=headers,timeout=5)

        try:
            # on parse la reponse
            reponse = r.json()

            if (r.status_code == 200):
                # si la reponse contient des données
                infoEtablissement = valorisation_infoEtablissement(reponse)

            # mise à jour de la table sirene si on a réussi à récupérer des données
            if infoEtablissement is not None:
                logging.info(f"AMise à jour de la table sirene pour le siret :  {id_siret[0]}")
                update_table_sirene(con, id_siret, infoEtablissement, r, todayStr)
                # update table acheteur
                con.execute(request_update_acheteur, {'id_acheteur': infoEtablissement.siret, 'denomination': infoEtablissement.denominationUniteLegale})
                #update table titulaire
                con.execute(request_update_titulaire, {'id_titulaire': infoEtablissement.siret, 'denomination': infoEtablissement.denominationUniteLegale})
            else:
                logging.info(f"Aucune information trouvée pour le siret : {id_siret[0]}")
                print(f"Aucune information trouvée pour le siret : {id_siret[0]}")


        except sqlalchemy.exc.IntegrityError as e:
            logging.warning(f"{id_siret[0]}  deja présent")
        except Exception as e:
            logging.error(f"{id_siret[0]}  deja présent")



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
            0:9] if infoEtablissement.trancheEffectifsEtablissement is not None else '',
            infoEtablissement.anneeEffectifsEtablissement,
            infoEtablissement.activitePrincipaleRegMet if infoEtablissement.activitePrincipaleRegMet is not None else '',
            infoEtablissement.etatAdministratifUniteLegale,
            infoEtablissement.statutDiffusionUniteLegale,
            infoEtablissement.dateCreationUniteLegale,
            infoEtablissement.categorieJuridiqueUniteLegale,
            infoEtablissement.denominationUniteLegale,
            infoEtablissement.sigleUniteLegale if infoEtablissement.sigleUniteLegale is not None else '',
            infoEtablissement.activitePrincipaleUniteLegale,
            infoEtablissement.nomenclatureActivitePrincipaleUniteLegale,
            infoEtablissement.caractereEmployeurUniteLegale,
            infoEtablissement.trancheEffectifsUniteLegale[
            0:9] if infoEtablissement.trancheEffectifsUniteLegale is not None else '',
            infoEtablissement.anneeEffectifsUniteLegale,
            infoEtablissement.nicSiegeUniteLegale,
            infoEtablissement.categorieEntreprise,
            infoEtablissement.anneeCategorieEntreprise,
            infoEtablissement.complementAdresseEtablissement if infoEtablissement.complementAdresseEtablissement is not None else '',
            infoEtablissement.numeroVoieEtablissement,
            infoEtablissement.indiceRepetitionEtablissement if infoEtablissement.indiceRepetitionEtablissement is not None else '',
            infoEtablissement.typeVoieEtablissement,
            infoEtablissement.libelleVoieEtablissement,
            infoEtablissement.codePostalEtablissement,
            infoEtablissement.libelleCommuneEtablissement,
            infoEtablissement.codeCommuneEtablissement,
            infoEtablissement.codeCedexEtablissement if infoEtablissement.codeCedexEtablissement is not None else '',
            infoEtablissement.libelleCedexEtablissement if infoEtablissement.libelleCedexEtablissement is not None else '',
            infoEtablissement.codePaysEtrangerEtablissement if infoEtablissement.codePaysEtrangerEtablissement is not None else '',
            infoEtablissement.libellePaysEtrangerEtablissement if infoEtablissement.libellePaysEtrangerEtablissement is not None else '',
            infoEtablissement.latitude if infoEtablissement.latitude is not None else '',
            infoEtablissement.longitude if infoEtablissement.longitude is not None else '')
        con.execute(sql_insert_sirene, record)

    except sqlalchemy.exc.IntegrityError:
        logging.info(id_siret[0] + ' deja présent')


def valorisation_infoEtablissement(reponse):
    infoEtablissement = InfoEtablissement()
    result = reponse['data']
    infoEtablissement.siret = result.get('siret')
    print("SIRET: " + infoEtablissement.siret)
    infoEtablissement.siren = result.get('unite_legale').get('siren')
    infoEtablissement.nic = result.get('siret')[9:15]
    if (result.get('date_creation')):

        if result.get('date_creation') < 0:
            infoEtablissement.dateCreationEtablissement = (datetime.datetime(1970, 1, 1) + datetime.timedelta(seconds=result.get('date_creation'))).strftime("%Y-%m-%d")
        else:
            infoEtablissement.dateCreationEtablissement = datetime.datetime.fromtimestamp(result.get('date_creation')).strftime("%Y-%m-%d")

    # todo mapping
    infoEtablissement.trancheEffectifsEtablissement = result.get('tranche_effectif_salarie').get('intitule')
    infoEtablissement.anneeEffectifsEtablissement = result.get('tranche_effectif_salarie').get('date_reference')

    infoEtablissement.trancheEffectifsUniteLegale = result.get('unite_legale').get('tranche_effectif_salarie').get('intitule')
    infoEtablissement.anneeEffectifsUniteLegale = result.get('unite_legale').get('tranche_effectif_salarie').get('date_reference')



    infoEtablissement.activitePrincipaleRegMet = result.get(
        'activite_principale').get('code') if result.get('activite_principale') is not None else None

    # todo mapping
    infoEtablissement.etatAdministratifUniteLegale = result.get('unite_legale').get('etat_administratif')
    #todo mapping
    infoEtablissement.statutDiffusionUniteLegale = result.get('unite_legale').get('status_diffusion')

    if (result.get('unite_legale').get('date_creation')):
        if result.get('unite_legale').get('date_creation') < 0:
            infoEtablissement.dateCreationUniteLegale = (datetime.datetime(1970, 1, 1) + datetime.timedelta(seconds=result.get('unite_legale').get('date_creation'))).strftime("%Y-%m-%d")
        else:
            infoEtablissement.dateCreationUniteLegale = datetime.datetime.fromtimestamp(result.get('unite_legale').get('date_creation')).strftime("%Y-%m-%d")


    infoEtablissement.categorieJuridiqueUniteLegale = result.get('unite_legale').get('code')
    infoEtablissement.denominationUniteLegale = result.get('unite_legale').get('personne_morale_attributs').get('raison_sociale') if result.get('unite_legale').get('personne_morale_attributs') is not None else None

    infoEtablissement.sigleUniteLegale = result.get('unite_legale').get('sigle')
    infoEtablissement.activitePrincipaleUniteLegale = result.get('unite_legale').get('activite_principale').get('code') if result.get('unite_legale').get('activite_principale') is not None else None

    infoEtablissement.nomenclatureActivitePrincipaleUniteLegale = result.get('unite_legale').get('activite_principale').get('nomenclature') if result.get('unite_legale').get('activite_principale') is not None else None

    infoEtablissement.caractereEmployeurUniteLegale = result.get('caractereemployeurunitelegale')

    #todo
    infoEtablissement.anneeEffectifsUniteLegale = result.get('unite_legale').get('tranche_effectif_salarie').get('intitule')
    infoEtablissement.anneeEffectifsUniteLegale = result.get('unite_legale').get('tranche_effectif_salarie').get('date_reference')

    infoEtablissement.nicSiegeUniteLegale = result.get('unite_legale').get('siret_siege_social')[9:15] if result.get('unite_legale').get('siret_siege_social') is not None else None
    infoEtablissement.categorieEntreprise = result.get('unite_legale').get('categorie_entreprise')

     #pas de donnée pour l'année de la catégorie entreprise
    #infoEtablissement.anneeCategorieEntreprise = result.get('anneecategorieentreprise')
    infoEtablissement.anneeCategorieEntreprise= "2022"
    infoEtablissement.complementAdresseEtablissement = result.get('adresse').get('complement_adresse')
    infoEtablissement.numeroVoieEtablissement = result.get('adresse').get('numero_voie')
    infoEtablissement.indiceRepetitionEtablissement = result.get('adresse').get('indice_repetition_voie')

    infoEtablissement.typeVoieEtablissement = result.get('adresse').get('type_voie')
    infoEtablissement.libelleVoieEtablissement = result.get('adresse').get('libelle_voie')
    infoEtablissement.codePostalEtablissement = result.get('adresse').get('code_postal')
    infoEtablissement.libelleCommuneEtablissement = result.get('adresse').get('libelle_commune')
    infoEtablissement.codeCommuneEtablissement = result.get('adresse').get('code_commune')
    infoEtablissement.codeCedexEtablissement = result.get('adresse').get('code_cedex')
    infoEtablissement.libelleCedexEtablissement = result.get('adresse').get('libelle_cedex')
    infoEtablissement.codePaysEtrangerEtablissement = result.get('adresse').get('code_pays_etranger')
    infoEtablissement.libellePaysEtrangerEtablissement = result.get('adresse').get('libelle_pays_etranger')

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

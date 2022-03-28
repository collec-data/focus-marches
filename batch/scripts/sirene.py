from model.object import InfoEtablissement, InfoEtablissementPrincipal, Sirene,db_session, engine
from settings.settings import URL_API_SIREN_PERSO,enable_http_proxy, proxyDict,URL_FICHIER_INFOS_GREFFE, WORKDIR
import datetime, logging,requests, sqlalchemy, urllib
from sqlalchemy import text
import pandas as pd


request_acheteur = text("""select id_acheteur FROM acheteur WHERE id_acheteur NOT IN  (SELECT id_sirene FROM sirene)""")
request_titulaire = text("""select id_titulaire FROM titulaire WHERE id_titulaire NOT IN  (SELECT id_sirene FROM sirene)""")
request_infogreffe =text("""select siren FROM sirene where fiche_identite is null""")


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


def insert_info_api_siren(con,request):
    print("DEBUT insert_info_api_siren")
    result = con.execute(request);
    for id_siret in result.cursor:
        today = datetime.date.today()
        todayStr=today.isoformat()
        if enable_http_proxy:
            r = requests.get(URL_API_SIREN_PERSO + '/etablissements/' + str(id_siret[0]), proxies=proxyDict)
        else:
            r = requests.get(URL_API_SIREN_PERSO + '/etablissements/' + str(id_siret[0]))

        reponse = r.json()
        try:
            if(r.status_code == 200):
                infoEtablissement = InfoEtablissement(reponse['etablissement'])
                record = (
                    id_siret[0],
                    str(r.status_code),
                    todayStr,
                    infoEtablissement.siren,
                    infoEtablissement.nic,
                    infoEtablissement.siret,
                    infoEtablissement.dateCreationEtablissement,
                    infoEtablissement.trancheEffectifsEtablissement,
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
                    infoEtablissement.trancheEffectifsUniteLegale,
                    infoEtablissement.anneeEffectifsUniteLegale,
                    infoEtablissement.nicSiegeUniteLegale,
                    infoEtablissement.categorieEntreprise,
                    infoEtablissement.anneeCategorieEntreprise,
                    infoEtablissement.complementAdresseEtablissement,
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
                    infoEtablissement.latitude,
                    infoEtablissement.longitude)
                con.execute(sql_insert_sirene, record)
            else:

                if len(id_siret[0]) >= 9:
                    siren= id_siret[0][0:9]
                    if (siren.isnumeric()):
                        if enable_http_proxy:
                            r = requests.get(URL_API_SIREN_PERSO + '/unites_legales/' + siren, proxies=proxyDict)
                        else:
                            r = requests.get(URL_API_SIREN_PERSO + '/unites_legales/' + siren)
                        if (r.status_code == 200):
                            reponse = r.json()
                            infoEtablissement = InfoEtablissementPrincipal(reponse['unite_legale'])
                            record = (
                                infoEtablissement.siret,
                                str(r.status_code),
                                todayStr,
                                infoEtablissement.siren,
                                infoEtablissement.nic,
                                infoEtablissement.siret,
                                infoEtablissement.dateCreationEtablissement,
                                infoEtablissement.trancheEffectifsEtablissement,
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
                                infoEtablissement.trancheEffectifsUniteLegale,
                                infoEtablissement.anneeEffectifsUniteLegale,
                                infoEtablissement.nicSiegeUniteLegale,
                                infoEtablissement.categorieEntreprise,
                                infoEtablissement.anneeCategorieEntreprise,
                                infoEtablissement.complementAdresseEtablissement,
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
                                infoEtablissement.latitude,
                                infoEtablissement.longitude)
                            con.execute(sql_insert_sirene, record)
                    else:
                        logging.error('SIREN au mauvais format ' + id_siret[0])
                else:
                    logging.error('Siret au mauvais format ' + id_siret[0])

            # insert in BDD sirene
        except sqlalchemy.exc.IntegrityError as e:
            logging.info(id_siret[0] + ' deja présent')
        except Exception as e:
            logging.exception(e)

    print("FIN insert_info_api_siren")

def complete_with_infogreffe(con, request, df):
    print("DEBUT complete_with_infogreffe")
    result = con.execute(request)
    nb_maj=0
    nb_not_found=0
    cpt_update=0
    for siren, nic in result.cursor:
        cpt_update =cpt_update+1
        if cpt_update >499:
            db_session.commit()
            cpt_update = 0
        try:
            info = df.query("(Siren=='"+siren+"') and (Nic=='"+nic+"')")
            sirene_record = Sirene.query.filter(Sirene.siret == siren + nic).one()
            if not info.empty:
                if str(info['Millesime 1'].values[0]) != 'nan':
                    sirene_record.millesime_1 = info['Millesime 1'].values[0]
                if str(info['Millesime 2'].values[0]) != 'nan':
                    sirene_record.millesime_2 = info['Millesime 2'].values[0]
                if str(info['Millesime 3'].values[0]) != 'nan':
                    sirene_record.millesime_3 = info['Millesime 3'].values[0]

                if str(info['CA 1'].values[0]) != 'nan':
                    sirene_record.ca_1 = info['CA 1'].values[0]
                if str(info['CA 2'].values[0]) != 'nan':
                    sirene_record.ca_2 = info['CA 2'].values[0]
                if str(info['CA 3'].values[0]) != 'nan':
                    sirene_record.ca_3 = info['CA 3'].values[0]

                if str(info['Résultat 1'].values[0]) != 'nan':
                    sirene_record.resultat_1 = info['Résultat 1'].values[0]
                if str(info['Résultat 2'].values[0]) != 'nan':
                    sirene_record.resultat_2 = info['Résultat 2'].values[0]
                if str(info['Résultat 3'].values[0]) != 'nan':
                    sirene_record.resultat_3 = info['Résultat 3'].values[0]

                if str(info['Effectif 1'].values[0]) != 'nan':
                    sirene_record.effectif_1 = info['Effectif 1'].values[0]
                if str(info['Effectif 2'].values[0]) != 'nan':
                    sirene_record.effectif_2 = info['Effectif 2'].values[0]
                if str(info['Effectif 3'].values[0]) != 'nan':
                    sirene_record.effectif_3 = info['Effectif 3'].values[0]

                if str(info['fiche_identite'].values[0]) != 'nan':
                    sirene_record.fiche_identite = info['fiche_identite'].values[0]
                db_session.add(sirene_record)
                nb_maj=nb_maj+1
            else:
                sirene_record.fiche_identite = "https://www.infogreffe.fr/recherche-siret-entreprise/chercher-siret-entreprise.html"
                db_session.add(sirene_record)
                nb_not_found=nb_not_found+1

            print('maj:+'+str(nb_maj) +' / notFound:'+str(nb_not_found))


        except Exception as e:
            logging.exception(e)
            print(e)

    db_session.commit()

    print("FIN  complete_with_infogreffe")

def load_infogreffe():
    print('Debut du telechargement du fichier ...' + URL_FICHIER_INFOS_GREFFE)
    urllib.request.urlretrieve(URL_FICHIER_INFOS_GREFFE, WORKDIR + '/chiffres-cles-2020.csv')
    print('fin du telechargement du fichier ...' + URL_FICHIER_INFOS_GREFFE)
    return pd.read_csv(WORKDIR + '/chiffres-cles-2020.csv', sep=';', index_col='Siren',
                       usecols=['Siren', 'Nic', 'Millesime 1', 'Millesime 2', 'Millesime 3', 'CA 1', 'CA 2', 'CA 3',
                                'Résultat 1', 'Résultat 2', 'Résultat 3', 'Effectif 1', 'Effectif 2', 'Effectif 3',
                                'fiche_identite'],
                       dtype={'Siren': 'str', 'Nic': 'str', 'Effectif 1': 'str', 'Effectif 2': 'str',
                              'Effectif 3': 'str','Résultat 1': 'str', 'Résultat 1': 'str', 'Résultat 1': 'str',
                              'CA 1': 'str','CA 2': 'str', 'CA 3': 'float64', 'Millesime 1': 'str', 'Millesime 2': 'str',
                              'Millesime 3': 'str'})

def maj_info_greffe():
    # chargement du fichier info greffe
    df = load_infogreffe()
    with engine.connect() as con:
        complete_with_infogreffe(con, request_infogreffe, df)

def maj_table_sirene():
    with engine.connect() as con:
        # result = con.execute("truncate table sirene");
        insert_info_api_siren(con, request_titulaire)
        insert_info_api_siren(con,request_acheteur)


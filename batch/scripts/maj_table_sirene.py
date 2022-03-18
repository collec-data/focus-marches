from dev_local_settings import enable_http_proxy, proxyDict
from scripts.model.object import InfoEtablissement, InfoEtablissementPrincipal, engine
from settings import URL_API_SIREN_PERSO
import datetime,logging,requests,sqlalchemy
from sqlalchemy import text


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






with engine.connect() as con:
    # result = con.execute("truncate table sirene");
    insert_info_api_siren(con, request_titulaire)
    insert_info_api_siren(con,request_acheteur)


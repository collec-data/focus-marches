import logging
import requests
from time import sleep
from sqlalchemy import text

from dev_local_settings import enable_http_proxy, proxyDict
from scripts.model.object import Sirene, InfoGreffe, db_session, engine
from settings import URL_INFO_GREFFE, TEMPO_CALL_INFO_GREFFE

request_infogreffe =text("""select siren FROM sirene where fiche_identite is null""")

def complete_with_infogreffe(con,request):
    print("DEBUT  complete_with_infogreffe")
    result = con.execute(request);
    for siren in result.cursor:
        if enable_http_proxy:
            r = requests.get(URL_INFO_GREFFE + str(siren[0]), proxies=proxyDict)
        else:
            r = requests.get(URL_INFO_GREFFE + str(siren[0]))

        try:
            if (r.status_code == 200):
                sleep(TEMPO_CALL_INFO_GREFFE)
                liste_sirene = Sirene.query.filter(Sirene.siren == siren[0]).all()
                reponse = r.json()
                if len(reponse['records']) > 0  and 'fields' in reponse['records'][0]:
                    infogreffe = InfoGreffe(reponse['records'][0]['fields'])
                    for sirene in liste_sirene:
                        sirene.millesime_1=infogreffe.millesime_1
                        sirene.millesime_2=infogreffe.millesime_2
                        sirene.millesime_3=infogreffe.millesime_3
                        sirene.ca_1=str(infogreffe.ca_1)
                        sirene.ca_2=str(infogreffe.ca_2)
                        sirene.ca_3=str(infogreffe.ca_3)
                        sirene.resultat_1=str(infogreffe.resultat_1)
                        sirene.resultat_2=str(infogreffe.resultat_2)
                        sirene.resultat_3=str(infogreffe.resultat_3)
                        sirene.effectif_1=infogreffe.effectif_1 if infogreffe.effectif_1 != None else ''
                        sirene.effectif_2=infogreffe.effectif_2 if infogreffe.effectif_2 != None else ''
                        sirene.effectif_3=infogreffe.effectif_3 if infogreffe.effectif_3 != None else ''
                        sirene.fiche_identite=infogreffe.fiche_identite
                        db_session.add(sirene)
                        db_session.commit()
                    print("update siren " + siren[0]+ " done")
                else:
                    print("pas de données infogreffe pour " + siren[0])
                    for sirene in liste_sirene:
                        sirene.fiche_identite="pas de données infogreffe pour " + siren[0]
                        db_session.add(sirene)
                        db_session.commit()
            else:
                print("status code infogreffe " + str(r.status_code))
                if r.status_code == 429:
                    return
        except Exception as e:
            logging.exception(e)
    print("FIN  complete_with_infogreffe")


with engine.connect() as con:
    complete_with_infogreffe(con,request_infogreffe)


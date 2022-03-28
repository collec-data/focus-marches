import logging, urllib
from sqlalchemy import text
from model.object import Sirene,db_session, engine
from settings.settings import URL_FICHIER_INFOS_GREFFE, WORKDIR
import pandas as pd

request_infogreffe = text("""select siren,nic FROM sirene where fiche_identite is null""")


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

from settings import settings
from sqlalchemy import Column, String, DateTime, BigInteger, Integer, create_engine, Text
from sqlalchemy.dialects import mysql
from sqlalchemy.orm import declarative_base, scoped_session, sessionmaker


engine = create_engine(settings.SQLALCHEMY_DATABASE_URI)
# Creates a new session to the database by using the engine we described.
db_session = scoped_session(sessionmaker(autocommit=False,
                                         autoflush=True,
                                         bind=engine))
Model = declarative_base(name='Model')
Model.query = db_session.query_property()

class Sirene(Model):
    __tablename__ = 'sirene'
    id_sirene = Column('id_sirene', BigInteger, nullable=False, primary_key=True)
    statut = Column('statut', Integer, nullable=True)
    siren = Column('siren', String(9), nullable=True)
    siren = Column('siren', String(9), nullable=True)
    nic = Column('nic', String(9), nullable=True)
    siret = Column('siret', String(14), nullable=True)
    dateCreationEtablissement = Column('dateCreationEtablissement', DateTime, nullable=True)
    trancheEffectifsEtablissement = Column(String(10), nullable=True)
    anneeEffectifsEtablissement = Column(String(10), nullable=True)
    activitePrincipaleRegistreMetiersEtablissement = Column(String(10), nullable=True)
    etatAdministratifUniteLegale = Column(String(10), nullable=True)
    complementAdresseEtablissement = Column(String(255), nullable=True)
    numeroVoieEtablissement = Column(String(10), nullable=True)
    indiceRepetitionEtablissement = Column(String(10), nullable=True)
    typeVoieEtablissement = Column(String(50), nullable=True)
    libelleVoieEtablissement = Column(String(255), nullable=True)
    codePostalEtablissement = Column(String(20), nullable=True)
    libelleCommuneEtablissement = Column(String(255), nullable=True)
    codeCommuneEtablissement = Column(String(10), nullable=True)
    codeCedexEtablissement = Column(String(20), nullable=True)
    libelleCedexEtablissement = Column(String(255), nullable=True)
    codePaysEtrangerEtablissement = Column(String(10), nullable=True)
    libellePaysEtrangerEtablissement = Column(String(255), nullable=True)
    statutDiffusionUniteLegale = Column(String(10), nullable=True)
    dateCreationUniteLegale = Column(DateTime, nullable=True)
    categorieJuridiqueUniteLegale = Column(String(10), nullable=True)
    denominationUniteLegale = Column(String(255), nullable=True)
    sigleUniteLegale = Column(String(50), nullable=True)
    activitePrincipaleUniteLegale = Column(String(10), nullable=True)
    nomenclatureActivitePrincipaleUniteLegale = Column(String(10), nullable=True)
    caractereEmployeurUniteLegale = Column(String(10), nullable=True)
    trancheEffectifsUniteLegale = Column(String(10), nullable=True)
    anneeEffectifsUniteLegale = Column(String(10), nullable=True)
    nicSiegeUniteLegale = Column(String(10), nullable=True)
    categorieEntreprise = Column(String(10), nullable=True)
    anneeCategorieEntreprise = Column(String(4), nullable=True)
    millesime_1 = Column(String(5), nullable=True)
    millesime_2 = Column(String(5), nullable=True)
    millesime_3 = Column(String(5), nullable=True)
    ca_1 = Column(String(30), nullable=True)
    ca_2 = Column(String(30), nullable=True)
    ca_3 = Column(String(30), nullable=True)
    resultat_1 = Column(String(30), nullable=True)
    resultat_2 = Column(String(30), nullable=True)
    resultat_3 = Column(String(30), nullable=True)
    effectif_1 = Column(String(10), nullable=True)
    effectif_2 = Column(String(10), nullable=True)
    effectif_3 = Column(String(10), nullable=True)
    fiche_identite = Column(String(255), nullable=True)
class Marche(Model):
    __tablename__ = 'marche'
    id = Column(Integer, primary_key=True, autoincrement=True,nullable=False)
    id_marche = Column(String(40), nullable=False)
    id_acheteur = Column(String(14), nullable=False)
    id_nature = Column(mysql.INTEGER(11), nullable=False)
    objet = Column(Text, nullable=False)
    code_cpv = Column(mysql.INTEGER(11), nullable=False)
    categorie = Column(String(255), nullable=False)
    id_procedure = Column(mysql.INTEGER(10), nullable=False)
    id_lieu_execution = Column(mysql.INTEGER(10), nullable=False)
    duree_mois = Column(mysql.INTEGER(3), nullable=False)
    date_notification = Column(DateTime, nullable=False)
    date_publication_donnees = Column(DateTime, nullable=False)
    date_transmission_etalab = Column(DateTime, nullable=False)
    montant = Column(mysql.INTEGER(10), nullable=False)
    id_forme_prix = Column(mysql.INTEGER(10), nullable=False)
    modifications = Column(String(45), nullable=False)

    @property
    def serialize(self):
        """Return object data in easily serializable format"""
        return {
            'id_marche': self.id_marche,
            'id_acheteur': self.id_acheteur,
            'id_nature': self.id_nature,
            'objet': self.objet,
            'code_cpv': self.code_cpv,
            'categorie': self.categorie,
            'id_procedure': self.id_procedure,
            'id_lieu_execution': self.id_lieu_execution,
            'duree_mois': self.duree_mois,
            'date_notification': self.date_notification,
            'date_publication_donnees': self.date_publication_donnees,
            'date_transmission_etalab': self.date_transmission_etalab,
            'montant': self.montant,
            'id_forme_prix': self.id_forme_prix,
            'modifications': self.modifications
        }
class Acheteur(Model):
    __tablename__ = 'acheteur'
    id_acheteur = Column(String(14), nullable=False, primary_key=True)
    nom_acheteur = Column(String(250), nullable=False)
    nom_ui = Column(String(250), nullable=False)
    @property
    def serialize(self):
        """Return object data in easily serializable format"""
        return {
            'id_acheteur': self.id_acheteur,
            'nom_acheteur': self.nom_acheteur,
            'nom_ui': self.nom_ui,
        }
class Titulaire(Model):
    __tablename__ = 'titulaire'
    id_titulaire = Column(String(14), nullable=False, primary_key=True)
    type_identifiant = Column(String(45), nullable=False)
    denomination_sociale = Column(String(250), nullable=False)

    @property
    def serialize(self):
        """Return object data in easily serializable format"""
        return {
            'id_titulaire': self.id_titulaire,
            'type_identifiant': self.type_identifiant,
            'denomination_sociale': self.denomination_sociale,
        }
class Lieu(Model):
    __tablename__ = 'lieu'
    id_lieu = Column(mysql.INTEGER(10), nullable=False, primary_key=True)
    #TODO 3 -> 10
    code = Column(String(10), nullable=False)
    type_code = Column(String(45), nullable=False)
    nom_lieu = Column(String(250), nullable=False)
class Marche_titulaires(Model):
    __tablename__ = 'marche_titulaires'
    id_marche_titulaires = Column(mysql.INTEGER(11), nullable=False, primary_key=True)
    id_marche= Column(String(40), nullable=False)
    id_titulaires = Column(String(14), nullable=False)
    @property
    def serialize(self):
        """Return object data in easily serializable format"""
        return {
            'id_marche_titulaires': self.id_marche_titulaires,
            'id_marche': self.id_marche,
            'id_titulaires': self.id_titulaires,
        }


class InfoEtablissement:
    def __init__(self, resultApi=None):
        if (resultApi is None):
            return
        self.latitude = resultApi['latitude']
        self.longitude = resultApi['longitude']
        self.nic = resultApi['nic']
        self.siret = resultApi['siret']
        self.siren = resultApi['unite_legale']['siren']
        self.dateCreationEtablissement = resultApi['date_creation']
        self.trancheEffectifsEtablissement = resultApi['tranche_effectifs']
        self.anneeEffectifsEtablissement = resultApi['annee_effectifs']
        self.activitePrincipaleRegistreMetiersEtablissement = resultApi['activite_principale_registre_metiers']
        self.complementAdresseEtablissement = resultApi['complement_adresse']
        self.numeroVoieEtablissement = resultApi['numero_voie']
        self.indiceRepetitionEtablissement = resultApi['indice_repetition']
        self.typeVoieEtablissement = resultApi['type_voie']
        self.libelleVoieEtablissement = resultApi['libelle_voie']
        self.codePostalEtablissement = resultApi['code_postal']
        self.libelleCommuneEtablissement = resultApi['libelle_commune']
        self.codeCommuneEtablissement = resultApi['code_commune']
        self.codeCedexEtablissement = resultApi['code_cedex']
        self.libelleCedexEtablissement = resultApi['libelle_cedex']
        self.codePaysEtrangerEtablissement = resultApi['code_pays_etranger']
        self.libellePaysEtrangerEtablissement = resultApi['libelle_pays_etranger']
        self.etatAdministratifUniteLegale = resultApi['unite_legale']['etat_administratif']
        self.statutDiffusionUniteLegale = resultApi['unite_legale']['etablissement_siege']['statut_diffusion']
        self.dateCreationUniteLegale = resultApi['unite_legale']['etablissement_siege']['date_creation']
        self.categorieJuridiqueUniteLegale = resultApi['unite_legale']['categorie_juridique']
        self.denominationUniteLegale = resultApi['unite_legale']['denomination']
        self.sigleUniteLegale = resultApi['unite_legale']['sigle']
        self.activitePrincipaleUniteLegale = resultApi['unite_legale']['etablissement_siege']['activite_principale']
        self.nomenclatureActivitePrincipaleUniteLegale = resultApi['unite_legale']['etablissement_siege'][
            'nomenclature_activite_principale']
        self.caractereEmployeurUniteLegale = resultApi['unite_legale']['etablissement_siege']['caractere_employeur']
        self.trancheEffectifsUniteLegale = resultApi['unite_legale']['etablissement_siege']['tranche_effectifs']
        self.anneeEffectifsUniteLegale = resultApi['unite_legale']['etablissement_siege']['annee_effectifs']
        self.nicSiegeUniteLegale = resultApi['unite_legale']['etablissement_siege']['nic']
        self.categorieEntreprise = resultApi['unite_legale']['activite_principale']
        self.anneeCategorieEntreprise = resultApi['unite_legale']['annee_categorie_entreprise']

class InfoEtablissementPrincipal:
    def __init__(self, resultApi):
        self.latitude = resultApi['etablissement_siege']['latitude']
        self.longitude = resultApi['etablissement_siege']['longitude']
        self.nic = resultApi['etablissement_siege']['nic']
        self.siret = resultApi['etablissement_siege']['siret']
        self.siren = resultApi['siren']
        self.dateCreationEtablissement = resultApi['etablissement_siege']['date_creation']
        self.trancheEffectifsEtablissement = resultApi['etablissement_siege']['tranche_effectifs']
        self.anneeEffectifsEtablissement = resultApi['etablissement_siege']['annee_effectifs']
        self.activitePrincipaleRegistreMetiersEtablissement = resultApi['etablissement_siege'][
            'activite_principale_registre_metiers']
        self.complementAdresseEtablissement = resultApi['etablissement_siege']['complement_adresse']
        self.numeroVoieEtablissement = resultApi['etablissement_siege']['numero_voie']
        self.indiceRepetitionEtablissement = resultApi['etablissement_siege']['indice_repetition']
        self.typeVoieEtablissement = resultApi['etablissement_siege']['type_voie']
        self.libelleVoieEtablissement = resultApi['etablissement_siege']['libelle_voie']
        self.codePostalEtablissement = resultApi['etablissement_siege']['code_postal']
        self.libelleCommuneEtablissement = resultApi['etablissement_siege']['libelle_commune']
        self.codeCommuneEtablissement = resultApi['etablissement_siege']['code_commune']
        self.codeCedexEtablissement = resultApi['etablissement_siege']['code_cedex']
        self.libelleCedexEtablissement = resultApi['etablissement_siege']['libelle_cedex']
        self.codePaysEtrangerEtablissement = resultApi['etablissement_siege']['code_pays_etranger']
        self.libellePaysEtrangerEtablissement = resultApi['etablissement_siege']['libelle_pays_etranger']
        self.etatAdministratifUniteLegale = resultApi['etat_administratif']
        self.statutDiffusionUniteLegale = resultApi['etablissement_siege']['statut_diffusion']
        self.dateCreationUniteLegale = resultApi['etablissement_siege']['date_creation']
        self.categorieJuridiqueUniteLegale = resultApi['categorie_juridique']
        self.denominationUniteLegale = resultApi['denomination']
        self.sigleUniteLegale = resultApi['sigle']
        self.activitePrincipaleUniteLegale = resultApi['etablissement_siege'][
            'activite_principale']
        self.nomenclatureActivitePrincipaleUniteLegale = resultApi['etablissement_siege'][
            'nomenclature_activite_principale']
        self.caractereEmployeurUniteLegale = resultApi['etablissement_siege'][
            'caractere_employeur']
        self.trancheEffectifsUniteLegale = resultApi['etablissement_siege']['tranche_effectifs']
        self.anneeEffectifsUniteLegale = resultApi['etablissement_siege']['annee_effectifs']
        self.nicSiegeUniteLegale = resultApi['etablissement_siege']['nic']
        self.categorieEntreprise = resultApi['activite_principale']
        self.anneeCategorieEntreprise = resultApi['annee_categorie_entreprise']
class InfoGreffe:
    def __init__(self, resultApi):
        self.millesime_1 = resultApi['millesime_1'] if 'millesime_1' in resultApi else None
        self.millesime_2 = resultApi['millesime_2'] if 'millesime_2' in resultApi else None
        self.millesime_3 = resultApi['millesime_3'] if 'millesime_3' in resultApi else None
        self.ca_1 = resultApi['ca_1'] if 'ca_1' in resultApi else None
        self.ca_2 = resultApi['ca_2'] if 'ca_2' in resultApi else None
        self.ca_3 = resultApi['ca_3'] if 'ca_3' in resultApi else None
        self.resultat_1 = resultApi['resultat_1'] if 'resultat_1' in resultApi else None
        self.resultat_2 = resultApi['resultat_2'] if 'resultat_2' in resultApi else None
        self.resultat_3 = resultApi['resultat_3'] if 'resultat_3' in resultApi else None
        self.effectif_1 = resultApi['effectif_1'] if 'effectif_1' in resultApi else None
        self.effectif_2 = resultApi['effectif_2'] if 'effectif_2' in resultApi else None
        self.effectif_3 = resultApi['effectif_3'] if 'effectif_3' in resultApi else None
        self.fiche_identite = resultApi['fiche_identite'] if 'fiche_identite' in resultApi else None

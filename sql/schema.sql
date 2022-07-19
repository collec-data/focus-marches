SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `marches_publics`
--

-- --------------------------------------------------------

--
-- Structure de la table `acheteur`
--

CREATE TABLE `acheteur` (
                            `id_acheteur` varchar(14) NOT NULL,
                            `nom_acheteur` varchar(250) DEFAULT NULL,
                            `nom_ui` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE `config` (     `nom` varchar(255) NOT NULL,
                            `date_mise_a_jour` date DEFAULT NULL,
                            `date_debut` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categories_juridiques`
--

CREATE TABLE `categories_juridiques` (
  `id_categories_juridiques` int(10) UNSIGNED NOT NULL,
  `code_categories_juridiques` varchar(10) NOT NULL,
  `libelle_categories_juridiques` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `cpv`
--

CREATE TABLE `cpv` (
  `id_cpv` int(10) UNSIGNED NOT NULL,
  `libelle_cpv` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `forme_prix`
--

CREATE TABLE `forme_prix` (
  `id_forme_prix` int(10) UNSIGNED NOT NULL,
  `nom_forme_prix` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Structure de la table `lieu`
--

CREATE TABLE `lieu` (
  `id_lieu` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `type_code` varchar(45) DEFAULT NULL,
  `nom_lieu` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `marche`
--

CREATE TABLE `marche` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_marche` varchar(40) NOT NULL,
  `id_acheteur` varchar(14) NOT NULL,
  `id_nature` int(11) DEFAULT NULL,
  `objet` text DEFAULT NULL,
  `code_cpv` int(11) DEFAULT NULL,
  `categorie` varchar(255) NOT NULL,
  `id_procedure` int(10) UNSIGNED DEFAULT NULL,
  `id_lieu_execution` int(10) UNSIGNED DEFAULT NULL,
  `duree_mois` tinyint(3) UNSIGNED DEFAULT NULL,
  `date_notification` date DEFAULT NULL,
  `date_publication_donnees` date DEFAULT NULL,
  `date_transmission_etalab` date DEFAULT NULL,
  `montant` int(10) UNSIGNED DEFAULT NULL,
  `id_forme_prix` int(10) UNSIGNED DEFAULT NULL,
  `modifications` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `marche_titulaires`
--

CREATE TABLE `marche_titulaires` (
  `id_marche_titulaires` int(11) UNSIGNED NOT NULL,
  `id_marche` varchar(40) NOT NULL,
  `id_titulaires` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mois`
--

CREATE TABLE `mois` (
  `id_mois` int(10) UNSIGNED NOT NULL,
  `date_mois` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `naf`
--

CREATE TABLE `naf` (
  `id_naf` int(10) UNSIGNED NOT NULL,
  `code_naf` varchar(50) NOT NULL,
  `libelle_naf` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `nafa`
--

CREATE TABLE `nafa` (
  `id_nafa` int(10) UNSIGNED NOT NULL,
  `code_nafa` varchar(10) NOT NULL,
  `libelle_nafa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `nature`
--

CREATE TABLE `nature` (
  `id_nature` int(10) UNSIGNED NOT NULL,
  `nom_nature` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `organismes`
--

CREATE TABLE `organismes` (
  `id_organisme` int(10) UNSIGNED NOT NULL,
  `typecom` varchar(10) DEFAULT NULL,
  `codepostal` varchar(10) DEFAULT NULL,
  `codeinsee` varchar(10) DEFAULT NULL,
  `siren` varchar(10) DEFAULT NULL,
  `siret` varchar(15) DEFAULT NULL,
  `reg` varchar(5) DEFAULT NULL,
  `dep` varchar(5) DEFAULT NULL,
  `ncc` varchar(255) DEFAULT NULL,
  `nom_commune` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `can` varchar(10) DEFAULT NULL,
  `pop_2015` varchar(10) DEFAULT NULL,
  `hommes_2015` varchar(10) DEFAULT NULL,
  `femmes_2015` varchar(10) DEFAULT NULL,
  `pop_15_plus` varchar(10) DEFAULT NULL,
  `agriculteurs` varchar(10) DEFAULT NULL,
  `artisans_chefs` varchar(10) DEFAULT NULL,
  `cadres` varchar(10) DEFAULT NULL,
  `intermediaires` varchar(10) DEFAULT NULL,
  `employes` varchar(10) DEFAULT NULL,
  `ouvriers` varchar(10) DEFAULT NULL,
  `retraites` varchar(10) DEFAULT NULL,
  `autres` varchar(10) DEFAULT NULL,
  `menages` varchar(10) DEFAULT NULL,
  `mediane_niveau_vie` varchar(20) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `procedure_marche`
--

CREATE TABLE `procedure_marche` (
  `id_procedure` int(10) UNSIGNED NOT NULL,
  `nom_procedure` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


-- --------------------------------------------------------

--
-- Structure de la table `sirene`
--

CREATE TABLE `sirene` (
  `id_sirene` bigint(16) UNSIGNED NOT NULL,
  `statut` int(3) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `siren` varchar(9) DEFAULT NULL,
  `nic` varchar(5) DEFAULT NULL,
  `siret` varchar(14) DEFAULT NULL,
  `dateCreationEtablissement` date DEFAULT NULL,
  `trancheEffectifsEtablissement` varchar(10) DEFAULT NULL,
  `anneeEffectifsEtablissement` varchar(10) DEFAULT NULL,
  `activitePrincipaleRegistreMetiersEtablissement` varchar(10) DEFAULT NULL,
  `etatAdministratifUniteLegale` varchar(10) DEFAULT NULL,
  `statutDiffusionUniteLegale` varchar(10) DEFAULT NULL,
  `dateCreationUniteLegale` date DEFAULT NULL,
  `categorieJuridiqueUniteLegale` varchar(10) DEFAULT NULL,
  `denominationUniteLegale` varchar(255) DEFAULT NULL,
  `sigleUniteLegale` varchar(50) DEFAULT NULL,
  `activitePrincipaleUniteLegale` varchar(10) DEFAULT NULL,
  `nomenclatureActivitePrincipaleUniteLegale` varchar(10) DEFAULT NULL,
  `caractereEmployeurUniteLegale` varchar(10) DEFAULT NULL,
  `trancheEffectifsUniteLegale` varchar(10) DEFAULT NULL,
  `anneeEffectifsUniteLegale` varchar(10) DEFAULT NULL,
  `nicSiegeUniteLegale` varchar(10) DEFAULT NULL,
  `categorieEntreprise` varchar(10) DEFAULT NULL,
  `anneeCategorieEntreprise` varchar(4) DEFAULT NULL,
  `complementAdresseEtablissement` varchar(255) DEFAULT NULL,
  `numeroVoieEtablissement` varchar(10) DEFAULT NULL,
  `indiceRepetitionEtablissement` varchar(10) DEFAULT NULL,
  `typeVoieEtablissement` varchar(50) DEFAULT NULL,
  `libelleVoieEtablissement` varchar(255) DEFAULT NULL,
  `codePostalEtablissement` varchar(20) DEFAULT NULL,
  `libelleCommuneEtablissement` varchar(255) DEFAULT NULL,
  `codeCommuneEtablissement` varchar(10) DEFAULT NULL,
  `codeCedexEtablissement` varchar(20) DEFAULT NULL,
  `libelleCedexEtablissement` varchar(255) DEFAULT NULL,
  `codePaysEtrangerEtablissement` varchar(10) DEFAULT NULL,
  `libellePaysEtrangerEtablissement` varchar(255) DEFAULT NULL,
  `latitude` varchar(20) DEFAULT NULL,
  `longitude` varchar(20) DEFAULT NULL,
  `millesime_1` varchar(5) DEFAULT NULL,
  `millesime_2` varchar(5) DEFAULT NULL,
  `millesime_3` varchar(5) DEFAULT NULL,
  `ca_1` varchar(30) DEFAULT NULL,
  `ca_2` varchar(30) DEFAULT NULL,
  `ca_3` varchar(30) DEFAULT NULL,
  `resultat_1` varchar(30) DEFAULT NULL,
  `resultat_2` varchar(30) DEFAULT NULL,
  `resultat_3` varchar(30) DEFAULT NULL,
  `effectif_1` varchar(10) DEFAULT NULL,
  `effectif_2` varchar(10) DEFAULT NULL,
  `effectif_3` varchar(10) DEFAULT NULL,
  `fiche_identite` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `titulaire`
--

CREATE TABLE `titulaire` (
  `id_titulaire` varchar(14) NOT NULL,
  `type_identifiant` varchar(45) DEFAULT NULL,
  `denomination_sociale` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tranches`
--

CREATE TABLE `tranches` (
  `id_tranche` int(10) UNSIGNED NOT NULL,
  `code_tranche` varchar(5) NOT NULL,
  `libelle_tranche` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php
header('Content-Type: application/json; charset=u/tf-8');
error_reporting(0);

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // pour real_escape_string

$sql = "SELECT t.id_titulaire, denomination_sociale, libelle_naf as naf,
        codePostalEtablissement as cp, trancheEffectifsEtablissement, t.type_identifiant,
        tr.libelle_tranche as libelle_tranche_etablissement, libelle_categories_juridiques, COUNT(m.id) nb, SUM(m.montant) total, s.ca_1, s.resultat_1
        FROM        titulaire t
        INNER JOIN  marche_titulaires mt ON mt.id_titulaires = t.id_titulaire
        INNER JOIN  sirene s ON t.id_titulaire = s.id_sirene
        LEFT JOIN   tranches tr ON tr.code_tranche = s.trancheEffectifsEtablissement
        LEFT JOIN   categories_juridiques ct ON ct.code_categories_juridiques = s.categorieJuridiqueUniteLegale
        LEFT JOIN   marche m ON mt.id_marche = m.id_marche
        LEFT JOIN   naf n ON n.code_naf = s.activitePrincipaleUniteLegale

        WHERE       m.date_notification > '2018-12-31'
        GROUP BY    t.id_titulaire
        ORDER BY    denomination_sociale  ASC";

try {
  $result = $connect->query($sql);

  if ($result) {
    $out = '{ "data" :[';

    while ($r = mysqli_fetch_assoc($result)) {
      if ($r['trancheEffectifsEtablissement'] === "00" || $r['trancheEffectifsEtablissement'] === "NN")
        $r['libelle_tranche_etablissement'] = "-";
      $r['denomination_sociale'] = str_replace('"', '', $r['denomination_sociale']);

      $tag_annuaire_entreprise = '"annuaire_lien":"<div style=\"display:none\"></div></div>",';
      if($r['type_identifiant'] === "SIRET"){
        $tag_annuaire_entreprise = '"annuaire_lien":"<a class=\"button voirAnnuaire small\" data-id=\"' . $r['id_titulaire'] . '\" href=\"https://annuaire-entreprises.data.gouv.fr/entreprise/' . hsc($r['id_titulaire']) . '\" target=\"_blank\"  title=\"Ouvrir l\'annuaire entreprise\" style=\"text-decoration:none\">&#128270</a> ",';
      }

      $out .= '{'
        . $tag_annuaire_entreprise
        . '"cp":"' . hsc($r['cp']) . '",'
        . '"denomination_sociale":"<a href=\"titulaire.php?i=' . hsc($r['id_titulaire']) . '\">' . hsc($r['denomination_sociale']) . '</a>",'
        . '"naf":"' . hsc($r['naf']) . '",'
        . '"nb":"' . hsc($r['nb']) . '",'
        . '"total":"' . hsc($r['total']) . '",'
        . '"libelle_tranche_etablissement":"' . hsc($r['libelle_tranche_etablissement']) . '",'
        . '"ca_1":"' . hsc($r['ca_1']) . '",'
        . '"resultat_1":"' . hsc($r['resultat_1']) . '" },';
    }
    $out = substr($out, 0, -1);
    $out .= "]}";
    mysqli_free_result($result);
  }
} catch (Exception $e) {
  $out = 0;
}

$connect->close();
echo ($out);

?>
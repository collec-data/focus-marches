<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=u/tf-8');
// header('Content-Type: text/html; charset=utf-8');

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

$sql = "SELECT t.id_titulaire, denomination_sociale, libelle_naf as naf,
        codePostalEtablissement as cp, trancheEffectifsEtablissement,
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
  // echo $sql;
  try
  {
    $result = $connect->query($sql);

    if ($result)
    {
      $out = '{ "data" :[';

      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        if ($r['trancheEffectifsEtablissement']==="00" || $r['trancheEffectifsEtablissement']==="NN") $r['libelle_tranche_etablissement'] = "-";
        $r['denomination_sociale'] = str_replace('"', '\"',$r['denomination_sociale']);
        $out .= '{"cp":"' . $r['cp'] . '",'
          . '"denomination_sociale":"<a href=\"titulaire.php?i=' . $r['id_titulaire'] . '\">' . $r['denomination_sociale'] . '</a>",'
          . '"naf":"' . $r['naf'] . '",'
          . '"nb":"' . $r['nb'] . '",'
          . '"total":"' . $r['total'] . '",'
          . '"libelle_tranche_etablissement":"' . $r['libelle_tranche_etablissement'] . '",'
          . '"ca_1":"' . $r['ca_1'] . '",'
          . '"resultat_1":"' . $r['resultat_1'] . '" },';
      }
      $out = substr($out, 0, -1);
      $out .="]}";
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $out = 0;
  }

  $connect->close();
  echo( $out);

?>

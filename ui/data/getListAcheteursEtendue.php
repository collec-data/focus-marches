<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=u/tf-8');
// header('Content-Type: text/html; charset=utf-8');

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

$sql = "SELECT a.id_acheteur, nom_acheteur as denomination_sociale,
        o.dep, o.pop_2015, codePostalEtablissement as cp, trancheEffectifsEtablissement,
        tr.libelle_tranche as libelle_tranche_etablissement, libelle_categories_juridiques, COUNT(m.id) nb, SUM(m.montant) total
        FROM        acheteur a
        INNER JOIN  sirene s ON a.id_acheteur = s.id_sirene
        LEFT JOIN   tranches tr ON tr.code_tranche = s.trancheEffectifsEtablissement
        LEFT JOIN   categories_juridiques ct ON ct.code_categories_juridiques = s.categorieJuridiqueUniteLegale
        LEFT JOIN   organismes o ON o.codeInsee = s.codeCommuneEtablissement
        LEFT JOIN   marche m ON a.id_acheteur = m.id_acheteur
        WHERE       m.date_notification > '2018-12-31'
        GROUP BY    a.id_acheteur
        ORDER BY    denomination_sociale, o.dep";
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

        $out .= '{"dep":"' . $r['dep'] . '",'
          . '"denomination_sociale":"<a href=\"acheteur.php?i=' . $r['id_acheteur'] . '\">' . trim($r['denomination_sociale']) . '</a>",'
          . '"libelle_categories_juridiques":"' . $r['libelle_categories_juridiques'] . '",'
          . '"nb":"' . $r['nb'] . '",'
          . '"total":"' . $r['total'] . '",'
          . '"libelle_tranche_etablissement":"' . $r['libelle_tranche_etablissement'] . '"},';
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

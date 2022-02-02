<?php
header('Content-Type: application/json; charset=utf-8');

/* -------------------------------
getCPVTreeMap
----------------------------------
Fonction pour générer un CSV qui sera affiché comme un treemap
*/
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

$sql =
  "SELECT categorie,
	(LEFT(m.code_cpv, 3) * 100000) subregion_code, sub.libelle_cpv subregion,
    m.code_cpv codecpv, c.libelle_cpv libelle, COUNT(c.libelle_cpv) total_libelle
  FROM marche m
  INNER JOIN cpv c ON c.id_cpv = m.code_cpv
  /* même code cpv mais avec les 3 premiers chiffres puis des zéros */
  INNER JOIN cpv sub ON sub.id_cpv = (LEFT(m.code_cpv, 3) * 100000)
  GROUP BY c.libelle_cpv
  ORDER BY total_libelle DESC";

  try
  {
    $out = "[";
    $result = $connect->query($sql);

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $out .= '{"region":"' . $r['categorie']
          . '","subregion":"' . clean($r['subregion'])
          . '","key":"' . clean($r['libelle'])
          . '","value":' . $r['total_libelle']
          . ',"cpv":' . $r['codecpv']
          . '},';
      }
      $out = substr($out, 0, -1);
    }
    $out .= "]";
  }
  catch (Exception $e)
  {
    return $e;
  }
  echo $out; 
?>

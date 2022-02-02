<?php

//// Afficher ou pas les infos de debug :
$debug = true;

function d($var)
{
  global $debug;
  if ($debug)
  {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
  }
}



/* -------------------------------
clean_get_dept()
----------------------------------
contrôle les valeurs présents dans GET[dept]
- 2 chiffres
- numérique
- valeurs limitées
*/
function clean_get_dept()
{
  try
  {
    $dept_valides = [21, 25, 39, 58, 70, 71, 89, 90];
    if (isset($_GET))
    {
      if (isset($_GET['dept']))
      {
        if (is_numeric($_GET['dept']))
        {
          if (in_array($_GET['dept'], $dept_valides))
          {
            return $_GET['dept'];
          }
        }
      }
    }
    return 0;
  }
  catch (Exception $e)
  {
    return 0;
  }
}


/* -------------------------------
clean($string)
----------------------------------
supprime les espaces et caractères qui risquent de casser le JSON
*/
function clean($str)
{
  try
  {
    return str_replace(
      ['"', "\r", "\t", "\n"],
      [" ", "", " ", "<br>"],
      $str);
  }
  catch (Exception $e)
  {
    return $e;
  }
}

/* -------------------------------
nf($n)
----------------------------------
Formatte le chiffre french style

*/
function nf ($n)
{
  return number_format ($n, 0, ",", " ");
}

/* -------------------------------
pf($total, $partie)
----------------------------------
Calcule et formatte le pourcentage avec 2d
retourne le chiffre entre parenthèses avec %
*/
function pf ($total, $partie)
{
  return " <i>(" . number_format ( (( $partie * 100 ) / $total), 2, ",", " ") . "&nbsp;%)</i>";
}

/* -------------------------------
ft($value)
----------------------------------
Formatte une valeur qui peut être '-' ou un numéro
*/
function ft ($value, $unite)
{
  if (is_numeric($value))
  {
    return nf($value) . " $unite";
  }
  return $value;
}

/* -------------------------------
coupe($str, $n)
----------------------------------
Formatte reduit une str

*/
function coupe ($str, $n)
{
  if (strlen($str) > $n)
  {
    $str = substr($str, 0, $n) . ' ...';
  }
  return $str;
}

/* -------------------------------
getNom($id)
----------------------------------

*/
function getNom ($connect, $id=0)
{
  if ($id === 0) return "0";
  if (!is_numeric($id)) return "no numeric";

  $sql = "SELECT nom_acheteur nom
  FROM acheteur a
  INNER JOIN marche m ON m.id_acheteur = a.id_acheteur
  WHERE a.id_acheteur = '" .  $id . "'";

  try
  {
    $result = $connect->query($sql);

    if ($result)
    {
      while ( $obj = mysqli_fetch_object( $result ) )
      {
        $nom = $obj->nom;
        // $nom = print_r($obj);
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $nom = $e;
  }
  return $nom;
}

/* -------------------------------
getNomTitulaire($id)
----------------------------------

*/
function getNomTitulaire ($connect, $id=0)
{
  if ($id === 0) return "0";
  if (!is_numeric($id)) return "no numeric";

  $sql = "SELECT denomination_sociale
          FROM titulaire
          WHERE id_titulaire = '" .  $id . "'";

  try
  {
    $result = $connect->query($sql);

    if ($result)
    {
      while ( $obj = mysqli_fetch_object( $result ) )
      {
        $nom = $obj->denomination_sociale;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $nom = $e;
  }
  return $nom;
}


/* -------------------------------
getCommune($id)
----------------------------------

*/
function getCommune ($connect, $nom=null)
{
  if (is_null($nom)) return "0";

  $sql = "SELECT *
  FROM communes
  WHERE nccenr = '" .  $nom . "'";
  // return $sql;
  try
  {
    $result = $connect->query($sql);
    $out = [];

    if ($result)
    {
      while ( $obj = mysqli_fetch_object( $result ) )
      {
        $out['pop_2015'] = $obj->pop_2015;
        // $nom = print_r($obj);
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $out = $e;
  }
  return $out;
}


/* -------------------------------
getMontantTotal
----------------------------------
Montant total de tous les marchés

*/
function getMontantTotal ($connect)
{
  $sql = "SELECT sum(montant) as montantTotal FROM marche";

  try
  {
    $result = $connect->query($sql);

    if ($result)
    {
      while ( $obj = mysqli_fetch_object( $result ) )
      {
        $montantTotal = $obj->montantTotal;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $montantTotal = 0;
  }
  return $montantTotal;
}


/* -------------------------------
getDeptsMontants
----------------------------------
Montant total de tous les marchés groupés par dépt

*/
function getDepts ($connect, $months=12)
{
  $sql = "SELECT nom_lieu, sum(montant) as montant, count(m.id) as nombre
  FROM marche m
  INNER JOIN lieu l ON l.id_lieu = `id_lieu_execution`
  WHERE m.date_notification > '0000-00-00'
  AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
  GROUP BY nom_lieu
  ORDER BY nom_lieu DESC";

  $lieu = [];
  $montant = [];
  $nombre = [];

  try
  {
    $result = $connect->query($sql);

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $lieu[]     = '"' . $r['nom_lieu'] . '"';
        $montant[]  = $r['montant'];
        $nombre[]   = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    $out [0] = "Pas de données";
  }
  return array(
    "lieu" => implode( ",", $lieu),
    "montant" => implode( ",", $montant),
    "nombre" => implode( ",", $nombre)
  );

}


/* -------------------------------
getKPIAll - Toutes les périodes
----------------------------------
*/

function getKPIAll ($connect)
{
  $sql = "SELECT SUM(montant) as montant_total,
  COUNT(m.id) as nombre,
  MAX(montant) maximum,
  AVG(montant) moyenne,
  (DATEDIFF( MAX(date_notification), MIN(m2.mxm) ) / 30 ) periode
  FROM marche m,
  (SELECT min(date_notification) mxm
  FROM marche ma2
  WHERE ma2.date_notification > '0000-00-00') m2 ";

  try
  {
    $result = $connect->query($sql);

    $kpi = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $kpi['montant_total'] = $r['montant_total'];
        $kpi['nombre'] = $r['nombre'];
        $kpi['maximum'] = $r['maximum'];
        $kpi['moyenne'] = $r['moyenne'];
        $kpi['periode'] = $r['periode'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return $kpi;
}



/* -------------------------------
getKPI (pour les acheteurs )
----------------------------------
SELECT CURRENT_DATE(), DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR)
*/

function getKPI ($connect, $id=0, $months=0, $dept=0)
{
  $sql = "SELECT SUM(montant) as montant_total,
  COUNT(m.id) as nombre,
  MAX(montant) maximum,
  AVG(montant) moyenne,
  (DATEDIFF( MAX(date_notification), MIN(date_notification) ) / 30 ) periode,
  COUNT(DISTINCT(a.nom_acheteur)) nb_acheteurs,
  COUNT(DISTINCT(t.denomination_sociale)) nb_fournisseurs
  FROM marche m
  LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
  INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
  INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire
  INNER JOIN lieu l ON l.id_lieu = m.id_lieu_execution
  WHERE m.date_notification > '0000-00-00' ";

  if ($months > 0)
  {
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";
  }
  if ($dept > 0)
  {
    $sql .= " AND l.code = " . $dept;
  }


  if ($id > 0)
  {
    $sql .= " AND m.id_acheteur = '" . $id . "'";
  }

  // return $sql;

  try
  {
    $result = $connect->query($sql);

    $kpi = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $kpi['montant_total'] = $r['montant_total'];
        $kpi['nombre'] = $r['nombre'];
        $kpi['maximum'] = $r['maximum'];
        $kpi['moyenne'] = $r['moyenne'];
        $kpi['periode'] = $months > 0 ? $months : $r['periode'];
        $kpi['nb_acheteurs'] = $r['nb_acheteurs'];
        $kpi['nb_fournisseurs'] = $r['nb_fournisseurs'];
        $kpi['sql'] = $sql;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // d($kpi);
  return $kpi;
}


/* -------------------------------
getKPITitulaire
----------------------------------
SELECT CURRENT_DATE(), DATE_SUB(CURRENT_DATE(), INTERVAL 1 YEAR)
*/

function getKPITitulaire ($connect, $id=0, $months=0)
{
  /*COUNT(DISTINCT(a.nom_acheteur)) nb_acheteurs*/
  $sql = "SELECT SUM(montant) as montant_total,
  COUNT(m.id) as nombre,
  MAX(montant) maximum,
  AVG(montant) moyenne,
  (DATEDIFF( MAX(date_notification), MIN(date_notification) ) / 30 ) periode,
  COUNT(DISTINCT(a.nom_acheteur)) nb_acheteurs
  FROM marche m
  LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
  INNER JOIN marche_titulaires mt ON mt.id_marche = m.id_marche
  INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
  WHERE m.date_notification > '0000-00-00' ";

  if ($months > 0)
  {
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";
  }

  if ($id > 0)
  {
    $sql .= " AND t.id_titulaire = '" . $id . "'";
  }
// return $sql;
  try
  {
    $result = $connect->query($sql);

    $kpi = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $kpi['montant_total'] = $r['montant_total'];
        $kpi['nombre'] = $r['nombre'];
        $kpi['maximum'] = $r['maximum'];
        $kpi['moyenne'] = $r['moyenne'];
        $kpi['periode'] = $months > 0 ? $months : $r['periode'];
        $kpi['nb_acheteurs'] = $r['nb_acheteurs'];
        $kpi['sql'] = $sql;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return $kpi;
}


/* -------------------------------
getMontant par CPV et lieu
----------------------------------
*/
function getMontantCPVLieu ($connect, $categorie, $months=12)
{
  $sql = "SELECT SUM(montant) total, nom_lieu
  FROM marche m
  INNER JOIN lieu l ON m.id_lieu_execution = l.id_lieu
  WHERE m.date_notification > '0000-00-00'
  AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
  AND ";

  switch ($categorie)
  {
    case 'services':
    $s= 0;
    $sql .= "code_cpv > 49999999";
    break;

    case 'travaux':
    $s = 1;
    $sql .= "code_cpv < 50000000 AND code_cpv > 44999999";
    break;

    case 'fournitures':
    $s = 2;
    $sql .= "code_cpv < 45000000";
    break;

  }

  $sql .= " GROUP BY nom_lieu";

  try
  {
    $result = $connect->query($sql);

    $source = [];
    $target = [];
    $values = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $source[] = $s;
        switch ($r['nom_lieu'])
        {
          case "(21) Côte d'Or" : $t = 3; break;
          case "(21) Côte-d'Or" : $t = 3; break;
          case "Côte d'Or" : $t = 3; break;

          case "(25) Doubs" : $t = 4; break;
          case "Doubs" : $t = 4; break;

          case "(70) Haute-Saône" : $t = 5; break;
          case "Haute-Saône" : $t = 5; break;

          case "(39) Jura" : $t = 6; break;
          case "Jura" : $t = 6; break;

          case "(58) Nièvre" : $t = 7; break;
          case "Nièvre" : $t = 7; break;

          case "(71) Saône-et-Loire" : $t = 8; break;
          case "Saône-et-Loire" : $t = 8; break;

          case "(90) Territoire de Belfort" : $t = 9; break;
          case "Territoire de Belfort" : $t = 9; break;

          case "(89) Yonne" : $t = 10; break;
          case "Yonne" : $t = 10; break;
        }
        $target[] = $t;
        $values[] = $r['total'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return array(
  //   "source" => implode( ",", $source),
  //   "target" => implode( ",", $target),
  //   "values" => implode( ",", $values)
  // );
  return array(
    "source" => $source,
    "target" => $target,
    "values" => $values
  );
}


/* -------------------------------
getDatesMontantsLieu par lieu
----------------------------------
*/
function getDatesMontantsLieu ($connect, $nom=null, $months=0)
{
  $sql = "SELECT montant, date_notification
  FROM marche m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur
  WHERE a.nom_acheteur = '" . $nom . "' ";

  if ($months > 0)
  {
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";
  }

  try
  {
    $result = $connect->query($sql);

    $montant = [];
    $date = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $montant[] = $r['montant'];
        $date[] = "'" . $r['date_notification'] . "'";
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }

  return array(
    "montant" => implode( ",", $montant),
    "date" => implode( ",", $date)
  );
}


/* -------------------------------
get Top acheteurs
----------------------------------
Si nb == 0 retourne tout
Sortie formatée pour plotly
*/
function getAcheteurs ($connect, $nb=5)
{
  $sql = "SELECT a.nom_acheteur, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur
  GROUP BY a.nom_acheteur
  ORDER BY montant DESC ";
  if ($nb !== 0) $sql .= "LIMIT $nb";

  try
  {
    $result = $connect->query($sql);

    $entite = [];
    $value = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $entite[] = '"' . $r['nom_acheteur'] . '"';
        $value[] = $r['montant'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return array(
    "entite" => implode( ",", $entite),
    "value" => implode( ",", $value)
  );
}

/* -------------------------------
get Top acheteurs List
----------------------------------
Si nb == 0 retourne tout
Sortie formatée pour plotly


requête pour un seul département "PLUS GROS ACHETEUR"

SELECT a.nom_acheteur nom, sum(m.`montant`) montant , id_lieu, nom_lieu
FROM `marche` m
INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur
INNER JOIN lieu l ON l.id_lieu = m.id_lieu_execution
WHERE id_lieu = 1038
AND m.date_notification > 2019-04-13
GROUP BY m.id_acheteur
ORDER BY montant DESC
*/
function getAcheteursList ($connect, $nb=5, $categorie=null)
{
  $sql = "SELECT a.nom_acheteur nom, categorie, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur ";
  if ($categorie)
  {
    $sql .= " WHERE categorie = '" . $categorie . "'";
  }
  $sql .= " GROUP BY a.nom_acheteur ";
  // if ($categorie == null) $sql.= ", categorie ";
  $sql.= " ORDER BY montant DESC ";
  if ($nb !== 0) $sql .= "LIMIT $nb";
// echo $sql;
  try
  {
    $result = $connect->query($sql);

    $out = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $row = array();
        $row[] = clean($r['nom']);
        $row[] = $categorie;
        $row[] = $r['montant'];
        $out[] = $row;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return $out;
}

/* -------------------------------
getAcheteursListByTitulaire
----------------------------------
$nb : nombre de titulaires à retourner
$categorie : services, travaux, fournitures
$id_titulaire : id du titulaire

 getTitulairesList($connect, 12, 'services', $id, $nb_mois);
*/
function getAcheteursListByTitulaire ($connect, $nb=5, $categorie=null, $id_titulaire=0, $months=12)
{
  $sql = "SELECT a.nom_acheteur nom, categorie, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN acheteur a ON a.id_acheteur = m.id_acheteur
  INNER JOIN marche_titulaires mt ON mt.id_marche = m.id_marche
  INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
  WHERE m.date_notification > '0000-00-00'
  AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  if ($categorie)
  {
    $sql .= " AND categorie = '" . $categorie . "' ";
  }

  if ($id_titulaire > 0)
  {
    $sql .= " AND t.id_titulaire = '" . $id_titulaire . "' ";
  }

  $sql .= " GROUP BY nom ";
  // if ($categorie == null) $sql.= ", categorie ";
  $sql .= " ORDER BY montant DESC ";
  if ($nb !== 0) $sql .= "LIMIT $nb";

  try
  {
    $result = $connect->query($sql);

    $out = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $row = array();
        $row[] = clean($r['nom']);
        $row[] = $categorie;
        $row[] = $r['montant'];
        $out[] = $row;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $sql;
  return $out;
}

/* -------------------------------
getTitulairesList
----------------------------------
$nb : nombre de titulaires à retourner
$categorie : services, travaux, fournitures
$id_acheteur : id de l'acheteur

 getTitulairesList($connect, 12, 'services', $id, $nb_mois);
*/
function getTitulairesList ($connect, $nb=5, $categorie=null, $id_acheteur=0, $months=12)
{
  $sql = "SELECT t.denomination_sociale nom, categorie, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN marche_titulaires mt ON mt.id_marche = m.id_marche
  INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
  WHERE m.date_notification > '0000-00-00'
  AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  if ($categorie)
  {
    $sql .= " AND categorie = '" . $categorie . "' ";
  }

  if ($id_acheteur > 0)
  {
    $sql .= " AND m.id_acheteur = '" . $id_acheteur . "' ";
  }

  $sql .= " GROUP BY nom ";
  // if ($categorie == null) $sql.= ", categorie ";
  $sql .= " ORDER BY montant DESC ";
  if ($nb !== 0) $sql .= "LIMIT $nb";

  try
  {
    $result = $connect->query($sql);

    $out = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $row = array();
        $row[] = clean($r['nom']);
        $row[] = $categorie;
        $row[] = $r['montant'];
        $out[] = $row;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // print_r( $sql) ;echo "<br>";
  return $out;
}

/* -------------------------------
get Top titulaires
----------------------------------
*/
function getTitulaires ($connect, $nb=5)
{
  $sql = "SELECT t.denomination_sociale, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
  INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire
  GROUP BY t.denomination_sociale
  ORDER BY montant DESC ";
  if ($nb !== 0) $sql .= "LIMIT $nb";

  try
  {
    $result = $connect->query($sql);

    $entite = [];
    $value = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $entite[] = '"' . $r['denomination_sociale'] . '"';
        $value[] = $r['montant'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return array(
    "entite" => implode( ",", $entite),
    "value" => implode( ",", $value)
  );
}


/* -------------------------------
getMontantSankey
----------------------------------
*/
function getMontantSankey ($connect)
{
  $sql = "SELECT SUM(montant) total, nom_lieu
  FROM marche m
  INNER JOIN lieu l ON m.id_lieu_execution = l.id_lieu
  WHERE ";

  switch ($categorie)
  {
    case 'services':
    $sql .= "code_cpv > 49999999";
    break;

    case 'fournitures':
    $sql .= "code_cpv < 45000000";
    break;

    case 'travaux':
    $sql .= "code_cpv < 50000000 AND code_cpv > 44999999";
    break;
  }

  $sql .= " GROUP BY nom_lieu";

  try
  {
    $result = $connect->query($sql);

    $source = [];
    $target = [];
    $values = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $source[] = '"' . $categorie . '"';
        $target[] = '"' . $r['nom_lieu'] . '"';
        $values[] = $r['total'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return array(
    "source" => implode( ",", $source),
    "target" => implode( ",", $target),
    "values" => implode( ",", $values)
  );
}


/* -------------------------------
class Cats
----------------------------------
Pour grouper toutes les informations concernant les principales catégories
Les données sont groupées de façon à être exploitées facilement en FO par
plotly
*/
class Cats
{
  public $tousMarches = [];
  public $services = [];
  public $travaux = [];
  public $fournitures = [];

  public $montantServices = 0;
  public $montantTravaux = 0;
  public $montantFournitures = 0;

  public $nombreServices = 0;
  public $nombreTravaux = 0;
  public $nombreFournitures = 0;

  public $totalMontant = 0;
  public $totalNombre = 0;
}

/* -------------------------------
getCategoriesPrincipales
----------------------------------
*/
function getCategoriesPrincipales ($connect, $months=12, $id=0, $version="acheteur")
{
  $cats = new Cats();

  switch($version)
  {
    case "acheteur" :
    $cats->services = getListByTypeArrZeros($connect, 'services', $months, $id);
    $cats->travaux = getListByTypeArrZeros($connect, 'travaux', $months, $id);
    $cats->fournitures = getListByTypeArrZeros($connect, 'fournitures', $months, $id);
    $cats->tousMarches = getListByTypeArrZeros($connect, null, $months, $id);
    break;
    case "titulaire" :
    $cats->services = getListByTypeArrZerosTitulaires($connect, 'services', $months, $id);
    $cats->travaux = getListByTypeArrZerosTitulaires($connect, 'travaux', $months, $id);
    $cats->fournitures = getListByTypeArrZerosTitulaires($connect, 'fournitures', $months, $id);
    $cats->tousMarches = getListByTypeArrZerosTitulaires($connect, null, $months, $id);
    break;
  }

  $cats->montantServices = array_sum ($cats->services['montants']) ;
  $cats->nombreServices = array_sum ($cats->services['nombre']) ;

  $cats->montantTravaux = array_sum ($cats->travaux['montants']) ;
  $cats->nombreTravaux = array_sum ($cats->travaux['nombre']) ;

  $cats->montantFournitures = array_sum ($cats->fournitures['montants']) ;
  $cats->nombreFournitures = array_sum ($cats->fournitures['nombre']) ;

  $cats->totalMontant = array_sum ($cats->tousMarches['montants']) ;
  $cats->totalNombre = array_sum ($cats->tousMarches['nombre']) ;

  // d($cats);

  return $cats;
}


/* -------------------------------
getListByType
----------------------------------
*/
function getListByType ($connect, $type=null, $months=12, $id=0)
{
  $sql = "SELECT SUBSTR(date_notification, 1, 7) dates, sum(montant) montant, count(id) nombre
          FROM marche m
          WHERE m.date_notification > '0000-00-00'
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  switch ($type)
  {
    case 'services':
    $sql .= " AND code_cpv > 49999999";
    break;

    case 'fournitures':
    $sql .= " AND code_cpv < 45000000";
    break;

    case 'travaux':
    $sql .= " AND (code_cpv < 50000000 AND code_cpv > 44999999)";
    break;
  }

  if ($id > 0)
  {
    $sql .= " AND m.id_acheteur = '" . $id . "'";
  }

  $sql .= " GROUP BY dates ORDER BY dates ASC";

  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montant'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return array(
    "dates" => implode(',', $dates),
    "montants" => implode(',', $montants),
    "nombre" => implode(',', $nombre)
  );
  return $out;
}




/* -------------------------------
getListByTypeArr
----------------------------------
*/
function getListByTypeArr ($connect, $type=null, $months=12, $id=0)
{
  $sql = "SELECT SUBSTR(date_notification, 1, 7) dates, sum(montant) montant, count(id) nombre
          FROM marche m
          WHERE m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";
  if (isset($type)) $sql .= " AND m.categorie = '" . $type . "' ";

  if ($id > 0) $sql .= " AND m.id_acheteur = '" . $id . "'";

  $sql .= " GROUP BY dates ORDER BY dates ASC";

  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montant'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  return array(
    "dates" => $dates,
    "montants" => $montants,
    "nombre" => $nombre
  );
  return $out;
}




/* -------------------------------
getListByTypeArrZeros
----------------------------------
inclut les dates avec zéro marchés

à garder ces fonctions sur les dates
ajoute le nombre de jours écoulés dans le mois à un mois qui démarre à 01
DATE_ADD(mo.date_mois, INTERVAL DAYOFMONTH(CURRENT_DATE()) DAY)
        > DATE_SUB(CURRENT_DATE(), INTERVAL (12) MONTH)
*/
function getListByTypeArrZeros ($connect, $type=null, $months=12, $id=0)
{
  $sql = "SELECT
            SUBSTR(mo.date_mois, 1, 7) dates,
            COALESCE(SUM(montant), 0) montants,
            COALESCE(COUNT(id), 0) nombre,
            categorie
          FROM mois mo
          LEFT JOIN
            (
                SELECT *
                FROM marche
                WHERE date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
                ";
  if (isset($type)) $sql .= " AND categorie = '" . $type . "'";

  if ($id > 0) $sql .= " AND id_acheteur = '" . $id . "' ";

  $sql .= ") ma

          ON SUBSTR(ma.date_notification, 1, 7) = SUBSTR(mo.date_mois, 1, 7)

          WHERE DATE_ADD(mo.date_mois, INTERVAL DAYOFMONTH(CURRENT_DATE()) DAY)
        > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
          AND date_mois < CURRENT_DATE()

          GROUP BY SUBSTR(mo.date_mois, 1, 7)
          ORDER BY date_mois ASC";
// echo $sql;
  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montants'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    return array(
      "sql" => $sql,
      "erreur" => $e
    );
  }
  return array(
    "dates" => $dates,
    "montants" => $montants,
    "nombre" => $nombre,
    "sql" => $sql
  );
  return $out;
}



/* -------------------------------
getListByTypeArrZerosTitulaires
----------------------------------
version titulaires
*/
function getListByTypeArrZerosTitulaires ($connect, $type=null, $months=12, $id=0)
{
  $sql = "SELECT
            SUBSTR(mo.date_mois, 1, 7) dates,
            COALESCE(SUM(montant), 0) montants,
            COALESCE(COUNT(id), 0) nombre,
            categorie
          FROM mois mo
          LEFT JOIN
            (
                SELECT m.*
                FROM marche m
                INNER JOIN marche_titulaires mt ON mt.id_marche = m.id_marche
                WHERE date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
                ";
  if (isset($type)) $sql .= " AND categorie = '" . $type . "'";

  if ($id > 0) $sql .= " AND mt.id_titulaires = '" . $id . "' ";

  $sql .= ") ma

          ON SUBSTR(ma.date_notification, 1, 7) = SUBSTR(mo.date_mois, 1, 7)

          WHERE DATE_ADD(mo.date_mois, INTERVAL DAYOFMONTH(CURRENT_DATE()) DAY)
        > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
          AND date_mois < CURRENT_DATE()

          GROUP BY SUBSTR(mo.date_mois, 1, 7)
          ORDER BY date_mois ASC";

  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montants'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e)
  {
    return array(
      "sql" => $sql,
      "erreur" => $e
    );
  }
  return array(
    "dates" => $dates,
    "montants" => $montants,
    "nombre" => $nombre,
    "sql" => $sql
  );
  return $out;
}



/* -------------------------------
getListByTypeTitulaire
----------------------------------
deprecated
*/
function getListByTypeTitulaire ($connect, $categorie=null, $months=12, $id=0)
{
  $sql = "SELECT SUBSTR(date_notification, 1, 7) dates, sum(montant) montant, count(id) nombre
          FROM marche m
          INNER JOIN marche_titulaires mt ON mt.id_marche = m.id_marche
          INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
          WHERE m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
          ";

  if ( $categorie)
  {
    $sql .= " AND categorie = '" . $categorie . "'";
  }

  if ($id > 0)
  {
    $sql .= " AND t.id_titulaire = '" . $id . "'";
  }

  $sql .= " GROUP BY dates ORDER BY dates ASC";
// return $sql;die;
  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montant'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $type . " " . $months . " " . $id . " " . $sql;
  return array(
    "dates" => implode(',', $dates),
    "montants" => implode(',', $montants),
    "nombre" => implode(',', $nombre),
    "sql" => $sql
  );
  return $out;
}


/* -------------------------------
getMoyenneCategoriesPrincipales
----------------------------------
*/
function getMoyenneCategoriesPrincipales ($connect, $months=12)
{
  $sql = "SELECT SUBSTR(date_notification, 1, 7) dates,
          avg(montant) montant, count(id) nombre
          FROM marche m
          WHERE m.date_notification > '0000-00-00'
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  // switch ($type)
  // {
  //   case 'services':
  //   $sql .= " AND code_cpv > 49999999";
  //   break;
  //
  //   case 'fournitures':
  //   $sql .= " AND code_cpv < 45000000";
  //   break;
  //
  //   case 'travaux':
  //   $sql .= " AND (code_cpv < 50000000 AND code_cpv > 44999999)";
  //   break;
  // }
  //
  // if ($id > 0)
  // {
  //   $sql .= " AND m.id_acheteur = '" . $id . "'";
  // }

  $sql .= " GROUP BY dates ORDER BY dates ASC";

  try
  {
    $result = $connect->query($sql);
    $dates = [];
    $montants = [];
    $nombre = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $dates[] = '"' . $r['dates'] . '"';
        $montants[] = $r['montant'];
        $nombre[] = $r['nombre'];
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $type . " " . $months . " " . $id . " " . $sql;
  return array(
    "dates" => implode(',', $dates),
    "montants" => implode(',', $montants),
    "nombre" => implode(',', $nombre)
  );
  return $out;
}



/* -------------------------------
getProcedures
----------------------------------
*/
function getProcedures ($connect, $id=0, $months=12)
{
  $sql = "SELECT COUNT(nom_procedure) nb_procedure, SUM(montant) total, nom_procedure
          FROM marche m
          INNER JOIN procedure_marche pm on pm.id_procedure = m.id_procedure
          WHERE m.date_notification > '0000-00-00'
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY nom_procedure ORDER BY nom_procedure DESC";

  // d($sql);

  try
  {
    $result           = $connect->query($sql);
    $nb_procedure     = [];
    $total_procedure  = [];
    $nom_procedure    = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        switch ($r['nom_procedure'])
        {
          case "Marché négocié sans publicité ni mise en concurrence préalable" :
            $r['nom_procedure'] = "Marché négocié";
            break;

          case "Procédure concurrentielle avec négociation" :
            $r['nom_procedure'] = "Procédure concurrentielle";
            break;

          case "Procédure négociée avec mise en concurrence préalable" :
            $r['nom_procedure'] = "Procédure négociée";
            break;
        }

        $nb_procedure[]     = $r['nb_procedure'] ;
        $total_procedure[]  = $r['total'] ;
        $nom_procedure[]    = '"' . $r['nom_procedure'] . '"';
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) { d($e); }

  // d( "nb procédures : " . $nb_procedure . ", total_procedure : " . $total_procedure . ", nom_procedure : " . $nom_procedure . ", sql : " . $sql );

  return array(
    "nb_procedure" => implode(',', $nb_procedure),
    "total_procedure" => implode(',', $total_procedure),
    "nom_procedure" => implode(',', $nom_procedure),
    "sql" => $sql
  );
}



/* -------------------------------
getProceduresTitulaires
----------------------------------
*/
function getProceduresTitulaires ($connect, $id=0, $months=12)
{
  $sql = "SELECT COUNT(nom_procedure) nb_procedure, SUM(montant) total, nom_procedure
          FROM marche m
          INNER JOIN procedure_marche pm on pm.id_procedure = m.id_procedure
          INNER JOIN marche_titulaires mt on mt.id_marche = m.id_marche
          INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
          WHERE t.id_titulaire = $id
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  // if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY nom_procedure ORDER BY nom_procedure DESC";


  try
  {
    $result = $connect->query($sql);
    $nb_procedure = [];
    $total_procedure = [];
    $nom_procedure = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        switch ($r['nom_procedure'])
        {
          case "Marché négocié sans publicité ni mise en concurrence préalable" :
            $r['nom_procedure'] = "Marché négocié";
            break;

          case "Procédure concurrentielle avec négociation" :
            $r['nom_procedure'] = "Procédure concurrentielle";
            break;

          case "Procédure négociée avec mise en concurrence préalable" :
            $r['nom_procedure'] = "Procédure négociée";
            break;
        }

        $nb_procedure[] = $r['nb_procedure'] ;
        $total_procedure[] = $r['total'] ;
        $nom_procedure[] = '"' . $r['nom_procedure'] . '"';
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $type . " " . $months . " " . $id . " " . $sql;
  return array(
    "nb_procedure" => implode(',', $nb_procedure),
    "total_procedure" => implode(',', $total_procedure),
    "nom_procedure" => implode(',', $nom_procedure),
    "sql" => $sql
  );
  return $out;
}




/* -------------------------------
getNatures2
----------------------------------
SELECT n.id_nature, COUNT(nom_nature) nb_nature, SUM(montant) total, nom_nature
FROM marche m
INNER JOIN nature n on n.id_nature = m.id_nature
WHERE m.id_acheteur = 2458044065646
GROUP BY nom_nature ORDER BY id_nature ASC
*/
function getNatures2 ($connect, $id=0, $months=12)
{
  // $sql = "SELECT n.id_nature, COUNT(nom_nature) nb_nature, SUM(montant) total, nom_nature
  //         FROM marche m
  //         INNER JOIN nature n on n.id_nature = m.id_nature
  //         WHERE m.date_notification > '0000-00-00'
  //         AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  $sql = "SELECT n.id_nature, COALESCE(COUNT(m.montant), 0) nb_nature, COALESCE(SUM(m.montant),0) total, n.nom_nature
          FROM nature n
          LEFT JOIN (
            SELECT *
            FROM marche
            WHERE date_notification > '0000-00-00'
            AND date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
          ) m ON n.id_nature = m.id_nature ";

  if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY nom_nature ORDER BY id_nature ASC";


  try
  {
    $result = $connect->query($sql);
    $nb_nature = [];
    $nom_nature = [];
    $out=[];
    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $nb_nature[] = $r['nb_nature'] ;
        $nom_nature[] = '"' . $r['nom_nature'] . '"';
        $out[]=$r;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $type . " " . $months . " " . $id . " " . $sql;
  // return array(
  //   "nb_nature" => implode(',', $nb_nature),
  //   "nom_nature" => implode(',', $nom_nature),
  //   "range" => max($nb_nature)
  // );
  return $out;
}



/* -------------------------------
getNaturesAcheteurs
----------------------------------
1 Select la table qui determine le résultat final (même s'il y a des sum=0)
2 Left join d'une surrequête qui filtre par l'id et la date
Si on utilise un join sans sub requete, on aura tous les résultats de la base
*/
function getNaturesAcheteurs ($connect, $id=0, $months=12)
{
    $sql = "SELECT  n.id_nature,
                    COALESCE(COUNT(ma.id_marche),0) nb_nature,
                    COALESCE(SUM(ma.montant),0)  total,
                    nom_nature
          FROM nature n
          LEFT JOIN
            (SELECT m.*
             FROM marche m
             INNER JOIN acheteur a
              ON a.id_acheteur = m.id_acheteur
             WHERE m.id_acheteur = $id
             AND date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) )
          ma ON n.id_nature = ma.id_nature
          GROUP BY nom_nature ORDER BY id_nature ASC ";

  try
  {
    $result = $connect->query($sql);
    $nb_nature = [];
    $nom_nature = [];
    $out = [];
    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $nb_nature[] = $r['nb_nature'] ;
        $nom_nature[] = '"' . $r['nom_nature'] . '"';
        $out[]=$r;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }

  // il faut retourner 1 ligne pour chaque nature même si elle est vide
  for ($i=1 ; $i < 5; $i++)
  {

  }
  return $out;
}





/* -------------------------------
getNaturesTitulaires
----------------------------------
1 Select la table qui determine le résultat final (même s'il y a des sum=0)
2 Left join d'une surrequête qui filtre par l'id et la date
Si on utilise un join sans sub requete, on aura tous les résultats de la base
*/
function getNaturesTitulaires ($connect, $id=0, $months=12)
{
    $sql = "SELECT  n.id_nature,
                    COALESCE(COUNT(ma.id_marche),0) nb_nature,
                    COALESCE(SUM(ma.montant),0)  total,
                    nom_nature
          FROM nature n
          LEFT JOIN
            (SELECT m.*
             FROM marche m
             INNER JOIN marche_titulaires mt
              ON mt.id_marche = m.id_marche
             WHERE id_titulaires = $id
             AND date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) )
          ma ON n.id_nature = ma.id_nature
          GROUP BY nom_nature ORDER BY id_nature ASC ";

  try
  {
    $result = $connect->query($sql);
    $nb_nature = [];
    $nom_nature = [];
    $out = [];
    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $nb_nature[] = $r['nb_nature'] ;
        $nom_nature[] = '"' . $r['nom_nature'] . '"';
        $out[]=$r;
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }

  // il faut retourner 1 ligne pour chaque nature même si elle est vide
  for ($i=1 ; $i < 5; $i++)
  {

  }
  return $out;
}



/* -------------------------------
getNatureByDate
----------------------------------
*/
function getNatureByDate ($connect, $id=0, $id_nature=0, $months=12)
{
  if ($id_nature === 0) return "type de nature non valide";

  // $sql = "SELECT SUM(montant)total, date_notification date
  $sql = "SELECT SUM(montant)total,  SUBSTR(date_notification, 1, 7) date
        FROM `marche` m
        LEFT JOIN nature n ON n.id_nature = m.id_nature
        WHERE m.date_notification > '0000-00-00'
        AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
        AND n.id_nature = $id_nature
        AND date_notification > '0000-00-00' ";

  if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY date ORDER BY date ASC";
// return $sql;

  try
  {
    $result = $connect->query($sql);
    $total = [];
    $date = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $total[] = $r['total'] ;
        $date[] = '"' . $r['date'] . '"';
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }

  return array(
    "total" => implode(',', $total),
    "date" => implode(',', $date),
    "sql" => $sql
  );
}


/* -------------------------------
getNatureByDateTitulaire
----------------------------------
*/
function getNatureByDateTitulaire ($connect, $id=0, $id_nature=0, $months=12)
{
  if ($id_nature === 0) return "type de nature non valide";

  // $sql = "SELECT SUM(montant)total, date_notification date
  $sql = "SELECT SUM(montant)total,  SUBSTR(date_notification, 1, 7) date
        FROM `marche` m
        LEFT JOIN nature n ON n.id_nature = m.id_nature
        INNER JOIN marche_titulaires mt on mt.id_marche = m.id_marche
        INNER JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        WHERE t.id_titulaire = $id
        AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)
        AND n.id_nature = $id_nature
        AND date_notification > '0000-00-00' ";

  // if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY date ORDER BY date ASC";
// return $sql;

  try
  {
    $result = $connect->query($sql);
    $total = [];
    $date = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $total[] = $r['total'] ;
        $date[] = '"' . $r['date'] . '"';
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }

  return array(
    "total" => implode(',', $total),
    "date" => implode(',', $date),
    "sql" => $sql
  );
}




/* -------------------------------
getNature
----------------------------------
1 	Marché
2 	Marché de partenariat
3 	Accord-cadre
4 	Marché subséquent
*/
function getNatures ($connect, $id=0, $nature=null, $months=12)
{
  if (is_null($nature)) return;

  $sql = "SELECT COUNT(nom_nature) nb_nature , nom_nature
          FROM marche m
          INNER JOIN nature n on n.id_nature = m.id_nature
          WHERE m.date_notification > '0000-00-00'
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH) ";

  if ($id > 0) $sql.= " AND m.id_acheteur = $id ";

  $sql .= "GROUP BY nom_nature ORDER BY nom_nature DESC";


  try
  {
    $result = $connect->query($sql);
    $nb_nature = [];
    $nom_nature = [];

    if ($result)
    {
      while ( $r = mysqli_fetch_assoc( $result ) )
      {
        $nb_nature[] = $r['nb_nature'] ;
        $nom_nature[] = '"' . $r['nom_nature'] . '"';
      }
      mysqli_free_result($result);
    }
  }
  catch (Exception $e) {  }
  // return $type . " " . $months . " " . $id . " " . $sql;
  return array(
    "nb_nature" => implode(',', $nb_nature),
    "nom_nature" => implode(',', $nom_nature),
    "range" => max($nb_nature)
  );
  return $out;
}


/* -------------------------------
getBarColumns
----------------------------------
Réalise un count de valeurs groupés selon le type de table
et renvoie un array serialisé pour stockage en BDD

*/
function getBarColumns ($connect, $table)
{
  switch ($table)
  {
    case 'formePrix' :
      $sql = "SELECT o.nom_forme_prix cle, count(m.id) valeur
      FROM `marche` m
      INNER JOIN forme_prix o ON o.id_forme_prix = m.id_forme_prix
      GROUP BY o.nom_forme_prix";
      break;

      case 'nature' :
      $sql = "SELECT o.nom_nature cle, count(m.id) valeur
      FROM `marche` m
      INNER JOIN nature o ON o.id_nature = m.id_nature
      GROUP BY o.nom_nature";
      break;

      case 'lieux' :
      $sql = "SELECT o.nom_lieu cle, sum(montant) as valeur
      FROM marche m
      INNER JOIN lieu o ON o.id_lieu = m.id_lieu_execution
      GROUP BY o.nom_lieu";
      break;

      case 'procedure' :
      $sql = "SELECT o.nom_procedure cle, count(m.id) as valeur
      FROM marche m
      INNER JOIN procedure_marche o ON o.id_procedure = m.id_procedure
      GROUP BY o.nom_procedure";
      break;
    }

    $out = [];

    try
    {
      $result = $connect->query($sql);

      if ($result)
      {
        while ( $r = mysqli_fetch_assoc( $result ) )
        {
          $out [ $r['cle'] ] = $r['valeur'] ;
        }
        mysqli_free_result($result);
      }
    }
    catch (Exception $e)
    {
      $out [0] = "Pas de données";
    }
    return serialize ($out);
  }




  /* -------------------------------
  arraySerialized2Columns
  ----------------------------------
  Les principales stats sont calculées lors de l'import des marchés,
  afin de faire gagner du temps aux utilisateurs.
  Ces stats sont stockées en BDD en tant que array sérialisé.
  Cette fonction, transforme le string en array puis le formatte
  à nouveau en string pour billboard en mode colonnes


  */
  function arraySerialized2Columns ($arrayString)
  {
    $i = 0;
    $data = unserialize( $arrayString );
    $out = "";

    foreach ($data as $k=>$v)
    {
      $i++;
      echo '["' . $k . '",' . $v . ']';
      if ($i < sizeof($data)) echo ",";
    }
    return $out;
  }




  /* -------------------------------
    getDataSiret
  ----------------------------------
  Retourne des donées INSEE à partir d'un siret
  Version titulaire
  */
  function getDataSiret ($connect, $siret)
  {
    if (!isset($siret))
    {
      return 0;
    }

    $out = [];

    $sql = "SELECT id_titulaire, type_identifiant, denomination_sociale, s.id_sirene, s.statut,
     s.siren, s.nic, s.siret, s.dateCreationEtablissement, trancheEffectifsEtablissement, anneeEffectifsEtablissement,activitePrincipaleRegistreMetiersEtablissement,etatAdministratifUniteLegale,
    statutDiffusionUniteLegale,dateCreationUniteLegale,categorieJuridiqueUniteLegale,denominationUniteLegale,
    sigleUniteLegale,activitePrincipaleUniteLegale,nomenclatureActivitePrincipaleUniteLegale,caractereEmployeurUniteLegale,
    trancheEffectifsUniteLegale,anneeEffectifsUniteLegale,nicSiegeUniteLegale,categorieEntreprise,anneeCategorieEntreprise,
    complementAdresseEtablissement,numeroVoieEtablissement,indiceRepetitionEtablissement,typeVoieEtablissement,libelleVoieEtablissement,codePostalEtablissement,
    libelleCommuneEtablissement,codeCommuneEtablissement,codeCedexEtablissement,libelleCedexEtablissement,
    codePaysEtrangerEtablissement,libellePaysEtrangerEtablissement,id_naf,code_naf,libelle_naf,
    tr.libelle_tranche as libelle_tranche_etablissement,tr2.libelle_tranche as libelle_tranche_entreprise, libelle_nafa, libelle_categories_juridiques, s.latitude, s.longitude,
    s.millesime_1, s.millesime_2, s.millesime_3,
    s.ca_1, s.ca_2, s.ca_3,
    s.resultat_1, s.resultat_2, s.resultat_3,
    s.effectif_1, s.effectif_2, s.effectif_3,
    s.fiche_identite
            FROM        titulaire t
            INNER JOIN  sirene s ON t.id_titulaire = s.id_sirene
            LEFT JOIN  naf n ON n.code_naf = s.activitePrincipaleUniteLegale
            LEFT JOIN  nafa n2 ON n2.code_nafa = s.activitePrincipaleRegistreMetiersEtablissement
            LEFT JOIN  tranches tr ON tr.code_tranche = s.trancheEffectifsEtablissement
            LEFT JOIN  tranches tr2 ON tr2.code_tranche = s.trancheEffectifsUniteLegale
            LEFT JOIN  categories_juridiques ct ON ct.code_categories_juridiques = s.categorieJuridiqueUniteLegale
            LEFT JOIN organismes o ON o.codeInsee = s.codeCommuneEtablissement
            WHERE       id_sirene = '" . $siret . "'";
    // echo $sql;
    try
    {
      $result = $connect->query($sql);

      if ($result)
      {
        while ( $r = mysqli_fetch_assoc( $result ) )
        {
          foreach ($r as $k=>$v) :
            $out [ $k ] = $v ;
          endforeach;
        }
        mysqli_free_result($result);
      }
    }
    catch (Exception $e)
    {
      $out = 0;
    }

    return $out;
  }




  /* -------------------------------
    getDataSiretAcheteur
  ----------------------------------
  Retourne des donées INSEE à partir d'un siret
  Version acheteur


  acheteur dans indicateur clés afficher pobulation et revenus moyens (avec les revenus moyens nationaux)
  */
  function getDataSiretAcheteur ($connect, $siret)
  {
    if (!isset($siret))
    {
      return 0;
    }

    $out = [];

    $sql = "SELECT id_acheteur, nom_acheteur as denomination_sociale,
    o.reg, o.dep, o.ncc, o.nom_commune, o.slug,
    o.pop_2015, o.hommes_2015, o.femmes_2015, o.pop_15_plus,
    o.agriculteurs, o.artisans_chefs, o.cadres, o.intermediaires, o.employes,
    o.ouvriers, o.retraites, o.autres, o.menages, o.mediane_niveau_vie,
    s.siren, s.nic, s.siret, s.dateCreationEtablissement, trancheEffectifsEtablissement, anneeEffectifsEtablissement,activitePrincipaleRegistreMetiersEtablissement,etatAdministratifUniteLegale,
    statutDiffusionUniteLegale,dateCreationUniteLegale,categorieJuridiqueUniteLegale,denominationUniteLegale,
    sigleUniteLegale,activitePrincipaleUniteLegale,nomenclatureActivitePrincipaleUniteLegale,caractereEmployeurUniteLegale,
    trancheEffectifsUniteLegale,anneeEffectifsUniteLegale,nicSiegeUniteLegale,categorieEntreprise,anneeCategorieEntreprise,
    complementAdresseEtablissement,numeroVoieEtablissement,indiceRepetitionEtablissement,typeVoieEtablissement,libelleVoieEtablissement,codePostalEtablissement,
    libelleCommuneEtablissement,codeCommuneEtablissement,codeCedexEtablissement,libelleCedexEtablissement,
    codePaysEtrangerEtablissement,libellePaysEtrangerEtablissement,id_naf,code_naf,libelle_naf,
    tr.libelle_tranche as libelle_tranche_etablissement,tr2.libelle_tranche as libelle_tranche_entreprise, libelle_nafa, libelle_categories_juridiques, s.latitude, s.longitude
            FROM        acheteur a
            INNER JOIN  sirene s ON a.id_acheteur = s.id_sirene
            LEFT JOIN  naf n ON n.code_naf = s.activitePrincipaleUniteLegale
            LEFT JOIN  nafa n2 ON n2.code_nafa = s.activitePrincipaleRegistreMetiersEtablissement
            LEFT JOIN  tranches tr ON tr.code_tranche = s.trancheEffectifsEtablissement
            LEFT JOIN  tranches tr2 ON tr2.code_tranche = s.trancheEffectifsUniteLegale
            LEFT JOIN  categories_juridiques ct ON ct.code_categories_juridiques = s.categorieJuridiqueUniteLegale
            LEFT JOIN organismes o ON o.codeInsee = s.codeCommuneEtablissement
            WHERE       id_sirene = '" . $siret . "'";
    // echo $sql;
    try
    {
      $result = $connect->query($sql);

      if ($result)
      {
        while ( $r = mysqli_fetch_assoc( $result ) )
        {
          foreach ($r as $k=>$v) :
            $out [ $k ] = $v ;
          endforeach;
        }
        mysqli_free_result($result);
      }
    }
    catch (Exception $e)
    {
      $out = 0;
    }

    return $out;
  }



  /* -------------------------------
  getMedianeNiveauVie
  ----------------------------------
  Pour afficher dans les indicateurs clés acheteur dans population et revenus moyens,
  en tant que revenus moyens nationaux
  */

  function getMedianeNiveauVie($connect)
  {
    try
    {
      $sql = "SELECT AVG(`mediane_niveau_vie`) as moyenne FROM `organismes`" ;
      $result = $connect->query($sql);
      $moyenne = 0;

      if ($result)
      {
        while ($obj = mysqli_fetch_object($result))
        {
          $moyenne = $obj->moyenne;
        }
      }
      return $moyenne;
    }
    catch (Exception $e)
    {
      return 0;
    }
  }

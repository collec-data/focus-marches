<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);



# date (0 par défaut)
if (isset($_GET['date_min'])) {
  $date_min = $_GET['date_min'];
}

if (isset($_GET['date_max'])) {
  $date_max = $_GET['date_max'];
}

// nb mois si date_min et date_max non définis
if (!isset($_GET['m']) && ((!isset($_GET['date_min'])) || (!isset($_GET['date_max'])))) {
  echo 0;
if (!is_numeric($_GET['m']))
  echo 0;
  $months = $_GET['m'];
}

// id acheteur (optionnel)
$id = "%";
if (isset($_GET['i'])) {
  if (is_numeric($_GET['i']))
    $id = $_GET['i'];
}


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
require_once('modelUtils.php');
require_once('validateurs.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string


try {
  $params = array();
  $types = "";
  $sql = "SELECT t.id_titulaire id, t.denomination_sociale nom, sum(m.`montant`) montant
          FROM `marche` m
          INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
          INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire
          WHERE m.id_acheteur LIKE ?";

  $params[] = $id;
  $types .= "s";

  if($months > 0){
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)";
    $params[] = $months;
    $types .= "d";
  }
  if (isset($date_min) && is_date($date_min) && isset($date_max) && is_date($date_max)) {
    $sql .= " AND m.date_notification BETWEEN ? AND ? ";
    $params[] = $date_min;
    $params[] = $date_max;
    $types .= "ss";
  } else if (isset($date_min)) {
    $sql .= " AND m.date_notification > ? ";
    $params[] = $date_min;
    $types .= "s";
  } else if (isset($date_max)) {
    $sql .= " AND m.date_notification < ? ";
    $params[] = $date_max;
    $types .= "s";
  }

  $sql .= " GROUP BY t.denomination_sociale ORDER BY nom ASC";

  $stmt = prepareAndExecute($connect, $sql, $params, $types);
  $result = $stmt->get_result();

  $num_rows = 0;
  $out = '{ "data" :[';

  while ($r = mysqli_fetch_assoc($result)) {
    $num_rows++;
    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"titulaire.php?i='
      . hsc($r['id']) . '\"><i class=\"fas fa-link\"></i>&nbsp;Page du fournisseur</a>",'
      . '"nom":"' . hsc(clean($r['nom'])) . '",'
      . '"montant":"' . hsc($r['montant']) . '"},';
  }
  $out = substr($out, 0, -1);
  $out .= "]}";

  //// No data :`(
  if ($num_rows === 0) {
    $out = '{ "data" :[]}';
  }
  echo ($out);

  mysqli_free_result($result);
} catch (Exception $e) {
}
?>
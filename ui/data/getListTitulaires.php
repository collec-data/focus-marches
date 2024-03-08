<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);



# date (0 par défaut)
$date_min_param = filter_input(INPUT_GET, 'date_min');
if (isset($date_min_param)) {
  $date_min = $date_min_param;
}

$date_max_param = filter_input(INPUT_GET, 'date_max');
if (isset($date_max_param)) {
  $date_max = $date_max_param;
}

// nb mois si date_min et date_max non définis
$nb_mois_param = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT);
if (!isset($nb_mois_param) && ((!isset($date_max_param)) || (!isset($date_max_param)))) {
  echo 0;
if (!is_numeric($nb_mois_param))
  echo 0;
  $months = $nb_mois_param;
}

// id acheteur (optionnel)
$id = "%";
$id_acheteur_param = filter_input(INPUT_GET, 'i', FILTER_VALIDATE_INT);
if (isset($id_acheteur_param) && is_numeric($id_acheteur_param)){
    $id = $id_acheteur_param;
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

  if($months > 0 && ((!isset($date_min) && !is_date($date_min)) || (!isset($date_max) && !is_date($date_max)))){
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
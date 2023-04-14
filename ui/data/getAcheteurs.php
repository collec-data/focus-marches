<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);


if (!isset($_POST['entite']))
  return;
$entite = $_POST['entite'];


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

$entite = '%' . $entite . '%';
$stmt = $connect->prepare('
  SELECT DISTINCT id_acheteur, nom_acheteur
  FROM `acheteur` m
  WHERE nom_acheteur LIKE ?
');
$stmt->bind_param("s", $entite);
$stmt->execute();
$result = $stmt->get_result();

try {
  $out = "<ul>";
  while ($r = mysqli_fetch_assoc($result)) {
    $out .= '<li class="' . hsc($r['id_acheteur']) . '">' . hsc($r['nom_acheteur']) . '</li>';
  }
  $out .= "</ul>";
  echo $out;
} catch (Exception $e) {
} finally {
  $connect->close();
}

?>
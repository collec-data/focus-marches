<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);



$out = "<ul>";

$entite = filter_input(INPUT_POST, 'entite');
if (!$entite)
  return $out;


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');


$connect->set_charset("utf8"); // nexesario pra real_escape_string

$entite = '%' . $entite . '%';

$stmt = $connect->prepare('
  SELECT DISTINCT id_titulaire, denomination_sociale
  FROM `titulaire` m
  WHERE denomination_sociale LIKE ?
');
$stmt->bind_param("s", $entite);
$stmt->execute();
$result = $stmt->get_result();

try {
  while ($r = mysqli_fetch_assoc($result)) {
    $out .= '<li class="' . hsc($r['id_titulaire']) . '">' . hsc($r['denomination_sociale']) . '</li>';
  }
  $out .= "</ul>";
  echo $out;
} catch (Exception $e) {
} finally {
  $connect->close();
}
?>
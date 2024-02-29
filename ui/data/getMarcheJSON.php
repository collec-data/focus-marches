<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$id_marche_param= filter_input(INPUT_POST, 'id');
if (!$id_marche_param || !isset($id_marche_param))
  return "[{}]";
$id = $id_marche_param;


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

$stmt = $connect->prepare('SELECT m.id_marche, objet, m.id_acheteur, t.id_titulaire, nom_acheteur,
        denomination_sociale, m.code_cpv, libelle_cpv, duree_mois,
        montant, date_notification, id_titulaire, type_identifiant,
        nom_procedure, nom_nature, nom_forme_prix, l.code as code_dept, l.nom_lieu as nom_dept
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
        LEFT JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        LEFT JOIN lieu l ON l.id_lieu = m.id_lieu_execution
        LEFT JOIN procedure_marche p ON p.id_procedure = m.id_procedure
        LEFT JOIN nature n ON n.id_nature = m.id_nature
        LEFT JOIN forme_prix f ON f.id_forme_prix = m.id_forme_prix
        WHERE m.id_marche = ?
');
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();


try {
  while ($r = mysqli_fetch_assoc($result)) {
    $out = '{"m_id":"' . hsc($r['id_marche']) . '",';
    $out .= '"m_cpv_code":' . hsc($r['code_cpv']) . ",";
    $out .= '"m_cpv_libelle":"' . hsc($r['libelle_cpv']) . '",';
    $out .= '"m_acheteur":"' . hsc(clean($r['nom_acheteur'])) . '",';
    $out .= '"m_acheteur_siret":"SIRET - ' . hsc($r['id_acheteur']) . '",';
    $out .= '"m_acheteur_btn":' . hsc($r['id_acheteur']) . ',';
    $out .= '"m_titulaire":"' . hsc(clean($r['denomination_sociale'])) . '",';
    $out .= '"m_titulaire_btn":' . hsc($r['id_titulaire']) . ',';
    $out .= '"m_titulaire_siret":"' . hsc($r['type_identifiant']) . ' - ' . hsc($r['id_titulaire']) . '",';
    $out .= '"m_procedure": "' . hsc($r['nom_procedure']) . '",';
    $out .= '"m_nature": "' . hsc($r['nom_nature']) . '",';
    $out .= '"m_forme_prix": "' . hsc($r['nom_forme_prix']) . '",';
    $out .= '"m_date_notification": "' . hsc($r['date_notification']) . '",';
    $out .= '"m_duree": "' . hsc($r['duree_mois']) . ' mois",';
    $out .= '"m_montant": "' . hsc(number_format($r['montant'], 0, ",", " ")) . ' €",';
    $out .= '"m_lieu": "' . hsc($r['code_dept']) . " - " . hsc($r['nom_dept']) . '",';
    $out .= '"m_objet": "' . hsc(clean($r['objet'])) . '"}';
  }
} catch (Exception $e) {
} finally {
  $connect->close();
  echo ($out);
}
?>
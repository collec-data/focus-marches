<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);


$out = 0;

$lieu_param = filter_input(INPUT_GET, 'lieu', FILTER_VALIDATE_INT);
$forme_prix_param = filter_input(INPUT_GET, 'forme_prix', FILTER_VALIDATE_INT);
$montant_min_param = filter_input(INPUT_GET, 'montant_min', FILTER_VALIDATE_INT);
$montant_max_param = filter_input(INPUT_GET, 'montant_max', FILTER_VALIDATE_INT);
$nature_param = filter_input(INPUT_GET, 'nature', FILTER_VALIDATE_INT);
$procedure_param = filter_input(INPUT_GET, 'procedure', FILTER_VALIDATE_INT);
$acheteur_param = filter_input(INPUT_GET, 'acheteur', FILTER_VALIDATE_INT);
$titulaire_param = filter_input(INPUT_GET, 'titulaire', FILTER_VALIDATE_INT);

if (!$lieu_param && !is_numeric($lieu_param))
  return $out;
if (!$forme_prix_param && !is_numeric($forme_prix_param))
  return $out;
if (!$montant_min_param && !is_numeric($montant_min_param))
  return $out;
if (!$montant_max_param && !is_numeric($montant_max_param))
  return $out;
if (!$nature_param && !is_numeric($nature_param))
  return $out;
if (!$procedure_param && !is_numeric($procedure_param))
  return $out;
if (!$acheteur_param && !is_numeric($acheteur_param))
  return $out;
if (!$titulaire_param && !is_numeric($titulaire_param))
  return $out;


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

# Préparer paramètres
# --------------------

#code_cpv
$code_cpv = filter_input(INPUT_GET, 'code_cpv');
if (!$code_cpv)
  $code_cpv = '%';
else
  $code_cpv = '%' . trim($code_cpv) . '%';

#lieu (select avec 0 par défaut)
$code = $lieu_param;
if ($code < 1)
  $code = "%";

#objet
$objet_marche = filter_input(INPUT_GET, 'objet');
if (!$objet_marche)
  $objet_marche = "%";
else
  $objet_marche = '%' . trim($objet_marche) . '%';

#libelle cpv
$libelle_cpv = filter_input(INPUT_GET, 'libelle_cpv');
if (!$libelle_cpv)
  $libelle_cpv = "%";
else
  $libelle_cpv = '%' . trim($libelle_cpv) . '%';

# montant (0 par défaut)
$montant_min = $montant_min_param;
if (!$montant_min || $montant_min < 1)
  $montant_min = 0;
$montant_max = $montant_max_param;
if (!$montant_max || $montant_max < 1 || $montant_max < $montant_min)
  $montant_max = 10000000000000;

# durée (0 par défaut)
$duree_min = filter_input(INPUT_GET, 'duree_min', FILTER_VALIDATE_INT);
if (!$duree_min || $duree_min < 1)
  $duree_min = 0;
$duree_max = filter_input(INPUT_GET, 'duree_max', FILTER_VALIDATE_INT);
if (!$duree_max || $duree_max < 1 || $duree_max < $duree_min)
  $duree_max = 10000000000000;

# date (0 par défaut)
$date_min = filter_input(INPUT_GET, 'date_min');
if (!$date_min || $date_min == 0 || $date_min < "2019-01-01")
  $date_min = "2019-01-01";

$date_max = filter_input(INPUT_GET, 'date_max');
if (!$date_max || $date_max < 1 || $date_max < $date_min)
  $date_max = "2119-01-01";

# forme de prix (0 == tous)
$id_forme_prix = $forme_prix_param;
if (!$id_forme_prix || $id_forme_prix < 1)
  $id_forme_prix = "%";

# nature (0 == tous)
$id_nature = $nature_param;
if (!$id_nature || $id_nature < 1)
  $id_nature = "%";

# procédure (0 == tous)
$id_procedure = $procedure_param;
if (!$id_procedure || $id_procedure < 1)
  $id_procedure = "%";

# acheteur
$id_acheteur = $acheteur_param;
if (!$id_acheteur || $id_acheteur < 1)
  $id_acheteur = "%";

# titulaire
$id_titulaire = $titulaire_param;
if (!$id_titulaire || $id_titulaire < 1)
  $id_titulaire = "%";

# sql
# --------------------
$sql = 'SELECT DISTINCT m.id, m.id_marche, m.date_notification, nom_acheteur, denomination_sociale, m.code_cpv, libelle_cpv, duree_mois,  montant, l.code as code_dept, l.nom_lieu as nom_dept, date_notification, objet, s.latitude, s.longitude, count(m.id_marche) nb_contrats
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
        LEFT JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        LEFT JOIN lieu l ON l.id_lieu = m.id_lieu_execution
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        LEFT JOIN  sirene s ON t.id_titulaire = s.id_sirene
        LEFT JOIN organismes o ON o.codeInsee = s.codeCommuneEtablissement
WHERE code_cpv LIKE ?
AND l.code LIKE ?
AND m.objet LIKE ?
AND libelle_cpv LIKE ?
AND montant >= ?
AND montant <= ?
AND duree_mois >= ?
AND duree_mois <= ?
AND date_notification >= ?
AND date_notification <= ?
AND id_forme_prix LIKE ?
AND id_nature LIKE ?
AND id_procedure LIKE ?
AND m.id_acheteur LIKE ?
AND id_titulaire LIKE ?
GROUP BY t.id_titulaire ';

try {
  $stmt = $connect->prepare($sql);
  $stmt->bind_param(
    "sssssssssssssss",
    $code_cpv,
    $code,
    $objet_marche,
    $libelle_cpv,
    $montant_min,
    $montant_max,
    $duree_min,
    $duree_max,
    $date_min,
    $date_max,
    $id_forme_prix,
    $id_nature,
    $id_procedure,
    $id_acheteur,
    $id_titulaire
  );
  $stmt->execute();
  $result = $stmt->get_result();
  $num_rows = 0;
  $out = '{ "data" :[';

  while ($r = mysqli_fetch_assoc($result)) {
    $num_rows++;
    // catégorie cpv
    $categorie = "Services";
    $color = 'rgb(93, 164, 214)';

    if ($r['code_cpv'] < "50000000") {
      $categorie = "Travaux";
      $color = 'rgb(255, 144, 14)';
    }

    if ($r['code_cpv'] < "45000000") {
      $categorie = "Fournitures";
      $color = 'rgb(44, 160, 101)';
    }
    list($y, $m, $d) = explode('-', $r['date_notification']);

    $out .= '{"id":"<button class=\"button voirMarche is-info small\" data-id=\"' . $r['id_marche'] . '\">Voir</button>",' .
      '"idN":"' . hsc(clean($r['id_marche'])) . '",' .
      '"acheteur":"' . hsc(clean($r['nom_acheteur'])) . '",' .
      '"titulaire":"' . hsc(clean($r['denomination_sociale'])) . '",' .
      '"code_cpv":"<span>' . hsc($r['code_cpv']) . '</span> ' . hsc($r['libelle_cpv']) . '",' .
      '"libelle_cpv":"' . hsc($r['libelle_cpv']) . '",' .
      '"categorie":"' . hsc($categorie) . '",' .
      '"color":"' . hsc($color) . '",' .
      '"date":"' . hsc("$d/$m/$y") . ' <span class=\"date\">(' . hsc($r['duree_mois']) . ' mois)</span>",' .
      '"date_notification":"' . hsc($r['date_notification']) . '",' .
      '"code_dept":"' . hsc($r['code_dept']) . '",' .
      '"nom_dept":"' . hsc($r['nom_dept']) . '",' .
      '"latitude":"' . hsc($r['latitude']) . '",' .
      '"longitude":"' . hsc($r['longitude']) . '",' .
      '"montant":"' . hsc($r['montant']) . '",' .
      '"nb_contrats":"' . hsc($r['nb_contrats']) . '"},';
  }
  $out = substr($out, 0, -1);
  $out .= "]}";

  //// No data :`(
  if ($num_rows === 0) {
    $out = '{ "data" :[]}';
  }
  echo $out;
} catch (Exception $e) {
  // echo 'Erreur : ' . $e->getMessage();
} finally {
  $connect->close();
}
?>
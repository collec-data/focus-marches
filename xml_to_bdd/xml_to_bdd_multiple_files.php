<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../ui/assets/bulma/bulma.min.css">
  <style>body {padding: 40px;}</style>
</head>
<body>
<h1 class="title">Import xml to BDD</h1>
<p><a href="#stats">Statistiques de l'import</a></p>

  <?php
/* ------------------------------------------------
| XML to BDD
|
| Ouvre et parse le fichier XML des marchés publics
| et insère son contenu en BDD
--------------------------------------------------

Utiliser dans un navigateur sinon pas de variabls $_SERVER
*/


//
/* -------------------------------
BDD
-------------------------------*/

// serveur xampp sous windows
if ($_SERVER['SERVER_NAME'] == 'localhost')
{
  $host = 'localhost';
  $user = '';
  $pass = '';
  $name = 'marches_publics';
}

// serveur easyphp sous windows
if ($_SERVER['SERVER_NAME'] == '127.0.0.1')
{
  $host = 'localhost';
  $user = '';
  $pass = '';
  $name = 'marches_publics';
}

$connect  = new mysqli($host, $user, $pass, $name);
$connect->query("SET NAMES 'utf8'");

// check
if ( $connect->connect_error )
{
  die("Erreur de connexion à la BDD : " . $connect->connect_error);
}

/* -------------------------------
Delete
--------------------------------
Vider les tables d'anciens enregistrements
*/
$tables_a_vider = ['marche', 'acheteur', 'lieu', 'marche_titulaires', 'titulaire'];

echo "<br><h2 class='subtitle'>Vidage de tables</h2>";

foreach ($tables_a_vider as $k=>$table)
{
  try
  {
    $connect->query("DELETE FROM $table");
    echo "Table $table vidée<br>";
  }
  catch (Exception $e)
  {
    echo "Impossible de vider la table $table $e<br>";
  }
}


/* -------------------------------
Utilitaires
--------------------------------*/

// Log
$log = false;
$nan = 'nan';

function e($s)
{
  global $log;
  if ($log)
  {
    if (is_array($s))
    {
      foreach ($s as $v) echo $v . " ";
    }
    else
    {
      echo $s;
    }
    echo "<br>";
  }
}

// valeurs par defaut
function val_null ($v)
{
  if ($v == '' || $v == 'nan')
  {
    return 0;
  }
  return $v;
}

function date_zero_null ($v)
{
  if (is_nan_string($v) ||
  $v == '' ||
  $v == 0)
  {
    return '0000-00-00';
  }
  return $v;
}

function is_nan_string ($v)
{
  if (strpos($v, 'nan') !== false)
  {
    return true;
  }
  return false;
}

/* -------------------------------
Parse
--------------------------------*/
$i = 1;
$m = 0;
$num_rows_marches = 0;
$acheteurs_crees = 0;
$titulaires_crees = 0;
$pas_nom_acheteur = 0;
$pas_titulaire = 0;
$date_anteriure = 0;
$pas_montant = 0;
$modifications = 0;
$modifications_update = 0;

echo "<h2 class='subtitle'>Log import</h2>";


$files = [
  "marches-19-01.xml",
  "marches-19-02.xml",
  "marches-19-03.xml",
  "marches-19-04.xml",
  "marches-19-05.xml",
  "marches-19-06.xml",
  "marches-19-07.xml",
  "marches-19-08.xml",
  "marches-19-09.xml",
  "marches-19-10.xml",
  "marches-19-11.xml",
  "marches-19-12.xml",
  "marches-20-01.xml",
  "marches-20-02.xml",
  "marches-20-03.xml",
  "marches-20-04.xml",
  "marches-20-05.xml",
  "marches-20-06.xml",
  "marches-20-07.xml",
  "marches-20-08.xml",
  "marches-20-09.xml",
  "marches-20-10.xml",
  "marches-20-11.xml",
  "marches-20-12.xml",
  "marches-21-01.xml",
  "marches-21-02.xml",
  "marches-21-03.xml",
  "marches-21-04.xml",
  "marches-21-05.xml",
];

foreach ($files as $file)
{
  /* -------------------------------
  Ouvrir le fichier
  -------------------------------
  */

  if (file_exists($file))
  {
    $xml = simplexml_load_file($file);
  }
  else
  {
    echo("Echec lors de l\'ouverture du fichier : $file \n");
    continue;
  }


  foreach ($xml as $k=>$marche)
  {
    e("<br><br>------------------------");
    e(["<b<ID:</b> ", $i , $marche->id]);
    e([$marche->acheteur->id , $marche->acheteur->nom]);
    e("<br>");
    $i++;

  // $id_marche = $marche->id . $marche->acheteur->id;
  $id_marche = $marche->uuid;

/*
  //// Tester si le marché n'est pas déjà en stock
  try
  {
    $result = mysqli_query($connect, "SELECT COUNT(id_marche) as nb FROM marche WHERE id_marche = '" . $id_marche . "';");
    $nb = 0;
    if ($result)
    {
      while ($r = mysqli_fetch_object($result))
      {
        $nb = $r->nb;
      }
    }
  }
  catch (Exception $e)
  {
    echo $e;
  }

  if ($nb > 0) //// Marché déjà connu ?
  {
    echo "L'id $id_marche est déjà présent sur la BDD. On zappe";
    continue;
  }
*/
  /*
  Filtrer les marchés
  --------------------
  * acheteur sans NOM
  * titulaires sans SIRET ou sans DENOMINATION SOCIALE
  * date antérieure au 1er janvier 2019
  * sans montant
  */


  // Filtrer les contrats antérieurs au 1er janvier 2019
  $notif = date_zero_null($marche->dateNotification);
  if ($notif < "2019-01-01")
  {
    $date_anteriure++;
    echo "<br>Date antérieure : $notif ". " " . $marche->uuid ;
    continue;
  }


  //// Si pas d'acheteur on zappe le marché :
  if ($marche->acheteur->nom == "" || $marche->acheteur->id == "" )
  {
    $pas_nom_acheteur++;
    echo "<br>Pas d'acheteur : " . $marche->uuid ;
    continue;
  }


  //// Si pas de titulaire on zappe le marché :
  $goId = false;
  $goDenomination = false;

  //// bug de l'API : il manque les balises <titulaires><titulaire>
  //// à la place on a <entry>
  if (isset($marche->titulaires->titulaire))
  {
    $titulaires = $marche->titulaires->titulaire;
  }
  else
  {
    $titulaires = $marche->entry;
  }

  foreach ($titulaires as $j=>$w)
  {
    if ((trim($w->id)) != "") $goId = true;
    if ((trim($w->denominationSociale)) != "") $goDenomination = true;
  }

  //// Pas d'id ou pas de titulaire
  if (!$goId || !$goDenomination)
  {
    $pas_titulaire++;
    echo "<br>Pas de titulaire : " . $w->id . " - " .  $w->denominationSociale . " " . $marche->uuid ;
    continue;
  }


  // avons-nous un montant ?
  if (!$marche->montant)
  {
    $pas_montant++;
    echo "<br>Pas de montant " . $marche->uuid ;
    continue;
  }


  /*
  Modifications
  ----------------------
  Si nous sommes face à une modification d'un marché,
  passer directement au update du marche

  <modifications>
    <modification>
      <objetModification><![CDATA[Avenant n°1]]></objetModification>
      <dateSignatureModification><![CDATA[2019-01-04]]></dateSignatureModification>
      <datePublicationDonneesModification><![CDATA[2019-01-09]]></datePublicationDonneesModification>
      <montant><![CDATA[21509.98]]></montant>
    </modification>
  </modifications>
  */

  if ($marche->modifications)
  {
    $modifications++;
    try
    {
      // vérifier qu'on a le marché en BDD (uuid)
      $sql = "SELECT * FROM marche WHERE id_marche='" . $id_marche . "'";
      $result = $connect->query(  $sql );

      if ($result->num_rows > 0)
      {
        while ( $r = mysqli_fetch_object( $result ) )
        {
          // greffer la date et l'objet de l'update
          $objet = $r->objet . "\n\n"
                  . "Modification - " . $marche->modifications->modification->dateSignatureModification . "\n"
                  . $marche->modifications->modification->objetModification . "\n"
                  . "Montant initial : " . $marche->montant . ". "
                  . "Nouveau montant : " . $marche->modifications->modification->montant . "\n";
          // /!\ durée, titulaire absents
          // màj
          try
          {
            $sql = "UPDATE marche
                    SET    objet = '" . $objet . "',
                           montant = '" . $marche->modifications->modification->montant . "'
                    WHERE id_marche='" . $id_marche . "'";
            $result = $connect->query(  $sql );
            $modifications_update++;
          }
          catch(Exception $e)
          {
            print_r($e);
          }
        } // whle
      } // if result
    } // try
    catch(Exception $e)
    {
      print_r($e);
    }
    continue;
  } //if marche->modifications




  /*
  Acheteur
  -------------  */
  // Créer le titulaire (ou pas)
  // check avec la denomination sociale pas avec l'id
  // Ajouter un nomUI (nom d'interface) sans "Commune de .., "
  // Changer Conseil Départ.. en CD "

  try
  {
    $sql = 'SELECT *
    FROM acheteur
    WHERE id_acheteur = "' . $marche->acheteur->id . '"';
  //  WHERE nom_acheteur = "' . $marche->acheteur->nom . '"';

    // echo $sql . "<br>";
    $marche->acheteur->nom = str_ireplace('"',"",$marche->acheteur->nom);
    $nom = $marche->acheteur->nom;
    $nomLower = mb_strtolower($nom);

    if ( mb_substr($nomLower, 0, 3) == 'ca ') $nom = mb_substr($nom, 3) . " (CA)";
    elseif ( mb_substr($nomLower, 0, 6) == 'cc de ') $nom = mb_substr($nom, 6) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 6) == 'cc du ') $nom = mb_substr($nom, 6) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 3) == 'cc ') $nom = mb_substr($nom, 3) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 30) == "communauté d'agglomération du ") $nom = mb_substr($nom, 30) . " (CA)";
    elseif ( mb_substr($nomLower, 0, 27) == "communauté d'agglomération ") $nom = mb_substr($nom, 27) . " (CA)";
    elseif ( mb_substr($nomLower, 0, 19) == "communauté urbaine ") $nom = ucfirst(mb_substr($nom, 19)) . " (CU)";
    elseif ( mb_substr($nomLower, 0, 29) == 'communauté de communes de la ') $nom = mb_substr($nom, 29) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 26) == "communauté de communes du ") $nom = mb_substr($nom, 26) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 25) == "communauté de communes d'") $nom = mb_substr($nom, 25) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 23) == "communauté de communes ") $nom = mb_substr($nom, 23) . " (CC)";
    elseif ( mb_substr($nomLower, 0, 10) == "commune d'") $nom = ucfirst(mb_substr($nom, 10)) . " (Commune)";
    elseif ( mb_substr($nomLower, 0, 11) == "commune de ") $nom = mb_substr($nom, 11) . " (Commune)";
    elseif ( mb_substr($nomLower, 0, 22) == "conseil départemental ") $nom = "CD " . mb_substr($nom, 22);
    elseif ( mb_substr($nomLower, 0, 47) == "service départemental d'incendie et de secours ") $nom = "SDIS " . mb_substr($nom, 47);
    $nomUI = $nom;

    // récupérer l'id existant (si c'est le cas)
    $result = $connect->query(  $sql );
    // print_r($result);
    // echo "<br>";

    if ($result->num_rows == 0)
    {
      // echo "Pas d'id existant : insert<br>";
      // Pas d'id existant : insert
      $sql = 'INSERT INTO acheteur (id_acheteur, nom_acheteur, nom_ui)
      VALUES (' . $marche->acheteur->id . ', "'
        . $connect->real_escape_string($marche->acheteur->nom) . '", "'
        . $connect->real_escape_string($nomUI)
        . '");';

      e("<b>Acheteur: </b> " .  $sql . "<br>");
      try
      {
        $connect->query(  $sql );
        $acheteurs_crees++;
      }
      catch (Exception $e)
      {
        print_r($e);
      }
    }
  }
  catch (Exeption $e)
  {
    print_r($e);
  }


    /*
    Nature
    ------------- */
    switch ($marche->nature)
    {
      case "Marché" :
      $nature = 1; break;

      case "Marché de partenariat" :
      $nature = 2; break;

      case "Accord-cadre" :
      $nature = 3; break;

      case "Marché subséquent" :
      $nature = 4; break;

      default :
      $nature = 1;
    }
    // echo $marche->nature . " -- " . $nature . "<br>";
    e("\tNature: " . $marche->nature . " [" . $nature . "]");


    /* Procédure
    ----------------- */

    switch ($marche->procedure)
    {
      case "Procédure adaptée":
      $procedure = 1 ; break;

      case "Appel d'offres ouvert":
      $procedure = 2 ; break;

      case "Appel d'offres restreint":
      $procedure = 3 ; break;

      case "Procédure concurrentielle avec négociation":
      $procedure = 4 ; break;

      case "Procédure négociée avec mise en concurrence préalable":
      $procedure = 5 ; break;

      case "Marché négocié sans publicité ni mise en concurrence préalable":
      $procedure = 6 ; break;

      case "Dialogue compétitif":
        $procedure = 7 ; break;

      default :
      $procedure = 1;
    }
      // echo $marche->procedure . " -- " . $procedure . "<br>";
      e("Procédure: " . $marche->procedure . " [" . $procedure . "]");


      /*
      Lieu d'exécution :
      --------------------------------------
      insérer si le lieu n'existe pas encore
      */
      $sql = 'SELECT * FROM lieu
      WHERE code = ' . $connect->real_escape_string($marche->lieuExecution->code) . '
      AND type_code = "' . $connect->real_escape_string($marche->lieuExecution->typeCode) . '"
      AND nom_lieu = "' . $connect->real_escape_string($marche->lieuExecution->nom) . '"';
      // echo $sql . "<br>";
      try
      {
        $result = $connect->query(  $sql );
        // echo "nombre enreg. = " . $result->num_rows . "<br>" ;

        if ($result->num_rows < 1)
        {
          $sql = 'INSERT INTO lieu (code, type_code, nom_lieu)
          VALUES ('
            . $connect->real_escape_string($marche->lieuExecution->code) . ', "'
            . $connect->real_escape_string($marche->lieuExecution->typeCode) . '", "'
            . $connect->real_escape_string($marche->lieuExecution->nom)
            . '");';
            // echo "INSERT de $sql <br>";
            try
            {
              $connect->query(  $sql );
              $lieu = $connect->insert_id;
            }
            catch (Exception $e)
            {
              print_r($e);
              $lieu = 0;
            }
          }
          else
          {
            while ($row = mysqli_fetch_row($result))
            {
              $lieu = $row[0];
            }
          }
        } // try lieu d'exécution
        catch (Exception $e)
        {
          print_r($e);
        }
        e("Lieu: " . $marche->lieuExecution->nom . " [" . $lieu . "]");


        /*
        Forme de prix
        --------------------------------------
        */

        switch ($marche->formePrix)
        {
          case "Ferme" :
          $forme = 1; break;

          case "Ferme et actualisable" :
          $forme = 2; break;

          case "Révisable" :
          $forme = 3; break;

          default :
          $forme = 1;
        }
        e("formePrix: " . $marche->formePrix . " [" . $forme . "]");


        /*
        Titulaires
        --------------------------------------
        chercher les titulaires par la denomination sociale car l'id varie à chaque fois (mal généré en amont)
        */

        foreach ($titulaires as $j=>$w)
        {
          e("\t" . $w->denominationSociale);

          // Créer le titulaire (ou pas)
          // check avec la denomination sociale pas avec l'id
          // TODO: changer au SIRET

          try
          {
            $sql = 'SELECT *
            FROM titulaire
            WHERE id_titulaire = "' . $w->id . '"';
            // WHERE denomination_sociale = "' . $w->denominationSociale . '"';
            e("  Check titulaire : " . $sql);

            // récupérer l'id existant (si c'est le cas)
            $result = $connect->query(  $sql );

            if ($result->num_rows > 0)
            {
              // while ($row = mysqli_fetch_row($result))
              // {
              //   $id_titulaire = $row[0];
              // }
              $id_titulaire = $w->id;
            }
            else
            {
              // echo "Pas d'id existant : insert<br>";
              // Pas d'id existant : insert
              $sql = 'INSERT INTO titulaire (id_titulaire, type_identifiant, denomination_sociale)
              VALUES ("'
                . $w->id . '", "'
                . $connect->real_escape_string($w->typeIdentifiant) . '", "'
                . $connect->real_escape_string($w->denominationSociale) .
                '")';

                // echo $sql . "<br>";

                try
                {
                  $connect->query(  $sql );
                  $titulaires_crees++;
                }
                catch (Exception $e)
                {
                  print_r($e);
                }
              }
            }
            catch (Exeption $e)
            {
              print_r($e);
            }
            // echo "<br><br>";
            // Créer le lien entre les tables (toujours)
            if (!isset($id_titulaire))
            {
              $id_titulaire = $w->id;
            }

            //// Les SIRET peuvent avoir des chaines de caractères
            $sql = 'INSERT INTO marche_titulaires (id_marche, id_titulaires)
            VALUES ("'
              . $id_marche . '", "'
              . $w->id .
              '")';

            // echo "  Lien entre tables : " . $sql . "<br>";

            try
            {
              $connect->query(  $sql );
            }
            catch (Exception $e)
            {
              print_r($e);
            }

          } // foreach titulaires

            e("\tTitulaires créés : $titulaires_crees");


            /*
            Marche
            --------------------------------------
            */

            //// Code CPV -> cat ppale du marché
            $categorie = "travaux";

            if ($marche->codeCPV > 49999999)
            {
              $categorie = 'services';
            }

            if ($marche->codeCPV < 45000000)
            {
              $categorie = 'fournitures';
            }
            e("codeCPV: " . $marche->codeCPV . " [" . $categorie . "]");


            // valeurs par défaut
            $dureeMois = val_null($marche->dureeMois);
            $notif = date_zero_null($marche->dateNotification);
            $pub = date_zero_null($marche->datePublicationDonnees);
            $etab = date_zero_null($marche->dateTransmissionDonneesEtalab);

            if (is_nan_string($marche->id))
            {
              $marche->id = str_replace('nan', '', $marche->id);
            }

            if (is_nan_string($marche->acheteur->id))
            {
              $marche->acheteur->id = str_replace('nan', '', $marche->acheteur->id);
            }
            if (is_nan_string($marche->codeCPV))
            {
              $marche->codeCPV = 0;
            }
            if (is_nan_string($marche->montant))
            {
              $marche->montant = 0;
            }

            $sql = 'INSERT INTO marche (id_marche, id_acheteur, id_nature, objet, code_cpv,
              categorie, id_procedure, id_lieu_execution, duree_mois, date_notification, date_publication_donnees,
              date_transmission_etalab, montant, id_forme_prix, modifications)
              VALUES("'
                . $id_marche . '", '
                . $marche->acheteur->id . ', '
                . $nature . ', "'
                . $connect->real_escape_string($marche->objet) . '", '
                . $marche->codeCPV . ', "'
                . $categorie . '", '
                . $procedure . ', '
                . $lieu . ', '
                . $dureeMois . ', "'
                . $notif . '", "'
                . $pub . '", "'
                . $etab . '", '
                . $marche->montant . ', '
                . $forme . ', "'
                . $connect->real_escape_string($marche->modifications)
                . '")';


                e("marche: " . $sql);

                try
                {
                  $result = $connect->query(  $sql );
                  $m++;
                  // echo("<br>$m - marche: " . $sql);
                }
                catch (Exception $e)
                {
                  print_r($e);
                }

} // foreach principal

} // iteration sur l'ensemble des fichiers

              echo "<br><br><h2 class='subtitle' id='stats'>Statistiques import</h2>";
              echo "<br>N° marchés = " . ($i-1);
              echo "<br>N° marchés OK = $m";
              echo "<br>N° acheteurs = $acheteurs_crees \n\n";
              echo "<br>N° titulaires = $titulaires_crees \n\n";
              echo "<br>N° modifications vues = $modifications  \n\n";
              echo "<br>N° modifications réalisées = $modifications_update \n\n";

              echo "<br><br>Problèmes\n\n";
              echo "<br>1. Date antérieure = $date_anteriure \n\n";
              echo "<br>2. Pas nom acheteur = $pas_nom_acheteur \n\n";
              echo "<br>3. Pas titulaires = $pas_titulaire \n\n";
              echo "<br>4. Pas montant = $pas_montant \n\n";


              ?>
</body>
</html>

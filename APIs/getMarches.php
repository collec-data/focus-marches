<?php

/* ----------------------------------------
    Télécharge les marchés de l'API d'Atexo
   ----------------------------------------

   MISE A JOUR GLOBALE :
   >getMarches.php
   >getAPIs.php

   1. Get Token :
   https
   <?xml version="1.0" encoding="UTF-8"?>
   <ticket> </ticket>

   2. Get data :
   https://marches.e-bourgogne.fr/app.php/api/v1/donnees-essentielles/contrat/format-pivot
   (form-data)
   token :
   version : v1

*/
function getToken ()
{
  try
  {
    $url = "https://marches.ternum-bfc.fr/api.php/ws/authentification/connexion/____________/___________";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);
    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
        // return false;
    }
    curl_close($ch);

    $xml = simplexml_load_string($result);

    return $xml;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}
function getToken2 ()
{
  try
  {
    $url = "https://marches.ternum-bfc.fr/api.php/ws/authentification/connexion/__________/_____________";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);
    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
        // return false;
    }
    curl_close($ch);

    // $xml = simplexml_load_string($result);

    return $result;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}

function getMarches ($token)
{
  try
  {
    $url = "https://marches.ternum-bfc.fr/app.php/api/v1/donnees-essentielles/contrat/format-pivot";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $postdata= array("token"=>$token, "version"=>"v1");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);
    print_r($result);
    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
        // return false;
    }
    curl_close($ch);

    $xml = simplexml_load_string($result);

    return $xml;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}



function getMarches2File ($token, $filename, $date_min, $date_max)
{
  try
  {
    $url_etendu = "https://marches.ternum-bfc.fr/app.php/api/v1/donnees-essentielles/contrat/format-etendu";

    $url = "https://marches.ternum-bfc.fr/app.php/api/v1/donnees-essentielles/contrat/format-pivot";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url_etendu);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $postdata= array("token"=>$token, "version"=>"v1",
      "date_notif_min"=>$date_min, "date_notif_max"=>$date_max );
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);

    file_put_contents($filename, $result);

    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}

$token = getToken ();
print("On utilise le token $token\n");

$dates = [

  ["marches-21-06.xml", "01-06-2021", "30-06-2021"],
];
echo "ini\n";

foreach ($dates as $d) :
  getMarches2File ($token, $d[0], $d[1], $d[2]);
  echo ("end $d[0] \n");
endforeach;

echo "end\n";


?>

<?php

/* --------------------------------------------
    Télécharge les organismes de l'API d'Atexo
   --------------------------------------------
   1. Get Token :
   https://marches.ternum-bfc.fr/api.php/ws/authentification/connexion/______________/_____________
   <?xml version="1.0" encoding="UTF-8"?>
   <ticket>_____________________</ticket>

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
    $url = "https://marches.ternum-bfc.fr/api.php/ws/authentification/connexion/____/____";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // https://stackoverflow.com/questions/28858351/php-ssl-certificate-error-unable-to-get-local-issuer-certificate
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
    $url = "https://marches.ternum-bfc.fr/api.php/ws/authentification/connexion/_____/______";

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

function getOrganismes ($token)
{
  try
  {
    // GET /api/{version}/organismes.{format}

    $url = "https://marches.ternum-bfc.fr/app.php/api/v1/organismes.xml";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $postdata= array("token"=>$token, "version"=>"v1", "format"=>"xml" );
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

    // $xml = simplexml_load_string($result);
    $date = date("Y-m-d");
    file_put_contents($date . '-organismes.txt', $result);

    // return $xml;
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
    // https://stackoverflow.com/questions/28858351/php-ssl-certificate-error-unable-to-get-local-issuer-certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($ch);

    file_put_contents($filename, $result);

    if (curl_errno($ch))
    {
        echo 'Error:' . curl_error($ch);
        // return false;
    }
    curl_close($ch);

    // $xml = simplexml_load_string($result);
    //
    // return $xml;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}

$token = getToken ();
echo $token;

getOrganismes($token);


?>

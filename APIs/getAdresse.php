<?php

/* ----------------------------------------
    getAdresse
   ----------------------------------------
   API : https://geo.api.gouv.fr/adresse
   URL objectif :
   https://api-adresse.data.gouv.fr/search/
   ?q=RUE+DU+CHAMP+DES+CANNES+BEAUNE
   &citycode=21054
   &autocomplete=1

*/
function getAdresse ($adresse, $codeInsee)
{
  try
  {
    $adresse = str_ireplace(" ", "+", $adresse);
    $url = "https://api-adresse.data.gouv.fr/search/?q=" . $adresse
    . "&citycode=" . $codeInsee
    . "&autocomplete=1";

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
    }
    curl_close($ch);

    $json = json_decode($result);

    return $json->features[0]->geometry->coordinates;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
}

// list($longitude, $latitude) = getAdresse("RUE DU CHAMP DES CANNES BEAUNE", "21054") ;
// echo "longitude $longitude , latitude $latitude";
?>

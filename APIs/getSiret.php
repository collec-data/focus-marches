<?php

/* ----------------------------------------
    getSiret
   ----------------------------------------

*/
function getSiret ($siret, $access_token, $token_type)
{
  try
  {
    $url = "https://api.insee.fr/entreprises/sirene/V3/siret/" . $siret;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $headers = array();
    $headers[] = "Accept: application/json";
    $headers[] = "Authorization: $token_type $access_token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch))
    {
        // echo 'Error:' . curl_error($ch);
        return false;
    }
    curl_close($ch);

    $json = json_decode($result);
    return $json;
  }
  catch (Exception $e)
  {
    echo $e;
    return 0;
  }
}
?>

<?php

/* ----------------------------------------
    getInfogreffe
   ----------------------------------------
   API : https://opendata.datainfogreffe.fr/explore/dataset/chiffres-cles-2018/api/?sort=ca_1
   URL objectif :
   https://opendata.datainfogreffe.fr/api/records/1.0/search/
   ?dataset=chiffres-cles-2018
   &q=528543267
   &sort=ca_1
   &refine.nic=00038
   &refine.siren=017240953


*/
function getInfogreffe ($siren)
{
  try
  {
    $url = "https://opendata.datainfogreffe.fr/api/records/1.0/search/?dataset=chiffres-cles-2020&q=" . $siren . "&sort=ca_1";

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

    $json = json_decode($result);

    return $json;
  }
  catch (Exception $e)
  {
    echo $e;
    return null;
  }
} 
?>

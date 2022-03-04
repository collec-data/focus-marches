<?php

/* ----------------------------------------
    getTokenSirene
   ----------------------------------------
   Générer un jeton d'accès en utilisant
   le type d'authentification de client.
   Il est valable 7 jours.
*/
function getTokenSirene ()
{
  $url_token = "https://api.insee.fr/token";
  $consumer_key = "o6PlvyST6cA7dtsq8J2W1Ih9Iv8a";
  $consumer_secret = "6kcyVdIePfJtB7z_SePlo2QZkaoa";

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url_token);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

  $hash = base64_encode($consumer_key . ":" . $consumer_secret);
  $headers = array();
  $headers[] = "Authorization: Basic $hash";
  $headers[] = "Content-Type: application/x-www-form-urlencoded";
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);
  if (curl_errno($ch))
  {
      // echo 'Error:' . curl_error($ch);
      return false;
  }
  curl_close($ch);

  /*
  Réponse
  {"access_token":"_______________________",
  "scope":"am_application_scope default",
  "token_type":"Bearer",
  "expires_in":577248}
  */
  $json = json_decode($result);
  return array($json->access_token, $json->token_type);
}
print_r(getTokenSirene ());
?>

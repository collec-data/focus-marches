<?php

$page = "mentions-legales";
$title = "Mentions légales | Données essentielles du profil d'acheteur de ".gettext("Territoires Numériques");
$desc = "Mentions légales | Données essentielles du profil d'acheteur de ".gettext("Territoires Numériques");


include('inc/head.php');
include('inc/config.php');
include('inc/localization.php');
?>
<!-- entre heads : ajouter extra css , ... -->
<style>
.title.is-3 { font-size: 1.4rem;}
</style>
<?php
include('inc/nav.php');
?>

<div id="main">

  <div class="container">

  <h1 class='title'>Mentions légales</h1>
  <p><?php echo gettext("Le site Focus Marchés est édité par le GIP Territoires Numériques Bourgogne-Franche-Comté.");?></p>
<!--  <ul>-->
<!--    <li><a href="http://focus-marches.e-bourgogne.fr/">http://focus-marches.e-bourgogne.fr/</a></li>-->
<!--    <li><a href="http://labo.e-bourgogne.fr/dataviz/donnees-essentielles/">http://labo.e-bourgogne.fr/dataviz/donnees-essentielles/</a></li>-->
<!--  </ul>-->

  <h2 class="title is-2">Crédits</h2>
  <p><?php echo gettext("Directeur de la publication&nbsp;: Gilles Delamarche");?><br>
      <b>Conception et réalisation de l'outil Focus marchés :</b> Arnia (Agence Régionale du Numérique et de l'intelligence artificielle) en Bourgogne - Franche-Comté<br>
      <?php echo gettext("Hébergement&nbsp;: OVH")?></p>
  <p>

  <h2 class="title is-2">Protection de la vie privée</h2>
<!--  <h3 class="title is-3">1. Quelles données sont collectées par ce site ?</h3>-->
<!--<p>Nous utilisons différents cookies pour améliorer l’interactivité du site et nos services.</p>-->
<!--<p><strong>Qu'est-ce qu'un "cookie" ?</strong></p>-->
<!--<p>Un "cookie" est un fichier de taille limitée, généralement constitué de lettres et de chiffres, envoyé par le serveur internet au fichier cookie du navigateur situé sur le disque dur de votre ordinateur.</p>-->
<!--<p><a href="https://www.cnil.fr/fr/cookies-les-outils-pour-les-maitriser">En savoir plus sur les cookies, leur fonctionnement et les moyens de s’y opposer</a></p>-->
<!--<p><strong>Cookies de mesure d’audience</strong></p>-->
<!--<p>En vue d’adapter le site aux demandes de ses visiteurs, nous mesurons des métriques comme le nombre de visites, le nombre de pages vues ainsi que l'activité des visiteurs sur le site et leur fréquence de retour.</p>-->
<!--<p><a href="https://matomo.org/">Matomo</a>, l'outil de statistiques utilisé par la Territoires Numériques, génère les cookies suivants :</p>-->
<!--<table style="width:500px;" cellspacing="1" cellpadding="1" border="1">-->
<!--  <tbody>-->
<!--    <tr>-->
<!--      <td>Nom du cookie</td>-->
<!--      <td>Finalité</td>-->
<!--<td>Durée de conservation</td>-->
<!--</tr>-->
<!--<tr>-->
<!--  <td>pk_session&nbsp;</td>-->
<!--<td>Statistiques Matomo</td>-->
<!--<td>1/2 h</td>-->
<!--</tr>-->
<!--<tr>-->
<!--  <td>_pk_id</td>-->
<!--<td>Statistiques Matomo</td>-->
<!--<td>13 mois</td>-->
<!--</tr>-->
<!--<tr>-->
<!--  <td>piwik_ignore</td>-->
<!--<td>Cookie permettant d’indiquer à Matomo que vous vous&nbsp; opposez au suivi statistique</td>-->
<!--<td>13 mois</td>-->
<!--</tr>-->
<!--</tbody>-->
<!--</table>-->
<!---->
<!--<p>Nous collectons également votre adresse IP, afin de déterminer la ville depuis laquelle vous vous connectez. Celle-ci est immédiatement anonymisée après utilisation. Territoires Numériques ne peut donc en aucun cas remonter par ce biais à une personne physique.</p>-->
<!--<p>Les données personnelles recueillies (identifiant du cookie) sont conservées par Territoires Numériques pour une durée de 13 mois. Elles ne sont pas cédées à des tiers ni utilisées à d'autres fins.</p>-->
<!--<p>Vous pouvez choisir de refuser les cookies et l’analyse statistique de vos données de navigation.</p>-->
<!--<p>Pour faire ce choix, utilisez la case à cocher ci-après :</p>-->
<!---->
<!--<p><iframe src="https://suivi-trafic.ternum-bfc.fr/index.php?module=CoreAdminHome&action=optOut&language=fr&amp;backgroundColor=#ffffdd&amp;fontColor=&amp;fontSize=1rem&amp;fontFamily=sans-serif" style="border: 1px solid red; padding: 20px; font-family: Arial, sans-serif; width: 100%;"></iframe></p>-->


<h3 class="title is-3">1. Qui est le Responsable des traitements, et le Délégué à la Protection des Données&nbsp;?<br></h3>
<p>
	<b>Responsable du traitement</b><br>
	<?php echo gettext('Le responsable du traitement des données à caractère personnel est : M Gilles DELAMARCHE. Il peut être contacté de la manière suivante :<br>Téléphone : 03 80 20 04 20<br>E-mail : <a href="mailto:contact@ternum-bfc.fr">contact@ternum-bfc.fr</a>')?>
</p>
<p>
	<b>Délégué à la Protection des Données</b><br>
	<?php echo gettext('La personne suivante a été nommée Délégué à la Protection des Données : M Victor VILA. Il peut être joint de la manière suivante :<br>Téléphone : 03 80 20 04 20<br>E-mail : <a href="mailto:dpo@ternum-bfc.fr">dpo@ternum-bfc.fr</a>')?>
</p>
<h3 class="title is-3">2. Quels sont vos droits concernant vos données à caractère personnel&nbsp;?</h3>
<ul>
  <li>
		Le <strong>droit d’obtenir des informations</strong> sur les données que nous détenons sur vous et les traitements mis en œuvre;
	</li>
  <li>
			Lorsque le traitement est fondé sur votre consentement, vous avez le <strong>droit de retirer ce consentement à tout moment</strong>. Cette action ne portera pas atteinte à la licéité du traitement fondé sur le consentement effectué avant le retrait de celui-ci ;
</li>
<li>
		Dans certaines circonstances, le <strong>droit de recevoir des données sous forme électronique</strong> et /ou de nous demander de transmettre ces informations à un tiers lorsque cela est techniquement possible (veuillez noter que ce droit n’est applicable qu’aux données que vous nous avez fournies) ;
	</li>
<li>
		Le <strong>droit de modifier ou corriger vos données</strong> (veuillez noter que des dispositions légales ou réglementaires ou des raisons légitimes peuvent limiter ce droit) ;
	</li>
<li>
		Le <strong>droit de demander la suppression de vos données</strong> dans certaines circonstances (veuillez noter que des dispositions légales ou réglementaires ou des raisons légitimes peuvent nous imposer de conserver ces données) ;
	</li>
<li>
		Le <strong>droit de demander de restreindre ou de vous opposer au traitement de vos données</strong>, dans certaines circonstances (veuillez noter que nous sommes susceptibles de continuer à traiter vos données personnelles si nous avons une base juridique pour le faire).
	</li>
<li>
		Vous disposez également du <strong>droit de déposer une réclamation auprès de la Commission Nationale de l’Informatique et des Libertés</strong>. Pour cela, merci d’adresser un courrier à l’adresse suivante : CNIL - 3 Place de Fontenoy - TSA 80715 - 75334 Paris - Cedex 07.
	</li>
</ul>
<h3 class="title is-3">3. Comment exercer vos droits ?</h3>
<p> <?php echo gettext('Pour faire valoir vos droits, vous pouvez contacter le Délégué à la Protection de Données du GIP Territoires Numériques M Victor VILA qui peut être joint par téléphone au 03 80 20 04 20 ou par e-mail à l\'adresse <a href="mailto:dpo@ternum-bfc.fr">dpo@ternum-bfc.fr</a> .')?>
</p>
<h3 class="title is-3">4. Quelles sont les obligations du Responsable des traitements&nbsp;?</h3>
<p>
	Le responsable du traitement s'engage à protéger les données à caractère personnel collectées, à ne pas les transmettre à des tiers autres que les sous-traitants de la plateforme sans que l'utilisateur n'en ait été informé et à respecter les finalités pour lesquelles ces données ont été collectées.
</p>
<p>
	De plus, le responsable du traitement des données s'engage à notifier l'utilisateur en cas de rectification ou de suppression des données, à moins que cela n'entraîne pour lui des formalités, coûts et démarches disproportionnés.
</p>
<p>
	Dans le cas où l'intégrité, la confidentialité ou la sécurité des données à caractère personnel de l'utilisateur est compromise et que la situation est susceptible d'engendrer un risque élevé pour ses droits et libertés, le responsable du traitement s'engage à informer l'utilisateur par tout moyen.
</p>


  <h2 class="title is-2">Décharge de responsabilité</h2>

  <?php echo gettext('<p>La présente décharge de responsabilité concerne le site Focus Marchés.</p>')?>
  <?php echo gettext('<p>Territoires Numériques n’est tenu que d’une simple obligation de moyens concernant les informations qu’elle met à disposition des personnes qui accèdent à son site Internet. Territoires Numériques ne peut encourir aucune responsabilité du fait d’erreurs, d’omissions, ou pour les résultats qui pourraient être obtenus par l’usage de ces informations. Notamment, l’usage de liens hypertextes peut conduire votre consultation de notre site vers d’autres serveurs pour prendre connaissance de l’information recherchée, serveurs sur lesquels Territoires Numériques n’a aucun contrôle.</p><p><strong>Ce site Internet a été créé afin de montrer le potentiel des données ouvertes, cépendant nous ne pouvons pas garantir ni l\'exactitude ni la complétude des données ouvertes sur lesquelles s`\'appuie le site</strong>.</p>')?>

  <h2 class="title is-2">Droits d’auteurs</h2>
    <?php echo gettext('<p>Ces données publiées sont publiées sous la <a href="pdf/ETALAB-Licence-Ouverte-v2.0.pdf">Licence Ouverte Version 2.0 - Etalab</a></p>')?>
    <?php echo gettext('<p>Le <a href="https://marches.ternum-bfc.fr/?page=entreprise.EntrepriseRechercherListeMarches&search">téléchargement des données</a> se fait librement sur notre salle de marchés.</p>')?>

<!--  <p>Crédit de la photo des halles de Dijon : <a href="https://commons.wikimedia.org/wiki/User:Fran%C3%A7ois_de_Dijon">François de Dijon</a></p>-->

  <h2 class="title is-2">Données utilisées</h2>
  <ul>
    <li><?php echo gettext('Données essentielles de la commande publique du profil d\'acheteur de Territoires Numériques.  Licence Ouverte / Open Licence version 2.0 .')?></li>
    <li><?php echo gettext('Codes CPV : simap.ted.europa.eu')?></li>
    <li><?php echo gettext('Populations légales 2015 : https://www.insee.fr/fr/statistiques/3292701')?></li>
    <li><?php echo gettext('API SIRENE. Licence Ouverte / Open Licence version 2.0 ')?></li>
    <li><?php echo gettext('API GEO : geo.api.gouv.fr. Licence Ouverte / Open Licence version 2.0 ')?></li>
    <li><?php echo gettext('Codes NAF (INSEE)')?></li>
    <li><?php echo gettext('Codes NAFA (INSEE)')?></li>
    <li><?php echo gettext('Catégories juridiques (INSEE)')?></li>
  </ul>
  
</div>
</div> <!-- ./ main -->

<?php include('js/common-js.php');?>
<?php include('inc/footer.php'); ?>

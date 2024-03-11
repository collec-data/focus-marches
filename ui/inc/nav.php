
</head>
<body id="<?php echo $page; ?>" class="site ">

  <div id="navWrap">
  <nav class="navbar is-light" role="navigation" aria-label="main navigation">
      <a class="navbar-item" href="index.php">
        <img src="./img/focus-marches_logo.png" height="42">
      </a>
      <a id="menu-burger" role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
      <a id="brand-opsn" class="navbar-item" style="visibility: hidden;" href=<?php echo gettext('URL_OPSN')?> target="_blank">
        <img src="./img/focus-marches_logo_brand.png" onload="this.parentElement.style.visibility='visible',this.parentElement.parentElement.style.visibility='visible'" height="42">
      </a>
      <div id="navbarMenu" class="navbar-menu">
      <div class="navbar-start">
        <a class="navbar-item" href="index.php"><i class="fas fa-home"></i>&nbsp;Accueil</a>
        <a class="navbar-item" href="acheteurs.php"><i class="fas fa-hotel"></i>&nbsp;Acheteurs</a>
        <a class="navbar-item" href="titulaires.php"><i class="fas fa-industry"></i>&nbsp;Fournisseurs</a>
        <!-- <a class="navbar-item" href="categories.php"><s>CPV</s></a> -->
        <a class="navbar-item" href="recherche.php"><i class="fas fa-search"></i>&nbsp;Recherche</a>
      </div>
  </nav>
</div><!-- ./navWrap -->

<h3>Distribution par catégorie principale d'achat</h3>
<p>Comparaison des trois catégories d'achats (zone colorée) par rapport au total (zone grise).</p>
<div class="columns">

  <div class="column tagsBlock has-text-centered">
    <h4>Services</h4>
    <div id="listServices"></div>
    <div class="tagLegend">
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Nombre :</span>
        <span class="tag has-services-bg"><?php echo nf($cats->nombreServices);?> marchés</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalNombre);?> marchés</span>
      </div>
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Montant :</span>
        <span class="tag has-services-bg"><?php echo nf($cats->montantServices);?> €</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalMontant);?> €</span>
      </div>
    </div>
  </div>

  <div class="column tagsBlock has-text-centered">
    <h4>Travaux</h4>
    <div id="listTravaux"></div>
    <div class="tagLegend">
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Travaux :</span>
        <span class="tag has-travaux-bg"><?php echo nf($cats->nombreTravaux);?> marchés</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalNombre);?> marchés</span>
      </div>
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Travaux :</span>
        <span class="tag has-travaux-bg"><?php echo nf($cats->montantTravaux);?> €</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalMontant);?> €</span>
      </div>
    </div>
  </div>

  <div class="column tagsBlock has-text-centered">
    <h4>Fournitures</h4>
    <div id="listFournitures"></div>
    <div class="tagLegend">
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Nombre :</span>
        <span class="tag has-fournitures-bg"><?php echo nf($cats->nombreFournitures);?> marchés</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalNombre);?> marchés</span>
      </div>
      <div class="tags has-addons">
        <span class="tag is-light is-upper">Montant :</span>
        <span class="tag has-fournitures-bg"><?php echo nf($cats->montantFournitures);?> €</span>
        <span class="tag has-total-bg"><?php echo nf($cats->totalMontant);?> €</span>
      </div>
    </div>
  </div>
</div>

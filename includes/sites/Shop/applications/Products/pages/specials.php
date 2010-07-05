<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Site\Shop\Specials;
  use osCommerce\OM\OSCOM;

  $Qspecials = Specials::getListing();
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div style="overflow: auto;">

<?php
  while ( $Qspecials->next() ) {
    echo '<span style="width: 33%; float: left; text-align: center;">';

    if ( !osc_empty($Qspecials->value('image')) ) {
      echo osc_link_object(OSCOM::getLink(null, null, $Qspecials->value('products_keyword')), $OSCOM_Image->show($Qspecials->value('image'), $Qspecials->value('products_name'))) . '<br />';
    }

    echo osc_link_object(OSCOM::getLink(null, null, $Qspecials->value('products_keyword')), $Qspecials->value('products_name')) . '<br />' .
         '<s>' . $OSCOM_Currencies->displayPrice($Qspecials->value('products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $OSCOM_Currencies->displayPrice($Qspecials->value('specials_new_products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</span>' .
         '</span>' . "\n";
  }
?>

</div>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qspecials->getBatchPageLinks(); ?></span>

  <?php echo $Qspecials->getBatchTotalPages(OSCOM::getDef('result_set_number_of_products')); ?>
</div>

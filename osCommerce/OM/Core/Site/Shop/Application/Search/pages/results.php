<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  $products_listing = $OSCOM_Search->execute();
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  require('includes/modules/product_listing.php');
?>

<div class="submitFormButtons">
  <?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?>
</div>

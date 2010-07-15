<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Site\Shop\Reviews;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  $Qreviews = Reviews::getListing();
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  while ( $Qreviews->next() ) {
?>

<div class="moduleBox">
  <div style="float: right; margin-top: 5px;"><?php echo sprintf(OSCOM::getDef('review_date_added'), DateTime::getLong($Qreviews->value('date_added'))); ?></div>

  <h6><?php echo osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&View=' . $Qreviews->valueInt('reviews_id') . '&' . $Qreviews->value('products_keyword')), $Qreviews->value('products_name')); ?> (<?php echo sprintf(OSCOM::getDef('reviewed_by'), $Qreviews->valueProtected('customers_name')); ?>)</h6>

  <div class="content">

<?php
    if ( !osc_empty($Qreviews->value('image')) ) {
      echo osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&View=' . $Qreviews->valueInt('reviews_id') . '&' . $Qreviews->value('products_keyword')), $OSCOM_Image->show($Qreviews->value('image'), $Qreviews->value('products_name'), 'style="float: left;"'));
    }
?>

    <p style="padding-left: 100px;"><?php echo wordwrap($Qreviews->valueProtected('reviews_text'), 60, '&shy;') . ((strlen($Qreviews->valueProtected('reviews_text')) >= 100) ? '..' : '') . '<br /><br /><i>' . sprintf(OSCOM::getDef('review_rating'), osc_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.png', sprintf(OSCOM::getDef('rating_of_5_stars'), $Qreviews->valueInt('reviews_rating'))), sprintf(OSCOM::getDef('rating_of_5_stars'), $Qreviews->valueInt('reviews_rating'))) . '</i>'; ?></p>

    <div style="clear: both;"></div>
  </div>
</div>

<?php
  }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qreviews->getBatchPageLinks('page', 'reviews'); ?></span>

  <?php echo $Qreviews->getBatchTotalPages(OSCOM::getDef('result_set_number_of_reviews')); ?>
</div>

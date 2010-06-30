<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;

// HPDL Should be moved to the customers class!
  $Qnewsletter = $OSCOM_Database->query('select customers_newsletter from :table_customers where customers_id = :customers_id');
  $Qnewsletter->bindInt(':customers_id', $OSCOM_Customer->getID());
  $Qnewsletter->execute();
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="account_newsletter" action="<?php echo OSCOM::getLink(null, null, 'Newsletters&Process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('newsletter_subscriptions_heading'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30"><?php echo osc_draw_checkbox_field('newsletter_general', '1', $Qnewsletter->value('customers_newsletter')); ?></td>
        <td><b><?php echo osc_draw_label(OSCOM::getDef('newsletter_general'), 'newsletter_general'); ?></b></td>
      </tr>
      <tr>
        <td width="30">&nbsp;</td>
        <td><?php echo OSCOM::getDef('newsletter_general_description'); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?>
</div>

</form>

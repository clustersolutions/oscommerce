<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<form name="account_notifications" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'notifications=save', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('newsletter_product_notifications'); ?></h6>

  <div class="content">
    <?php echo $osC_Language->get('newsletter_product_notifications_description'); ?>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('newsletter_product_notifications_global'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30"><?php echo osc_draw_checkbox_field('product_global', '1', $Qglobal->value('global_product_notifications')); ?></td>
        <td><b><?php echo osc_draw_label($osC_Language->get('newsletter_product_notifications_global'), 'product_global'); ?></b></td>
      </tr>
      <tr>
        <td width="30">&nbsp;</td>
        <td><?php echo $osC_Language->get('newsletter_product_notifications_global_description'); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  if ($Qglobal->valueInt('global_product_notifications') != '1') {
?>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('newsletter_product_notifications_products'); ?></h6>

  <div class="content">

<?php
    if ($osC_Template->hasCustomerProductNotifications($osC_Customer->getID())) {
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><?php echo $osC_Language->get('newsletter_product_notifications_products_description'); ?></td>
      </tr>

<?php
      $Qproducts = $osC_Template->getListing();

      while ($Qproducts->next()) {
        $counter++;
?>

      <tr>
        <td width="30"><?php echo osc_draw_checkbox_field('products[' . $counter . ']', $Qproducts->valueInt('products_id'), true); ?></td>
        <td><b><?php echo osc_draw_label($Qproducts->value('products_name'), 'products[' . $counter . ']'); ?></b></td>
      </tr>

<?php
      }
?>

    </table>

<?php
    } else {
      echo $osC_Language->get('newsletter_product_notifications_products_none');
    }
?>

  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?>
</div>

</form>

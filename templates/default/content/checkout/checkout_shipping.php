<?php
/*
  $Id:checkout_shipping.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_delivery.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<form name="checkout_address" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'shipping=process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('shipping_address_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><?php echo $osC_Language->get('choose_shipping_destination') . '<br /><br /><a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping_address', 'SSL') . '">' . tep_image_button('button_change_address.gif', $osC_Language->get('button_change_address')) . '</a>'; ?></td>
        <td valign="top" align="center"><?php echo '<b>' . $osC_Language->get('current_shipping_address_title') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
        <td valign="top"><?php echo tep_address_label($osC_Customer->getID(), $osC_ShoppingCart->getShippingAddress('id'), true, ' ', '<br />'); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  if ($osC_Shipping->hasQuotes()) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('shipping_method_title'); ?></div>

  <div class="content">

<?php
    if ($osC_Shipping->numberOfQuotes() > 1) {
?>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('please_select') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo $osC_Language->get('choose_shipping_method'); ?></p>

<?php
    } else {
?>

    <p style="margin-top: 0px;"><?php echo $osC_Language->get('only_one_shipping_method_available'); ?></p>

<?php
    }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    $radio_buttons = 0;
    foreach ($osC_Shipping->getQuotes() as $quotes) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="3"><b><?php echo $quotes['module']; ?></b>&nbsp;<?php if (isset($quotes['icon']) && tep_not_null($quotes['icon'])) { echo $quotes['icon']; } ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
      if (isset($quotes['error'])) {
?>
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="3"><?php echo $quotes['error']; ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
      } else {
        foreach ($quotes['methods'] as $methods) {
          if ($quotes['id'] . '_' . $methods['id'] == $osC_ShoppingCart->getShippingMethod('id')) {
            echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          } else {
            echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" width="75%"><?php echo $methods['title']; ?></td>
<?php
          if ( ($osC_Shipping->numberOfQuotes() > 1) || (sizeof($quotes['methods']) > 1) ) {
?>
            <td class="main"><?php echo $osC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']); ?></td>
            <td class="main" align="right"><?php echo osc_draw_radio_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id'], $osC_ShoppingCart->getShippingMethod('id')); ?></td>
<?php
          } else {
?>
            <td class="main" align="right" colspan="2"><?php echo $osC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']) . osc_draw_hidden_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id']); ?></td>
<?php
          }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
          $radio_buttons++;
        }
      }
?>
        </table></td>
      </tr>
<?php
    }
?>

    </table>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('add_comment_to_order_title'); ?></div>

  <div class="content">
    <?php echo osc_draw_textarea_field('comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : '')); ?>
  </div>
</div>

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?>
    </div>

    <?php echo '<b>' . $osC_Language->get('continue_checkout_procedure_title') . '</b><br />' . $osC_Language->get('continue_checkout_procedure_to_payment'); ?>
  </div>
</div>

</form>

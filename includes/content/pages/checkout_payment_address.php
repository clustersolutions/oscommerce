<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $addresses_count = tep_count_customer_address_book_entries();
?>

<script language="javascript"><!--
var selected;

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_address.address[0]) {
    document.checkout_address.address[buttonSelect].checked=true;
  } else {
    document.checkout_address.address.checked=true;
  }
}

function check_form_optional(form_name) {
  var form = form_name;

  var firstname = form.elements['firstname'].value;
  var lastname = form.elements['lastname'].value;
  var street_address = form.elements['street_address'].value;

  if (firstname == '' && lastname == '' && street_address == '') {
    return true;
  } else {
    return check_form(form_name);
  }
}
//--></script>

<?php require('includes/form_check.js.php'); ?>

<div class="pageHeading">
  <span class="pageHeadingImage"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_delivery.gif', HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></span>

  <h1><?php echo HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS; ?></h1>
</div>

<?php
  if ($messageStack->size('checkout_address') > 0) {
    echo $messageStack->output('checkout_address');
  }
?>

<form name="checkout_address" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'payment_address=process', 'SSL'); ?>" method="post" onSubmit="return check_form_optional(checkout_address);">

<?php
  if ($_GET['payment_address'] != 'process') {
    if ($osC_Customer->hasDefaultAddress() === true) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_PAYMENT_ADDRESS; ?></div>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo tep_address_label($osC_Customer->id, $osC_Session->value('billto'), true, ' ', '<br>'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . TITLE_PAYMENT_ADDRESS . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <?php echo TEXT_SELECTED_PAYMENT_DESTINATION; ?>
  </div>
</div>

<?php
    }

    if ($addresses_count > 1) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></div>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo TEXT_SELECT_OTHER_PAYMENT_DESTINATION; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      $radio_buttons = 0;

      $Qaddresses = $osC_Database->query('select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where customers_id = :customers_id');
      $Qaddresses->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddresses->bindInt(':customers_id', $osC_Customer->id);
      $Qaddresses->execute();

      while ($Qaddresses->next()) {
        $format_id = tep_get_address_format_id($Qaddresses->valueInt('country_id'));
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
       if ($Qaddresses->valueInt('address_book_id') == $osC_Session->value('billto')) {
          echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        } else {
          echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="2"><b><?php echo $Qaddresses->valueProtected('firstname') . ' ' . $Qaddresses->valueProtected('lastname'); ?></b></td>
            <td class="main" align="right"><?php echo osc_draw_radio_field('address', $Qaddresses->valueInt('address_book_id'), $osC_Session->value('billto')); ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td colspan="3"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo tep_address_format($format_id, $Qaddresses->toArray(), true, ' ', ', '); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
        </table></td>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
      </tr>
<?php
        $radio_buttons++;
      }
?>

    </table>
  </div>
</div>

<?php
    }
  }

  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS; ?></div>

  <div class="content">
    <?php echo TEXT_CREATE_NEW_PAYMENT_ADDRESS; ?>

    <div style="margin: 10px 30px 10px 30px;">
      <?php require('includes/modules/checkout_new_address.php'); ?>
    </div>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
    </div>

    <?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_PAYMENT; ?>
  </div>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
     <tr>
        <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
      </tr>
    </table></td>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
      </tr>
    </table></td>
    <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
    <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
  </tr>
</table>

</form>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if ($osC_Customer->isLoggedOn() == false) {
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_CHECKOUT_PAYMENT_ADDRESS);

  if (isset($_POST['action']) && ($_POST['action'] == 'submit')) {
// process a new billing address
    if (($osC_Customer->hasDefaultAddress() === false) || (tep_not_null($_POST['firstname']) && tep_not_null($_POST['lastname']) && tep_not_null($_POST['street_address'])) ) {
      if (ACCOUNT_GENDER > 0) {
        if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
          $messageStack->add('checkout_address', ENTRY_GENDER_ERROR);
        }
      }

      if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
        $messageStack->add('checkout_address', ENTRY_FIRST_NAME_ERROR);
      }

      if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
        $messageStack->add('checkout_address', ENTRY_LAST_NAME_ERROR);
      }

      if (ACCOUNT_COMPANY > 0) {
        if (!isset($_POST['company']) || (strlen(trim($_POST['company'])) < ACCOUNT_COMPANY)) {
          $messageStack->add('checkout_address', ENTRY_COMPANY_ERROR);
        }
      }

      if (!isset($_POST['street_address']) || (strlen(trim($_POST['street_address'])) < ACCOUNT_STREET_ADDRESS)) {
        $messageStack->add('checkout_address', ENTRY_STREET_ADDRESS_ERROR);
      }

      if (ACCOUNT_SUBURB > 0) {
        if (!isset($_POST['suburb']) || (strlen(trim($_POST['suburb'])) < ACCOUNT_SUBURB)) {
          $messageStack->add('checkout_address', ENTRY_SUBURB_ERROR);
        }
      }

      if (!isset($_POST['postcode']) || (strlen(trim($_POST['postcode'])) < ACCOUNT_POST_CODE)) {
        $messageStack->add('checkout_address', ENTRY_POST_CODE_ERROR);
      }

      if (!isset($_POST['city']) || (strlen(trim($_POST['city'])) < ACCOUNT_CITY)) {
        $messageStack->add('checkout_address', ENTRY_CITY_ERROR);
      }

      if (ACCOUNT_STATE > 0) {
        $zone_id = 0;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
        $Qcheck->bindRaw(':table_zones', TABLE_ZONES);
        $Qcheck->bindValue(':zone_country_id', $_POST['country']);
        $Qcheck->execute();

        $entry_state_has_zones = ($Qcheck->numberOfRows() > 0);

        $Qcheck->freeResult();

        if ($entry_state_has_zones === true) {
          $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
          $Qzone->bindRaw(':table_zones', TABLE_ZONES);
          $Qzone->bindValue(':zone_country_id', $_POST['country']);
          $Qzone->bindValue(':zone_code', trim($_POST['state']));
          $Qzone->execute();

          if ($Qzone->numberOfRows() === 1) {
            $zone_id = $Qzone->valueInt('zone_id');
          } else {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
            $Qzone->bindRaw(':table_zones', TABLE_ZONES);
            $Qzone->bindValue(':zone_country_id', $_POST['country']);
            $Qzone->bindValue(':zone_name', trim($_POST['state']) . '%');
            $Qzone->execute();

            if ($Qzone->numberOfRows() === 1) {
              $zone_id = $Qzone->valueInt('zone_id');
            } else {
              $messageStack->add('checkout_address', ENTRY_STATE_ERROR_SELECT);
            }
          }

          $Qzone->freeResult();
        } else {
          if (strlen(trim($_POST['state'])) < ACCOUNT_STATE) {
            $messageStack->add('checkout_address', ENTRY_STATE_ERROR);
          }
        }
      }

      if ( (is_numeric($_POST['country']) === false) || ($_POST['country'] < 1) ) {
        $messageStack->add('checkout_address', ENTRY_COUNTRY_ERROR);
      }

      if (ACCOUNT_TELEPHONE > 0) {
        if (!isset($_POST['telephone']) || (strlen(trim($_POST['telephone'])) < ACCOUNT_TELEPHONE)) {
          $messageStack->add('checkout_address', ENTRY_TELEPHONE_NUMBER_ERROR);
        }
      }

      if (ACCOUNT_FAX > 0) {
        if (!isset($_POST['fax']) || (strlen(trim($_POST['fax'])) < ACCOUNT_FAX)) {
          $messageStack->add('checkout_address', ENTRY_FAX_NUMBER_ERROR);
        }
      }

      if ($messageStack->size('checkout_address') === 0) {
        $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
        $Qab->bindRaw(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qab->bindInt(':customers_id', $osC_Customer->id);
        $Qab->bindValue(':entry_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
        $Qab->bindValue(':entry_company', ((ACCOUNT_COMPANY > -1) ? trim($_POST['company']) : ''));
        $Qab->bindValue(':entry_firstname', trim($_POST['firstname']));
        $Qab->bindValue(':entry_lastname', trim($_POST['lastname']));
        $Qab->bindValue(':entry_street_address', trim($_POST['street_address']));
        $Qab->bindValue(':entry_suburb', ((ACCOUNT_SUBURB > -1) ? trim($_POST['suburb']) : ''));
        $Qab->bindValue(':entry_postcode', trim($_POST['postcode']));
        $Qab->bindValue(':entry_city', trim($_POST['city']));
        $Qab->bindValue(':entry_state', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? '' : trim($_POST['state'])) : ''));
        $Qab->bindInt(':entry_country_id', $_POST['country']);
        $Qab->bindInt(':entry_zone_id', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? $zone_id : 0) : ''));
        $Qab->bindValue(':entry_telephone', ((ACCOUNT_TELEPHONE > -1) ? trim($_POST['telephone']) : ''));
        $Qab->bindValue(':entry_fax', ((ACCOUNT_FAX > -1) ? trim($_POST['fax']) : ''));
        $Qab->execute();

        if ($Qab->affectedRows() === 1) {
          $address_book_id = $osC_Database->nextID();

          if ($osC_Customer->hasDefaultAddress() === false) {
            $Qcustomer = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
            $Qcustomer->bindRaw(':table_customers', TABLE_CUSTOMERS);
            $Qcustomer->bindInt(':customers_default_address_id', $address_book_id);
            $Qcustomer->bindInt(':customers_id', $osC_Customer->id);
            $Qcustomer->execute();

            $osC_Customer->setCountryID($_POST['country']);
            $osC_Customer->setZoneID($zone_id);
            $osC_Customer->setDefaultAddressID($address_book_id);
          }

          $osC_Session->set('billto', $address_book_id);
          $osC_Session->remove('payment');

          tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
        } else {
          $messageStack->add('checkout_address', 'Error inserting into address book table.');
        }
      }
// process the selected billing destination
    } elseif (isset($_POST['address'])) {
      $reset_payment = false;
      if ($osC_Session->exists('billto')) {
        if ($osC_Session->value('billto') != $_POST['address']) {
          if ($osC_Session->exists('payment')) {
            $reset_payment = true;
          }
        }
      }

      $osC_Session->set('billto', $_POST['address']);

      $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
      $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qcheck->bindInt(':customers_id', $osC_Customer->id);
      $Qcheck->bindInt(':address_book_id', $osC_Session->value('billto'));
      $Qcheck->execute();

      if ($Qcheck->valueInt('total') == 1) {
        if ($reset_payment == true) {
          $osC_Session->remove('payment');
        }

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      } else {
        $osC_Session->remove('billto');
      }
// no addresses to select from - customer decided to keep the current assigned address
    } else {
      $osC_Session->set('billto', $osC_Customer->default_address_id);

      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
  }

// if no billing destination address was selected, use their own address as default
  if ($osC_Session->exists('billto') == false) {
    $osC_Session->set('billto', $osC_Customer->default_address_id);
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

  $addresses_count = tep_count_customer_address_book_entries();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
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

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
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
<?php require(DIR_WS_INCLUDES . 'form_check.js.php'); ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><?php echo tep_draw_form('checkout_address', tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'), 'post', 'onSubmit="return check_form_optional(checkout_address);"'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_payment.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><?php require(DIR_WS_MODULES . 'checkout_trail.php');?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('checkout_address') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('checkout_address'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }

  if (!isset($_POST['action'])) {
    if ($osC_Customer->hasDefaultAddress() === true) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_ADDRESS; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo TEXT_SELECTED_PAYMENT_DESTINATION; ?></td>
                <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main" align="center" valign="top"><?php echo '<b>' . TITLE_PAYMENT_ADDRESS . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" valign="top"><?php echo tep_address_label($osC_Customer->id, $osC_Session->value('billto'), true, ' ', '<br>'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    }

    if ($addresses_count > 1) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_ADDRESS_BOOK_ENTRIES; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo TEXT_SELECT_OTHER_PAYMENT_DESTINATION; ?></td>
                <td class="main" width="50%" valign="top" align="right"><?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
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
          echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        } else {
          echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
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
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    }
  }

  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_NEW_PAYMENT_ADDRESS; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="100%" valign="top"><?php echo TEXT_CREATE_NEW_PAYMENT_ADDRESS; ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td><?php require(DIR_WS_MODULES . 'checkout_new_address.php'); ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>
                <td class="main" align="right"><?php echo osc_draw_hidden_field('action', 'submit') . tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
            <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
          </tr>
        </table></td>
      </tr>
    </table></form></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

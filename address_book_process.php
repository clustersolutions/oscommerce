<?php
/*
  $Id: address_book_process.php,v 1.84 2004/05/24 10:53:21 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($osC_Customer->isLoggedOn() == false) {
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_ADDRESS_BOOK_PROCESS);

  if (isset($_GET['action']) && ($_GET['action'] == 'deleteconfirm') && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where address_book_id = '" . (int)$_GET['delete'] . "' and customers_id = '" . (int)$osC_Customer->id . "'");

    $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');

    tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
  }

// error checking when updating or adding an entry
  if (isset($_POST['action']) && (($_POST['action'] == 'process') || ($_POST['action'] == 'update'))) {
    if (ACCOUNT_GENDER > 0) {
      if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
        $messageStack->add('addressbook', ENTRY_GENDER_ERROR);
      }
    }

    if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
      $messageStack->add('addressbook', ENTRY_FIRST_NAME_ERROR);
    }

    if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
      $messageStack->add('addressbook', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_COMPANY > 0) {
      if (!isset($_POST['company']) || (strlen(trim($_POST['company'])) < ACCOUNT_COMPANY)) {
        $messageStack->add('addressbook', ENTRY_COMPANY_ERROR);
      }
    }

    if (!isset($_POST['street_address']) || (strlen(trim($_POST['street_address'])) < ACCOUNT_STREET_ADDRESS)) {
      $messageStack->add('addressbook', ENTRY_STREET_ADDRESS_ERROR);
    }

    if (ACCOUNT_SUBURB > 0) {
      if (!isset($_POST['suburb']) || (strlen(trim($_POST['suburb'])) < ACCOUNT_SUBURB)) {
        $messageStack->add('addressbook', ENTRY_SUBURB_ERROR);
      }
    }

    if (!isset($_POST['postcode']) || (strlen(trim($_POST['postcode'])) < ACCOUNT_POST_CODE)) {
      $messageStack->add('addressbook', ENTRY_POST_CODE_ERROR);
    }

    if (!isset($_POST['city']) || (strlen(trim($_POST['city'])) < ACCOUNT_CITY)) {
      $messageStack->add('addressbook', ENTRY_CITY_ERROR);
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
            $messageStack->add('addressbook', ENTRY_STATE_ERROR_SELECT);
          }
        }

        $Qzone->freeResult();
      } else {
        if (strlen(trim($_POST['state'])) < ACCOUNT_STATE) {
          $messageStack->add('addressbook', ENTRY_STATE_ERROR);
        }
      }
    }

    if ( (is_numeric($_POST['country']) === false) || ($_POST['country'] < 1) ) {
      $messageStack->add('addressbook', ENTRY_COUNTRY_ERROR);
    }

    if (ACCOUNT_TELEPHONE > 0) {
      if (!isset($_POST['telephone']) || (strlen(trim($_POST['telephone'])) < ACCOUNT_TELEPHONE)) {
        $messageStack->add('addressbook', ENTRY_TELEPHONE_NUMBER_ERROR);
      }
    }

    if (ACCOUNT_FAX > 0) {
      if (!isset($_POST['fax']) || (strlen(trim($_POST['fax'])) < ACCOUNT_FAX)) {
        $messageStack->add('addressbook', ENTRY_FAX_NUMBER_ERROR);
      }
    }

    if ($messageStack->size('addressbook') === 0) {
      if ($_POST['action'] == 'update') {
        $Qab = $osC_Database->query('update :table_address_book set customers_id = :customers_id, entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
        $Qab->bindInt(':address_book_id', $_GET['edit']);
        $Qab->bindInt(':customers_id', $osC_Customer->id);
      } else {
        $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
      }
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

      if ( ($osC_Customer->hasDefaultAddress() === false) || (isset($_POST['primary']) && ($_POST['primary'] == 'on')) ) {
        if ($_POST['action'] == 'update') {
          $default_address_id = $_GET['edit'];
        } else {
          $default_address_id = $osC_Database->nextID();
        }

        $Qupdate = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
        $Qupdate->bindRaw(':table_customers', TABLE_CUSTOMERS);
        $Qupdate->bindInt(':customers_default_address_id', $default_address_id);
        $Qupdate->bindInt(':customers_id', $osC_Customer->id);
        $Qupdate->execute();

        if ($Qupdate->affectedRows() === 1) {
          $osC_Customer->setCountryID($_POST['country']);
          $osC_Customer->setZoneID(($zone_id > 0) ? (int)$zone_id : '0');
          $osC_Customer->setDefaultAddressID($default_address_id);
        }
      }

      $messageStack->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');

      tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
  }

  if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $entry_query = tep_db_query("select entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_zone_id, entry_country_id, entry_telephone, entry_fax from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$osC_Customer->id . "' and address_book_id = '" . (int)$_GET['edit'] . "'");

    if (!tep_db_num_rows($entry_query)) {
      $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

      tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }

    $entry = tep_db_fetch_array($entry_query);
  } elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($_GET['delete'] == $osC_Customer->default_address_id) {
      $messageStack->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');

      tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    } else {
      $check_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where address_book_id = '" . (int)$_GET['delete'] . "' and customers_id = '" . (int)$osC_Customer->id . "'");
      $check = tep_db_fetch_array($check_query);

      if ($check['total'] < 1) {
        $messageStack->add_session('addressbook', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY);

        tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
      }
    }
  } else {
    $entry = array();
  }

  if (!isset($_GET['delete']) && !isset($_GET['edit'])) {
    if (tep_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES) {
      $messageStack->add_session('addressbook', ERROR_ADDRESS_BOOK_FULL);

      tep_redirect(tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

  if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $breadcrumb->add(NAVBAR_TITLE_MODIFY_ENTRY, tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'edit=' . $_GET['edit'], 'SSL'));
  } elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $breadcrumb->add(NAVBAR_TITLE_DELETE_ENTRY, tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'], 'SSL'));
  } else {
    $breadcrumb->add(NAVBAR_TITLE_ADD_ENTRY, tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, '', 'SSL'));
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<?php
  if (!isset($_GET['delete'])) {
    include('includes/form_check.js.php');
  }
?>
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
    <td width="100%" valign="top"><?php if (!isset($_GET['delete'])) echo tep_draw_form('addressbook', tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, (isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'SSL'), 'post', 'onSubmit="return check_form(addressbook);"'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php if (isset($_GET['edit'])) { echo HEADING_TITLE_MODIFY_ENTRY; } elseif (isset($_GET['delete'])) { echo HEADING_TITLE_DELETE_ENTRY; } else { echo HEADING_TITLE_ADD_ENTRY; } ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_address_book.gif', (isset($_GET['edit']) ? HEADING_TITLE_MODIFY_ENTRY : HEADING_TITLE_ADD_ENTRY), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('addressbook') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('addressbook'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }

  if (isset($_GET['delete'])) {
?>
      <tr>
        <td class="main"><b><?php echo DELETE_ADDRESS_TITLE; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo DELETE_ADDRESS_DESCRIPTION; ?></td>
                <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main" align="center" valign="top"><b><?php echo SELECTED_ADDRESS; ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" valign="top"><?php echo tep_address_label($osC_Customer->id, $_GET['delete'], true, ' ', '<br>'); ?></td>
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
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . $_GET['delete'] . '&action=deleteconfirm', 'SSL') . '">' . tep_image_button('button_delete.gif', IMAGE_BUTTON_DELETE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo NEW_ADDRESS_TITLE; ?></b></td>
                <td class="inputRequirement" align="right"><?php echo FORM_REQUIRED_INFORMATION; ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><?php include(DIR_WS_MODULES . 'address_book_details.php'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right"><?php echo osc_draw_hidden_field('action', 'update') . osc_draw_hidden_field('edit', $_GET['edit']) . tep_image_submit('button_update.gif', IMAGE_BUTTON_UPDATE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
    } else {
      if (sizeof($navigation->snapshot) > 0) {
        $back_link = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array($osC_Session->name)), $navigation->snapshot['mode']);
      } else {
        $back_link = tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
      }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php echo '<a href="' . $back_link . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right"><?php echo osc_draw_hidden_field('action', 'process') . tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
    }
  }
?>
    </table><?php if (!isset($_GET['delete'])) echo '</form>'; ?></td>
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

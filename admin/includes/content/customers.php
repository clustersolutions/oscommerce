<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Customers extends osC_Template {

/* Private variables */

    var $_module = 'customers',
        $_page_title,
        $_page_contents = 'customers.php';

/* Class constructor */

    function osC_Content_Customers() {
      include('external/adodb/adodb-time.inc.php');

      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'cEdit':
          case 'cNew':
            $this->_page_contents = 'customers_edit.php';
            break;

          case 'save':
            $this->_page_contents = 'customers_edit.php';

            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack, $entry_state_has_zones;

      $error = false;

      if (ACCOUNT_GENDER > 0) {
        if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
          $osC_MessageStack->add($this->_module, ENTRY_GENDER_ERROR, 'error');
          $error = true;
        }
      }

      if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
        $osC_MessageStack->add($this->_module, ENTRY_FIRST_NAME_ERROR, 'error');
        $error = true;
      }

      if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
        $osC_MessageStack->add($this->_module, ENTRY_LAST_NAME_ERROR, 'error');
        $error = true;
      }

      if (ACCOUNT_DATE_OF_BIRTH == '1') {
        if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
          $dob = adodb_mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
        } else {
          $osC_MessageStack->add($this->_module, ENTRY_DATE_OF_BIRTH_ERROR, 'error');
          $error = true;
        }
      }

      if (!isset($_POST['email_address']) || (strlen(trim($_POST['email_address'])) < ACCOUNT_EMAIL_ADDRESS)) {
        $osC_MessageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_ERROR, 'error');
        $error = true;
      } elseif (osc_validate_email_address($_POST['email_address']) == false) {
        $osC_MessageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_CHECK_ERROR, 'error');
        $error = true;
      } else {
        $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address');
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcheck->appendQuery('and customers_id != :customers_id');
          $Qcheck->bindInt(':customers_id', $_GET['cID']);
        }
        $Qcheck->appendQuery('limit 1');
        $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
        $Qcheck->execute();

        if ($Qcheck->numberOfRows() > 0) {
          $osC_MessageStack->add($this->_module, ENTRY_EMAIL_ADDRESS_ERROR_EXISTS, 'error');
          $error = true;
        }

        $Qcheck->freeResult();
      }

      if ( (!isset($_GET['cID']) || (isset($_POST['password']) && !empty($_POST['password']))) && (strlen(trim($_POST['password'])) < ACCOUNT_PASSWORD)) {
        $osC_MessageStack->add($this->_module, ENTRY_PASSWORD_ERROR, 'error');
        $error = true;
      } elseif (isset($_POST['confirmation']) && !empty($_POST['confirmation']) && (trim($_POST['password']) != trim($_POST['confirmation']))) {
        $osC_MessageStack->add($this->_module, ENTRY_PASSWORD_ERROR_NOT_MATCHING, 'error');
        $error = true;
      }

      if (!isset($_GET['cID']) || (strlen(trim($_POST['ab_street_address'])) > 0)) {
        if (ACCOUNT_GENDER > 0) {
          if (!isset($_POST['ab_gender']) || (($_POST['ab_gender'] != 'm') && ($_POST['ab_gender'] != 'f'))) {
            $osC_MessageStack->add($this->_module, ENTRY_GENDER_ERROR, 'error');
            $error = true;
          }
        }

        if (!isset($_POST['ab_firstname']) || (strlen(trim($_POST['ab_firstname'])) < ACCOUNT_FIRST_NAME)) {
          $osC_MessageStack->add($this->_module, ENTRY_FIRST_NAME_ERROR, 'error');
          $error = true;
        }

        if (!isset($_POST['ab_lastname']) || (strlen(trim($_POST['ab_lastname'])) < ACCOUNT_LAST_NAME)) {
          $osC_MessageStack->add($this->_module, ENTRY_LAST_NAME_ERROR, 'error');
          $error = true;
        }

        if (ACCOUNT_COMPANY > 0) {
          if (!isset($_POST['ab_company']) || (strlen(trim($_POST['ab_company'])) < ACCOUNT_COMPANY)) {
            $osC_MessageStack->add($this->_module, ENTRY_COMPANY_ERROR, 'error');
            $error = true;
          }
        }

        if (!isset($_POST['ab_street_address']) || (strlen(trim($_POST['ab_street_address'])) < ACCOUNT_STREET_ADDRESS)) {
          $osC_MessageStack->add($this->_module, ENTRY_STREET_ADDRESS_ERROR, 'error');
          $error = true;
        }

        if (ACCOUNT_SUBURB > 0) {
          if (!isset($_POST['ab_suburb']) || (strlen(trim($_POST['ab_suburb'])) < ACCOUNT_SUBURB)) {
            $osC_MessageStack->add($this->_module, ENTRY_SUBURB_ERROR, 'error');
            $error = true;
          }
        }

        if (ACCOUNT_POST_CODE > 0) {
          if (!isset($_POST['ab_postcode']) || (strlen(trim($_POST['ab_postcode'])) < ACCOUNT_POST_CODE)) {
            $osC_MessageStack->add($this->_module, ENTRY_POST_CODE_ERROR, 'error');
            $error = true;
          }
        }

        if (!isset($_POST['ab_city']) || (strlen(trim($_POST['ab_city'])) < ACCOUNT_CITY)) {
          $osC_MessageStack->add($this->_module, ENTRY_CITY_ERROR, 'error');
          $error = true;
        }

        if (ACCOUNT_STATE > 0) {
          $zone_id = 0;

          $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
          $Qcheck->bindTable(':table_zones', TABLE_ZONES);
          $Qcheck->bindInt(':zone_country_id', $_POST['ab_country']);
          $Qcheck->execute();

          $entry_state_has_zones = ($Qcheck->numberOfRows() > 0);

          $Qcheck->freeResult();

          if ($entry_state_has_zones === true) {
            $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code like :zone_code');
            $Qzone->bindTable(':table_zones', TABLE_ZONES);
            $Qzone->bindInt(':zone_country_id', $_POST['ab_country']);
            $Qzone->bindValue(':zone_code', $_POST['ab_state']);
            $Qzone->execute();

            if ($Qzone->numberOfRows() === 1) {
              $zone_id = $Qzone->valueInt('zone_id');
            } else {
              $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
              $Qzone->bindTable(':table_zones', TABLE_ZONES);
              $Qzone->bindInt(':zone_country_id', $_POST['ab_country']);
              $Qzone->bindValue(':zone_name', $_POST['ab_state'] . '%');
              $Qzone->execute();

              if ($Qzone->numberOfRows() === 1) {
                $zone_id = $Qzone->valueInt('zone_id');
              } else {
                $osC_MessageStack->add($this->_module, ENTRY_STATE_ERROR_SELECT, 'error');
                $error = true;
              }
            }

            $Qzone->freeResult();
          } else {
            if (strlen(trim($_POST['ab_state'])) < ACCOUNT_STATE) {
              $osC_MessageStack->add($this->_module, ENTRY_STATE_ERROR, 'error');
              $error = true;
            }
          }
        }

        if ( (is_numeric($_POST['ab_country']) === false) || ($_POST['ab_country'] < 1) ) {
          $osC_MessageStack->add($this->_module, ENTRY_COUNTRY_ERROR, 'error');
          $error = true;
        }

        if (ACCOUNT_TELEPHONE > 0) {
          if (!isset($_POST['ab_telephone']) || (strlen(trim($_POST['ab_telephone'])) < ACCOUNT_TELEPHONE)) {
            $osC_MessageStack->add($this->_module, ENTRY_TELEPHONE_NUMBER_ERROR, 'error');
            $error = true;
          }
        }

        if (ACCOUNT_FAX > 0) {
          if (!isset($_POST['ab_fax']) || (strlen(trim($_POST['ab_fax'])) < ACCOUNT_FAX)) {
            $osC_MessageStack->add($this->_module, ENTRY_FAX_NUMBER_ERROR, 'error');
            $error = true;
          }
        }
      }

      if ($error === false) {
        $modified = false;

        $osC_Database->startTransaction();

        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcustomer = $osC_Database->query('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob, customers_newsletter = :customers_newsletter, customers_status = :customers_status where customers_id = :customers_id');
          $Qcustomer->bindInt(':customers_id', $_GET['cID']);
        } else {
          $Qcustomer = $osC_Database->query('insert into :table_customers (customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_dob, customers_newsletter, customers_status, number_of_logons, date_account_created) values (:customers_gender, :customers_firstname, :customers_lastname, :customers_email_address, :customers_dob, :customers_newsletter, :customers_status, :number_of_logons, :date_account_created)');
          $Qcustomer->bindInt(':number_of_logons', 0);
          $Qcustomer->bindRaw(':date_account_created', 'now()');
        }
        $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
        $Qcustomer->bindValue(':customers_firstname', $_POST['firstname']);
        $Qcustomer->bindValue(':customers_lastname', $_POST['lastname']);
        $Qcustomer->bindValue(':customers_email_address', $_POST['email_address']);
        $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH == '1') ? adodb_date('Ymd', $dob) : ''));
        $Qcustomer->bindInt(':customers_newsletter', (isset($_POST['newsletter']) && ($_POST['newsletter'] == 'on') ? '1' : '0'));
        $Qcustomer->bindInt(':customers_status', (isset($_POST['status']) && ($_POST['status'] == 'on') ? '1' : '0'));
        $Qcustomer->execute();

        if ($osC_Database->isError() === false) {
          if ($Qcustomer->affectedRows()) {
            $modified = true;

            if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
              $customer_id = $_GET['cID'];

              $Qupdate = $osC_Database->query('update :table_customers set date_account_last_modified = :date_account_last_modified where customers_id = :customers_id');
              $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
              $Qupdate->bindRaw(':date_account_last_modified', 'now()');
              $Qupdate->bindInt(':customers_id', $customer_id);
              $Qupdate->execute();

              if ($osC_Database->isError()) {
                $error = true;
              }
            } else {
              $customer_id = $osC_Database->nextID();
            }
          }
        } else {
          $error = true;
        }

        if ($error === false) {
          if (isset($_POST['confirmation']) && !empty($_POST['confirmation']) && (trim($_POST['password']) == trim($_POST['confirmation']))) {
            $Qpassword = $osC_Database->query('update :table_customers set customers_password = :customers_password where customers_id = :customers_id');
            $Qpassword->bindTable(':table_customers', TABLE_CUSTOMERS);
            $Qpassword->bindValue(':customers_password', osc_encrypt_string(trim($_POST['password'])));
            $Qpassword->bindInt(':customers_id', $customer_id);
            $Qpassword->execute();

            if ($osC_Database->isError() === false) {
              if ($Qpassword->affectedRows()) {
                $modified = true;
              }
            } else {
              $error = true;
            }
          }
        }

        if ($error === false) {
          if (strlen(trim($_POST['ab_street_address'])) > 0) {
            $Qcustomer = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, customers_email_address, customers_default_address_id from :table_customers where customers_id = :customers_id');
            $Qcustomer->bindTable(':table_customers', TABLE_CUSTOMERS);
            $Qcustomer->bindInt(':customers_id', $customer_id);
            $Qcustomer->execute();

            if ($Qcustomer->valueInt('customers_default_address_id') > 0) {
              $Qab = $osC_Database->query('update :table_address_book set customers_id = :customers_id, entry_gender = :entry_gender, entry_company = :entry_company, entry_firstname = :entry_firstname, entry_lastname = :entry_lastname, entry_street_address = :entry_street_address, entry_suburb = :entry_suburb, entry_postcode = :entry_postcode, entry_city = :entry_city, entry_state = :entry_state, entry_country_id = :entry_country_id, entry_zone_id = :entry_zone_id, entry_telephone = :entry_telephone, entry_fax = :entry_fax where address_book_id = :address_book_id and customers_id = :customers_id');
              $Qab->bindInt(':address_book_id', $Qcustomer->valueInt('customers_default_address_id'));
              $Qab->bindInt(':customers_id', $customer_id);
            } else {
              $Qab = $osC_Database->query('insert into :table_address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id, entry_telephone, entry_fax) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id, :entry_telephone, :entry_fax)');
            }
            $Qab->bindInt(':customers_id', $customer_id);
            $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
            $Qab->bindValue(':entry_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['ab_gender']) && (($_POST['ab_gender'] == 'm') || ($_POST['ab_gender'] == 'f'))) ? $_POST['ab_gender'] : ''));
            $Qab->bindValue(':entry_company', ((ACCOUNT_COMPANY > -1) ? $_POST['ab_company'] : ''));
            $Qab->bindValue(':entry_firstname', $_POST['ab_firstname']);
            $Qab->bindValue(':entry_lastname', $_POST['ab_lastname']);
            $Qab->bindValue(':entry_street_address', $_POST['ab_street_address']);
            $Qab->bindValue(':entry_suburb', ((ACCOUNT_SUBURB > -1) ? $_POST['ab_suburb'] : ''));
            $Qab->bindValue(':entry_postcode', ((ACCOUNT_POST_CODE > -1) ? $_POST['ab_postcode'] : ''));
            $Qab->bindValue(':entry_city', $_POST['ab_city']);
            $Qab->bindValue(':entry_state', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? '' : $_POST['ab_state']) : ''));
            $Qab->bindInt(':entry_country_id', $_POST['ab_country']);
            $Qab->bindInt(':entry_zone_id', ((ACCOUNT_STATE > -1) ? (($zone_id > 0) ? $zone_id : 0) : ''));
            $Qab->bindValue(':entry_telephone', ((ACCOUNT_TELEPHONE > -1) ? $_POST['ab_telephone'] : ''));
            $Qab->bindValue(':entry_fax', ((ACCOUNT_FAX > -1) ? $_POST['ab_fax'] : ''));
            $Qab->execute();

            if ($osC_Database->isError() === false) {
              if ($Qab->affectedRows()) {
                $modified = true;

                if ($Qcustomer->valueInt('customers_default_address_id') < 1) {
                  $address_book_id = $osC_Database->nextID();

                  $Qupdate = $osC_Database->query('update :table_customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
                  $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
                  $Qupdate->bindInt(':customers_default_address_id', $address_book_id);
                  $Qupdate->bindInt(':customers_id', $customer_id);
                  $Qupdate->execute();

                  if ($osC_Database->isError()) {
                    $error = true;
                  }
                }
              }
            } else {
              $error = true;
            }
          }
        }

        if ($error === false) {
          if ($modified === true) {
            $osC_Database->commitTransaction();

            if (!isset($_GET['cID'])) {
              $full_name = $Qcustomer->value('customers_firstname') . ' ' . $Qcustomer->value('customers_lastname');

              if (ACCOUNT_GENDER > -1) {
                if ($Qcustomer->value('customers_gender') == 'm') {
                  $email_text = sprintf(EMAIL_GREET_MR, $Qcustomer->value('customers_lastname'));
                } else {
                  $email_text = sprintf(EMAIL_GREET_MS, $Qcustomer->value('customers_lastname'));
                }
              } else {
                $email_text = sprintf(EMAIL_GREET_NONE, $full_name);
              }

              $email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT; // . sprintf(EMAIL_PASSWORD, $customers_password);
              osc_email($full_name, $Qcustomer->value('customers_email_address'), EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            }

            $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $customer_id));
      }
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
        $error = false;

        $osC_Database->startTransaction();

        if (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on')) {
          $Qreviews = $osC_Database->query('delete from :table_reviews where customers_id = :customers_id');
          $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qreviews->bindInt(':customers_id', $_GET['cID']);
          $Qreviews->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        } else {
          $Qcheck = $osC_Database->query('select reviews_id from :table_reviews where customers_id = :customers_id limit 1');
          $Qcheck->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qcheck->bindInt(':customers_id', $_GET['cID']);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() > 0) {
            $Qreviews = $osC_Database->query('update :table_reviews set customers_id = null where customers_id = :customers_id');
            $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
            $Qreviews->bindInt(':customers_id', $_GET['cID']);
            $Qreviews->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }
        }

        if ($error === false) {
          $Qab = $osC_Database->query('delete from :table_address_book where customers_id = :customers_id');
          $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
          $Qab->bindInt(':customers_id', $_GET['cID']);
          $Qab->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qcustomers = $osC_Database->query('delete from :table_customers where customers_id = :customers_id');
          $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qcustomers->bindInt(':customers_id', $_GET['cID']);
          $Qcustomers->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qcb = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id');
          $Qcb->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qcb->bindInt(':customers_id', $_GET['cID']);
          $Qcb->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qpn = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id');
          $Qpn->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
          $Qpn->bindInt(':customers_id', $_GET['cID']);
          $Qpn->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qwho = $osC_Database->query('delete from :table_whos_online where customer_id = :customer_id');
          $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
          $Qwho->bindInt(':customer_id', $_GET['cID']);
          $Qwho->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }
      }

      osc_redirect(osc_href_link(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page']));
    }
  }
?>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/customers/classes/customers.php');

  class osC_Application_Customers extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'customers',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Database, $osC_Language, $osC_MessageStack, $entry_state_has_zones;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
        $this->_page_title .= ': ' . osc_output_string_protected(osC_Customers_Admin::getData($_GET['cID'], 'customers_full_name'));
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('gender' => (isset($_POST['gender']) ? $_POST['gender'] : ''),
                            'firstname' => $_POST['firstname'],
                            'lastname' => $_POST['lastname'],
                            'dob_day' => (isset($_POST['dob_days']) ? $_POST['dob_days'] : ''),
                            'dob_month' => (isset($_POST['dob_months']) ? $_POST['dob_months'] : ''),
                            'dob_year' => (isset($_POST['dob_years']) ? $_POST['dob_years'] : ''),
                            'email_address' => $_POST['email_address'],
                            'password' => $_POST['password'],
                            'newsletter' => (isset($_POST['newsletter']) && ($_POST['newsletter'] == 'on') ? '1' : '0'),
                            'status' => (isset($_POST['status']) && ($_POST['status'] == 'on') ? '1' : '0'));

              $error = false;

              if ( ACCOUNT_GENDER > 0 ) {
                if ( ($data['gender'] != 'm') && ($data['gender'] != 'f') ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_gender'), 'error');
                  $error = true;
                }
              }

              if ( strlen(trim($data['firstname'])) < ACCOUNT_FIRST_NAME ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_first_name'), ACCOUNT_FIRST_NAME), 'error');
                $error = true;
              }

              if ( strlen(trim($data['lastname'])) < ACCOUNT_LAST_NAME ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_last_name'), ACCOUNT_LAST_NAME), 'error');
                $error = true;
              }

              if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
                if ( !checkdate($data['dob_month'], $data['dob_day'], $data['dob_year']) ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_date_of_birth'), 'error');
                  $error = true;
                }
              }

              if ( strlen(trim($data['email_address'])) < ACCOUNT_EMAIL_ADDRESS ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_email_address'), ACCOUNT_EMAIL_ADDRESS), 'error');
                $error = true;
              } elseif ( !osc_validate_email_address($data['email_address']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_email_address_invalid'), 'error');
                $error = true;
              } else {
                $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address');

                if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
                  $Qcheck->appendQuery('and customers_id != :customers_id');
                  $Qcheck->bindInt(':customers_id', $_GET['cID']);
                }

                $Qcheck->appendQuery('limit 1');
                $Qcheck->bindTable(':table_customers', TABLE_CUSTOMERS);
                $Qcheck->bindValue(':customers_email_address', $data['email_address']);
                $Qcheck->execute();

                if ( $Qcheck->numberOfRows() > 0 ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_email_address_exists'), 'error');
                  $error = true;
                }

                $Qcheck->freeResult();
              }

              if ( ( !isset($_GET['cID']) || !empty($data['password']) ) && (strlen(trim($data['password'])) < ACCOUNT_PASSWORD) ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_password'), ACCOUNT_PASSWORD), 'error');
                $error = true;
              } elseif ( !empty($_POST['confirmation']) && (trim($data['password']) != trim($_POST['confirmation'])) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_password_confirmation_invalid'), 'error');
                $error = true;
              }

              if ( $error === false ) {
                if ( osC_Customers_Admin::save((isset($_GET['cID']) && is_numeric($_GET['cID']) ? $_GET['cID'] : null), $data) ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&search=' . $_GET['search'] . '&page=' . $_GET['page']));
              }
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Customers_Admin::delete($_GET['cID'], (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on') ? true : false)) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&search=' . $_GET['search'] . '&page=' . $_GET['page']));
            }

            break;

          case 'saveAddress':
            if ( isset($_GET['abID']) && is_numeric($_GET['abID']) ) {
              $this->_page_contents = 'address_book_edit.php';
            } else {
              $this->_page_contents = 'address_book_new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('customer_id' => $_GET['cID'],
                            'gender' => (isset($_POST['ab_gender']) ? $_POST['ab_gender'] : ''),
                            'firstname' => $_POST['ab_firstname'],
                            'lastname' => $_POST['ab_lastname'],
                            'company' => (isset($_POST['ab_company']) ? $_POST['ab_company'] : ''),
                            'street_address' => $_POST['ab_street_address'],
                            'suburb' => (isset($_POST['ab_suburb']) ? $_POST['ab_suburb'] : ''),
                            'postcode' => (isset($_POST['ab_postcode']) ? $_POST['ab_postcode'] : ''),
                            'city' => $_POST['ab_city'],
                            'state' => (isset($_POST['ab_state']) ? $_POST['ab_state'] : ''),
                            'zone_id' => '0', // set below
                            'country_id' => $_POST['ab_country'],
                            'telephone' => (isset($_POST['ab_telephone']) ? $_POST['ab_telephone'] : ''),
                            'fax' => (isset($_POST['ab_fax']) ? $_POST['ab_fax'] : ''),
                            'primary' => (isset($_POST['ab_primary']) && ($_POST['ab_primary'] == 'on') ? true : false));

              $error = false;

              if ( ACCOUNT_GENDER > 0 ) {
                if ( ($data['gender'] != 'm') && ($data['gender'] != 'f') ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_gender'), 'error');
                  $error = true;
                }
              }

              if ( strlen(trim($data['firstname'])) < ACCOUNT_FIRST_NAME ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_first_name'), ACCOUNT_FIRST_NAME), 'error');
                $error = true;
              }

              if ( strlen(trim($data['lastname'])) < ACCOUNT_LAST_NAME ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_last_name'), ACCOUNT_LAST_NAME), 'error');
                $error = true;
              }

              if ( ACCOUNT_COMPANY > 0 ) {
                if ( strlen(trim($data['company'])) < ACCOUNT_COMPANY ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_company'), ACCOUNT_COMPANY), 'error');
                  $error = true;
                }
              }

              if ( strlen(trim($data['street_address'])) < ACCOUNT_STREET_ADDRESS ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_street_address'), ACCOUNT_STREET_ADDRESS), 'error');
                $error = true;
              }

              if ( ACCOUNT_SUBURB > 0 ) {
                if ( strlen(trim($data['suburb'])) < ACCOUNT_SUBURB ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_suburb'), ACCOUNT_SUBURB), 'error');
                  $error = true;
                }
              }

              if ( ACCOUNT_POST_CODE > 0 ) {
                if ( strlen(trim($data['postcode'])) < ACCOUNT_POST_CODE ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('entry_post_code'), ACCOUNT_POST_CODE), 'error');
                  $error = true;
                }
              }

              if ( strlen(trim($data['city'])) < ACCOUNT_CITY ) {
                $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_city'), ACCOUNT_CITY), 'error');
                $error = true;
              }

              if ( ACCOUNT_STATE > 0 ) {
                $Qcheck = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id limit 1');
                $Qcheck->bindTable(':table_zones', TABLE_ZONES);
                $Qcheck->bindInt(':zone_country_id', $data['country_id']);
                $Qcheck->execute();

                $entry_state_has_zones = ( $Qcheck->numberOfRows() > 0 );

                $Qcheck->freeResult();

                if ( $entry_state_has_zones === true ) {
                  $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_code = :zone_code');
                  $Qzone->bindTable(':table_zones', TABLE_ZONES);
                  $Qzone->bindInt(':zone_country_id', $data['country_id']);
                  $Qzone->bindValue(':zone_code', strtoupper($data['state']));
                  $Qzone->execute();

                  if ( $Qzone->numberOfRows() === 1 ) {
                    $data['zone_id'] = $Qzone->valueInt('zone_id');
                  } else {
                    $Qzone = $osC_Database->query('select zone_id from :table_zones where zone_country_id = :zone_country_id and zone_name like :zone_name');
                    $Qzone->bindTable(':table_zones', TABLE_ZONES);
                    $Qzone->bindInt(':zone_country_id', $data['country_id']);
                    $Qzone->bindValue(':zone_name', $data['state'] . '%');
                    $Qzone->execute();

                    if ( $Qzone->numberOfRows() === 1 ) {
                      $data['zone_id'] = $Qzone->valueInt('zone_id');
                    } else {
                      $osC_MessageStack->add($this->_module, $osC_Language->get('ms_warning_state_select_from_list'), 'warning');
                      $error = true;
                    }
                  }

                  $Qzone->freeResult();
                } else {
                  if ( strlen(trim($data['state'])) < ACCOUNT_STATE ) {
                    $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_state'), ACCOUNT_STATE), 'error');
                    $error = true;
                  }
                }
              }

              if ( !is_numeric($data['country_id']) || ($data['country_id'] < 1) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_country'), 'error');
                $error = true;
              }

              if ( ACCOUNT_TELEPHONE > 0 ) {
                if ( strlen(trim($data['telephone'])) < ACCOUNT_TELEPHONE ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_telephone_number'), ACCOUNT_TELEPHONE), 'error');
                  $error = true;
                }
              }

              if ( ACCOUNT_FAX > 0 ) {
                if ( strlen(trim($data['fax'])) < ACCOUNT_FAX ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_fax_number'), ACCOUNT_FAX), 'error');
                  $error = true;
                }
              }

              if ( $error === false ) {
                if ( osC_Customers_Admin::saveAddress((isset($_GET['abID']) && is_numeric($_GET['abID']) ? $_GET['abID'] : null), $data) ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save&tabIndex=tabAddressBook'));
              }
            }

            break;

          case 'deleteAddress':
            $this->_page_contents = 'address_book_delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Customers_Admin::deleteAddress($_GET['abID'], $_GET['cID']) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&cID=' . $_GET['cID'] . '&page=' . $_GET['page'] . '&search=' . $_GET['search'] . '&action=save&tabIndex=tabAddressBook'));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Customers_Admin::delete($id, (isset($_POST['delete_reviews']) && ($_POST['delete_reviews'] == 'on') ? true : false)) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&search=' . $_GET['search']));
              }
            }

            break;
        }
      }
    }
  }
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Newsletter_product_notification {

/* Private methods */

    var $_title,
        $_has_audience_selection = true,
        $_newsletter_title,
        $_newsletter_content,
        $_newsletter_id,
        $_audience_size = 0;

/* Class constructor */

    function osC_Newsletter_product_notification($title = '', $content = '', $newsletter_id = '') {
      $this->_title = MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_TITLE;

      $this->_newsletter_title = $title;
      $this->_newsletter_content = $content;
      $this->_newsletter_id = $newsletter_id;
    }

/* Public methods */

    function getTitle() {
      return $this->_title;
    }

    function hasAudienceSelection() {
      if ($this->_has_audience_selection === true) {
        return true;
      }

      return false;
    }

    function showAudienceSelectionForm() {
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      global $osC_Database, $osC_Language;

      $products_array = array();

      $Qproducts = $osC_Database->query('select pd.products_id, pd.products_name from :table_products p, :table_products_description pd where pd.language_id = :language_id and pd.products_id = p.products_id and p.products_status = 1 order by pd.products_name');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $products_array[] = array('id' => $Qproducts->valueInt('products_id'),
                                  'text' => $Qproducts->value('products_name'));
      }

      $Qproducts->freeResult();

      $audience_form = '<script language="javascript"><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.products.length); x++) {
      if (document.notifications.products.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.products.options[x].text,document.notifications.products.options[x].value);
        }
        document.notifications.products.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.products) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_JS_PLEASE_SELECT_PRODUCTS . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';

      $audience_form .= tep_draw_form('notifications', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmConfirm', 'post', 'onSubmit="return selectAll(\'notifications\', \'chosen[]\');"') .
                        '  <table border="0" width="100%" cellspacing="0" cellpadding="2">' .
                        '    <tr>' .
                        '      <td align="center" class="main"><b>' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_TABLE_HEADING_PRODUCTS . '</b><br>' . osc_draw_pull_down_menu('products', $products_array, '', 'size="20" style="width: 20em;" multiple') . '</td>' .
                        '      <td align="center" class="main">&nbsp;<br><input type="button" value="' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_BUTTON_GLOBAL . '" style="width: 90px;" onClick="document.location=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmConfirm&global=true') . '\';" class="operationButton"><br><br><br><input type="button" value="' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_BUTTON_SELECT . '" style="width: 90px;" onClick="mover(\'remove\');" class="infoBoxButton"><br><br><input type="button" value="' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_BUTTON_UNSELECT . '" style="width: 90px;" onClick="mover(\'add\');" class="infoBoxButton"><br><br><br><input type="submit" value="' . BUTTON_OK . '" style="width: 90px;" class="operationButton"><br><br><input type="button" value="' . BUTTON_CANCEL . '" style="width: 90px;" onClick="document.location=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton"></td>' .
                        '      <td align="center" class="main"><b>' . MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_TABLE_HEADING_SELECTED_PRODUCTS . '</b><br>' . osc_draw_pull_down_menu('chosen[]', array(), '', 'size="20" style="width: 20em;" multiple') . '</td>' .
                        '    </tr>' .
                        '  </table>' .
                        '</form>';

      return $audience_form;
    }

    function showConfirmation() {
      if (PHP_VERSION < 4.1) {
        global $_GET, $_POST;
      }

      global $osC_Database;

      if ( (isset($_POST['chosen']) && !empty($_POST['chosen'])) || (isset($_GET['global']) && ($_GET['global'] == 'true')) ) {
        $Qcustomers = $osC_Database->query('select count(customers_info_id) as total from :table_customers_info where global_product_notifications = 1');
        $Qcustomers->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
        $Qcustomers->execute();

        $this->_audience_size = $Qcustomers->valueInt('total');

        $Qcustomers = $osC_Database->query('select count(distinct pn.customers_id) as total from :table_products_notifications pn, :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where pn.customers_id = c.customers_id and nl.email_address is null');
        $Qcustomers->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
        $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomers->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
        $Qcustomers->bindInt(':newsletters_id', $this->_newsletter_id);

        if (isset($_POST['chosen']) && !empty($_POST['chosen'])) {
          $Qcustomers->appendQuery('and pn.products_id in (:products_id)');
          $Qcustomers->bindRaw(':products_id', implode(', ', $_POST['chosen']));
        }

        $Qcustomers->execute();

        $this->_audience_size =+ $Qcustomers->valueInt('total');
      }

      $confirmation_string = '<p><font color="#ff0000"><b>' . sprintf(MODULE_NEWSLETTER_PRODUCT_NOTIFICATION_TEXT_TOTAL_RECIPIENTS, $this->_audience_size) . '</b></font></p>' .
                             '<p><b>' . $this->_newsletter_title . '</b></p>' .
                             '<p>' . nl2br(tep_output_string_protected($this->_newsletter_content)) . '</p>' .
                             tep_draw_form('confirm', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmSendConfirm') .
                             '<p align="right">';

      if ($this->_audience_size > 0) {
        if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
          $confirmation_string .= osc_draw_hidden_field('global', 'true');
        } elseif (isset($_POST['chosen']) && !empty($_POST['chosen'])) {
          for ($i=0, $n=sizeof($_POST['chosen']); $i<$n; $i++) {
            $confirmation_string .= osc_draw_hidden_field('chosen[]', $_POST['chosen'][$i]);
          }
        }

        $confirmation_string .= '<input type="submit" value="' . BUTTON_SEND . '" class="operationButton">&nbsp;';
      }

      $confirmation_string .= '<input type="button" value="' . BUTTON_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmSend') . '\'" class="operationButton">&nbsp;<input type="button" value="' . BUTTON_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\'" class="operationButton"></p>' .
                              '</form>';

      return $confirmation_string;
    }

    function sendEmail() {
      if (PHP_VERSION < 4.1) {
        global $_GET, $_POST;
      }

      global $osC_Database;

      $max_execution_time = 0.8 * (int)ini_get('max_execution_time');
      $time_start = explode(' ', PAGE_PARSE_START_TIME);

      if (isset($_POST['chosen'])) {
        $chosen = $_POST['chosen'];
      } elseif (isset($_GET['chosen'])) {
        $chosen = $_GET['chosen'];
      } elseif (isset($_POST['global'])) {
        $global = $_POST['global'];
      } elseif (isset($_GET['global'])) {
        $global = $_GET['global'];
      }

      $chosen_get_string = '';
      if (isset($chosen) && !empty($chosen)) {
        foreach ($chosen as $id) {
          $chosen_get_string .= 'chosen[]=' . $id . '&';
        }
      }

      $audience = array();

      $Qcustomers = $osC_Database->query('select c.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from :table_customers c, :table_customers_info ci where ci.global_product_notifications = 1 and ci.customers_info_id = c.customers_id');
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qcustomers->execute();

      while ($Qcustomers->next()) {
        if (!isset($audience[$Qcustomers->valueInt('customers_id')])) {
          $audience[$Qcustomers->valueInt('customers_id')] = array('firstname' => $Qcustomers->value('customers_firstname'),
                                                                   'lastname' => $Qcustomers->value('customers_lastname'),
                                                                   'email_address' => $Qcustomers->value('customers_email_address'));
        }
      }

      $Qcustomers = $osC_Database->query('select distinct pn.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from :table_products_notifications pn, :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where pn.customers_id = c.customers_id and nl.email_address is null');
      $Qcustomers->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
      $Qcustomers->bindInt(':newsletters_id', $this->_newsletter_id);

      if (isset($chosen) && !empty($chosen)) {
        $Qcustomers->appendQuery('and pn.products_id in (:products_id)');
        $Qcustomers->bindRaw(':products_id', implode(', ', $chosen));
      }

      $Qcustomers->execute();

      while ($Qcustomers->next()) {
        if (!isset($audience[$Qcustomers->valueInt('customers_id')])) {
          $audience[$Qcustomers->valueInt('customers_id')] = array('firstname' => $Qcustomers->value('customers_firstname'),
                                                                   'lastname' => $Qcustomers->value('customers_lastname'),
                                                                   'email_address' => $Qcustomers->value('customers_email_address'));
        }
      }

      if (sizeof($audience) > 0) {
        $mimemessage = new email(array(base64_decode('WC1NYWlsZXI6IG9zQ29tbWVyY2UgKGh0dHA6Ly93d3cub3Njb21tZXJjZS5jb20p')));
        $mimemessage->add_text($this->_newsletter_content);
        $mimemessage->build_message();

        foreach ($audience as $key => $value) {
          $mimemessage->send($value['firstname'] . ' ' . $value['lastname'], $value['email_address'], '', EMAIL_FROM, $this->_newsletter_title);

          $Qlog = $osC_Database->query('insert into :table_newsletters_log (newsletters_id, email_address, date_sent) values (:newsletters_id, :email_address, now())');
          $Qlog->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
          $Qlog->bindInt(':newsletters_id', $this->_newsletter_id);
          $Qlog->bindValue(':email_address', $value['email_address']);
          $Qlog->execute();

          $time_end = explode(' ', microtime());
          $timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

          if ($timer_total > $max_execution_time) {
            echo '<p><font color="#38BB68"><b>' . TEXT_REFRESHING_PAGE . '</b></font></p>' .
                 '<p><a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm&' . ((isset($global) && ($global == 'true')) ? 'global=true' : $chosen_get_string)) . '">' . TEXT_CONTINUE_MANUALLY . '</a></p>' .
                 '<META HTTP-EQUIV="refresh" content="2; URL=' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm&' . ((isset($global) && ($global == 'true')) ? 'global=true' : $chosen_get_string)) . '">';
            exit;
          }
        }
      }

      $Qupdate = $osC_Database->query('update :table_newsletters set date_sent = now(), status = 1 where newsletters_id = :newsletters_id');
      $Qupdate->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
      $Qupdate->bindInt(':newsletters_id', $this->_newsletter_id);
      $Qupdate->execute();
    }
  }
?>

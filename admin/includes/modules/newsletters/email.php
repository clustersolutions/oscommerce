<?php
/*
  $Id: email.php,v 1.2 2004/08/17 23:41:04 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Newsletter_email {

/* Private methods */

    var $_title,
        $_has_audience_selection = true,
        $_newsletter_title,
        $_newsletter_content,
        $_newsletter_id,
        $_audience_size = 0;

/* Class constructor */

    function osC_Newsletter_email($title = '', $content = '', $newsletter_id = '') {
      $this->_title = MODULE_NEWSLETTER_EMAIL_TITLE;

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

      global $osC_Database;

      $customers_array = array(array('id' => '***', 'text' => MODULE_NEWSLETTER_EMAIL_TEXT_ALL_CUSTOMERS));

      $Qcustomers = $osC_Database->query('select customers_id, customers_firstname, customers_lastname, customers_email_address from :table_customers order by customers_lastname');
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->execute();

      while ($Qcustomers->next()) {
        $customers_array[] = array('id' => $Qcustomers->valueInt('customers_id'),
                                   'text' => $Qcustomers->value('customers_lastname') . ', ' . $Qcustomers->value('customers_firstname') . ' (' . $Qcustomers->value('customers_email_address') . ')');
      }

      $Qcustomers->freeResult();

      $audience_form = tep_draw_form('customers', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmConfirm') .
                       '  <p align="center">' . osc_draw_pull_down_menu('customer', $customers_array, '', 'size="20" style="width: 100%;"') . '</p>' .
                       '  <p align="right"><input type="submit" value="' . BUTTON_OK . '" class="operationButton">&nbsp;<input type="button" value="' . BUTTON_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton"></p>' .
                       '</form>';

      return $audience_form;
    }

    function showConfirmation() {
      if (PHP_VERSION < 4.1) {
        global $_GET, $_POST;
      }

      global $osC_Database;

      if (isset($_POST['customer']) && !empty($_POST['customer'])) {
        $Qcustomers = $osC_Database->query('select count(customers_id) as total from :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where nl.email_address is null');
        $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomers->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
        $Qcustomers->bindInt(':newsletters_id', $this->_newsletter_id);

        if (is_numeric($_POST['customer'])) {
          $Qcustomers->appendQuery('and c.customers_id = :customers_id');
          $Qcustomers->bindInt(':customers_id', $_POST['customer']);
        }

        $Qcustomers->execute();

        $this->_audience_size =+ $Qcustomers->valueInt('total');
      }

      $confirmation_string = '<p><font color="#ff0000"><b>' . sprintf(MODULE_NEWSLETTER_EMAIL_TEXT_TOTAL_RECIPIENTS, $this->_audience_size) . '</b></font></p>' .
                             '<p><b>' . $this->_newsletter_title . '</b></p>' .
                             '<p>' . nl2br(tep_output_string_protected($this->_newsletter_content)) . '</p>' .
                             tep_draw_form('confirm', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmSendConfirm') .
                             '<p align="right">';

      if ($this->_audience_size > 0) {
        $confirmation_string .= osc_draw_hidden_field('customer', $_POST['customer']) .
                                '<input type="submit" value="' . BUTTON_SEND . '" class="operationButton">&nbsp;';
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

      $audience = array();

      $customer = '';
      if (isset($_POST['customer']) && !empty($_POST['customer'])) {
        $customer = $_POST['customer'];
      } elseif (isset($_GET['customer']) && !empty($_GET['customer'])) {
        $customer = $_GET['customer'];
      }

      if (!empty($customer)) {
        $Qcustomers = $osC_Database->query('select customers_id, customers_firstname, customers_lastname, customers_email_address from :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where nl.email_address is null');
        $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qcustomers->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
        $Qcustomers->bindInt(':newsletters_id', $this->_newsletter_id);

        if (is_numeric($customer)) {
          $Qcustomers->appendQuery('and c.customers_id = :customers_id');
          $Qcustomers->bindInt(':customers_id', $customer);
        }

        $Qcustomers->execute();

        while ($Qcustomers->next()) {
          if (!isset($audience[$Qcustomers->valueInt('customers_id')])) {
            $audience[$Qcustomers->valueInt('customers_id')] = array('firstname' => $Qcustomers->value('customers_firstname'),
                                                                     'lastname' => $Qcustomers->value('customers_lastname'),
                                                                     'email_address' => $Qcustomers->value('customers_email_address'));
          }
        }

        $Qcustomers->freeResult();

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
                   '<p><a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm&customer=' . $customer) . '">' . TEXT_CONTINUE_MANUALLY . '</a></p>' .
                   '<META HTTP-EQUIV="refresh" content="2; URL=' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm&customer=' . $customer) . '">';
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
  }
?>

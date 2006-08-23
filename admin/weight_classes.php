<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['wcID']) && is_numeric($_GET['wcID'])) {
          $weight_class_id = $_GET['wcID'];
        } else {
          $Qwc = $osC_Database->query('select max(weight_class_id) as weight_class_id from :table_weight_classes');
          $Qwc->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
          $Qwc->execute();

          $weight_class_id = ($Qwc->valueInt('weight_class_id') + 1);
        }

        $error = false;

        $osC_Database->startTransaction();

        foreach ($osC_Language->getAll() as $l) {
          if (isset($_GET['wcID']) && is_numeric($_GET['wcID'])) {
            $Qwc = $osC_Database->query('update :table_weight_classes set weight_class_key = :weight_class_key, weight_class_title = :weight_class_title where weight_class_id = :weight_class_id and language_id = :language_id');
          } else {
            $Qwc = $osC_Database->query('insert into :table_weight_classes (weight_class_id, language_id, weight_class_key, weight_class_title) values (:weight_class_id, :language_id, :weight_class_key, :weight_class_title)');
          }
          $Qwc->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
          $Qwc->bindInt(':weight_class_id', $weight_class_id);
          $Qwc->bindInt(':language_id', $l['id']);
          $Qwc->bindValue(':weight_class_key', $_POST['weight_class_key'][$l['id']]);
          $Qwc->bindValue(':weight_class_title', $_POST['weight_class_title'][$l['id']]);
          $Qwc->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          if (isset($_GET['wcID']) && is_numeric($_GET['wcID'])) {
            $Qrules = $osC_Database->query('select weight_class_to_id from :table_weight_classes_rules where weight_class_from_id = :weight_class_from_id and weight_class_to_id != :weight_class_to_id');
            $Qrules->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
            $Qrules->bindInt(':weight_class_from_id', $weight_class_id);
            $Qrules->bindInt(':weight_class_to_id', $weight_class_id);
            $Qrules->execute();

            while ($Qrules->next()) {
              $Qrule = $osC_Database->query('update :table_weight_classes_rules set weight_class_rule = :weight_class_rule where weight_class_from_id = :weight_class_from_id and weight_class_to_id = :weight_class_to_id');
              $Qrule->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
              $Qrule->bindValue(':weight_class_rule', $_POST['weight_class_rules'][$Qrules->valueInt('weight_class_to_id')]);
              $Qrule->bindInt(':weight_class_from_id', $weight_class_id);
              $Qrule->bindInt(':weight_class_to_id', $Qrules->valueInt('weight_class_to_id'));
              $Qrule->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }
          } else {
            $Qclasses = $osC_Database->query('select weight_class_id from :table_weight_classes where weight_class_id != :weight_class_id and language_id = :language_id');
            $Qclasses->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
            $Qclasses->bindInt(':weight_class_id', $weight_class_id);
            $Qclasses->bindInt(':language_id', $osC_Language->getID());
            $Qclasses->execute();

            while ($Qclasses->next()) {
              $Qdefault = $osC_Database->query('insert into :table_weight_classes_rules (weight_class_from_id, weight_class_to_id, weight_class_rule) values (:weight_class_from_id, :weight_class_to_id, :weight_class_rule)');
              $Qdefault->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
              $Qdefault->bindInt(':weight_class_from_id', $Qclasses->valueInt('weight_class_id'));
              $Qdefault->bindInt(':weight_class_to_id', $weight_class_id);
              $Qdefault->bindValue(':weight_class_rule', '1');
              $Qdefault->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }

              if ($error === false) {
                $Qnew = $osC_Database->query('insert into :table_weight_classes_rules (weight_class_from_id, weight_class_to_id, weight_class_rule) values (:weight_class_from_id, :weight_class_to_id, :weight_class_rule)');
                $Qnew->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
                $Qnew->bindInt(':weight_class_from_id', $weight_class_id);
                $Qnew->bindInt(':weight_class_to_id', $Qclasses->valueInt('weight_class_id'));
                $Qnew->bindValue(':weight_class_rule', $_POST['weight_class_rules'][$Qclasses->valueInt('weight_class_id')]);
                $Qnew->execute();

                if ($osC_Database->isError()) {
                  $error = true;
                  break;
                }
              }
            }
          }
        }

        if ($error === false) {
          if (isset($_POST['default']) && ($_POST['default'] == 'on') && (SHIPPING_WEIGHT_UNIT != $weight_class_id)) {
            $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bindInt(':configuration_value', $weight_class_id);
            $Qupdate->bindValue(':configuration_key', 'SHIPPING_WEIGHT_UNIT');
            $Qupdate->execute();

            if ($osC_Database->isError() === false) {
              $clear_cache = ($Qupdate->affectedRows() ? true : false);
            } else {
              $error = true;
            }
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          if (isset($_POST['default']) && ($_POST['default'] == 'on') && (SHIPPING_WEIGHT_UNIT != $weight_class_id)) {
            if ($clear_cache === true) {
              $osC_Cache->clear('configuration');
            }
          }

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_WEIGHT_CLASSES, 'page=' . $_GET['page'] . '&wcID=' . $weight_class_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['wcID']) && is_numeric($_GET['wcID'])) {
          $Qcheck = $osC_Database->query('select count(*) as total from :table_products where products_weight_class = :products_weight_class');
          $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
          $Qcheck->bindInt(':products_weight_class', $_GET['wcID']);
          $Qcheck->execute();

          if ( (SHIPPING_WEIGHT_UNIT == $_GET['wcID']) || ($Qcheck->valueInt('total') > 0) ) {
            if (SHIPPING_WEIGHT_UNIT == $_GET['wcID']) {
              $osC_MessageStack->add_session('header', TEXT_INFO_DELETE_PROHIBITED, 'warning');
            }

            if ($Qcheck->valueInt('total') > 0) {
              $osC_MessageStack->add_session('header', sprintf(TEXT_INFO_DELETE_PROHIBITED_PRODUCTS, $Qcheck->valueInt('total')), 'warning');
            }

            osc_redirect(osc_href_link_admin(FILENAME_WEIGHT_CLASSES, 'page=' . $_GET['page'] . '&wcID=' . $_GET['wcID']));
          } else {
            $error = false;

            $osC_Database->startTransaction();

            $Qrules = $osC_Database->query('delete from :table_weight_classes_rules where weight_class_from_id = :weight_class_from_id or weight_class_to_id = :weight_class_to_id');
            $Qrules->bindTable(':table_weight_classes_rules', TABLE_WEIGHT_CLASS_RULES);
            $Qrules->bindInt(':weight_class_from_id', $_GET['wcID']);
            $Qrules->bindInt(':weight_class_to_id', $_GET['wcID']);
            $Qrules->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }

            if ($error === false) {
              $Qclasses = $osC_Database->query('delete from :table_weight_classes where weight_class_id = :weight_class_id');
              $Qclasses->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
              $Qclasses->bindInt(':weight_class_id', $_GET['wcID']);
              $Qclasses->execute();

              if ($osC_Database->isError()) {
                $error = true;
              }
            }

            if ($error === false) {
              $osC_Database->commitTransaction();

              $osC_Cache->clear('weight-classes');
              $osC_Cache->clear('weight-rules');

              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_Database->rollbackTransaction();

              $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }

            osc_redirect(osc_href_link_admin(FILENAME_WEIGHT_CLASSES, 'page=' . $_GET['page']));
          }
        }
        break;
    }
  }

  $page_contents = 'weight_classes.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

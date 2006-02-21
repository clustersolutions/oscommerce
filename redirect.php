<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  switch ($_GET['action']) {
    case 'banner':
      if (isset($_GET['goto']) && is_numeric($_GET['goto'])) {
        if ($osC_Services->isStarted('banner') && $osC_Banner->isActive($_GET['goto'])) {
          tep_redirect($osC_Banner->getURL($_GET['goto'], true));
        }
      }
      break;

    case 'url':
      if (isset($_GET['goto']) && tep_not_null($_GET['goto'])) {
        $Qcheck = $osC_Database->query('select products_url from :table_products_description where products_url = :products_url limit 1');
        $Qcheck->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qcheck->bindValue(':products_url', $_GET['goto']);
        $Qcheck->execute();

        if ($Qcheck->numberOfRows() === 1) {
          tep_redirect('http://' . $HTTP_GET_VARS['goto']);
        }
      }
      break;

    case 'manufacturer':
      if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
        $Qmanufacturer = $osC_Database->query('select manufacturers_url from :table_manufacturers_info where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
        $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
        $Qmanufacturer->bindInt(':manufacturers_id', $_GET['manufacturers_id']);
        $Qmanufacturer->bindInt(':languages_id', $osC_Language->getID());
        $Qmanufacturer->execute();

        if ($Qmanufacturer->numberOfRows() && tep_not_null($Qmanufacturer->value('manufacturers_url'))) {
          $Qupdate = $osC_Database->query('update :table_manufacturers_info set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
          $Qupdate->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qupdate->bindInt(':manufacturers_id', $_GET['manufacturers_id']);
          $Qupdate->bindInt(':languages_id', $osC_Language->getID());
          $Qupdate->execute();

          tep_redirect($Qmanufacturer->value('manufacturers_url'));
        } else {
// no url exists for the selected language, lets use the default language then
          $Qmanufacturer = $osC_Database->query('select mi.languages_id, mi.manufacturers_url from :table_manufacturers_info mi, :table_languages l where mi.manufacturers_id = :manufacturers_id and mi.languages_id = l.languages_id and l.code = :code');
          $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qmanufacturer->bindTable(':table_languages', TABLE_LANGUAGES);
          $Qmanufacturer->bindInt(':manufacturers_id', $_GET['manufacturers_id']);
          $Qmanufacturer->bindValue(':code', DEFAULT_LANGUAGE);
          $Qmanufacturer->execute();

          if ($Qmanufacturer->numberOfRows() && tep_not_null($Qmanufacturer->value('manufacturers_url'))) {
            $Qupdate = $osC_Database->query('update :table_manufacturers_info set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
            $Qupdate->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
            $Qupdate->bindInt(':manufacturers_id', $_GET['manufacturers_id']);
            $Qupdate->bindInt(':languages_id', $Qmanufacturer->valueInt('languages_id'));
            $Qupdate->execute();

            tep_redirect($Qmanufacturer->value('manufacturers_url'));
          }
        }
      }
      break;
  }

  tep_redirect(tep_href_link(FILENAME_DEFAULT));
?>

<?php
/*
  $Id: redirect.php,v 1.13 2004/11/28 18:32:34 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

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
        tep_redirect('http://' . $_GET['goto']);
      }
      break;

    case 'manufacturer':
      if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
        $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)$osC_Session->value('languages_id') . "'");
        if (tep_db_num_rows($manufacturer_query)) {
// url exists in selected language
          $manufacturer = tep_db_fetch_array($manufacturer_query);

          if (tep_not_null($manufacturer['manufacturers_url'])) {
            tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)$osC_Session->value('languages_id') . "'");

            tep_redirect($manufacturer['manufacturers_url']);
          }
        } else {
// no url exists for the selected language, lets use the default language then
          $manufacturer_query = tep_db_query("select mi.languages_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " mi, " . TABLE_LANGUAGES . " l where mi.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and mi.languages_id = l.languages_id and l.code = '" . DEFAULT_LANGUAGE . "'");
          if (tep_db_num_rows($manufacturer_query)) {
            $manufacturer = tep_db_fetch_array($manufacturer_query);

            if (tep_not_null($manufacturer['manufacturers_url'])) {
              tep_db_query("update " . TABLE_MANUFACTURERS_INFO . " set url_clicked = url_clicked+1, date_last_click = now() where manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and languages_id = '" . (int)$manufacturer['languages_id'] . "'");

              tep_redirect($manufacturer['manufacturers_url']);
            }
          }
        }
      }
      break;
  }

  tep_redirect(tep_href_link(FILENAME_DEFAULT));
?>

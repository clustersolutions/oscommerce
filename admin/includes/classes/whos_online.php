<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_WhosOnline_Admin {
    function getData($id) {
      global $osC_Database;

      $Qwho = $osC_Database->query('select * from :table_whos_online where session_id = :session_id');
      $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
      $Qwho->bindValue(':session_id', $id);
      $Qwho->execute();

      $data = $Qwho->toArray();

      $Qwho->freeResult();

      return $data;
    }

    function delete($id) {
      global $osC_Session, $osC_Database;

      osC_Session_Admin::delete($id);

      $Qwho = $osC_Database->query('delete from :table_whos_online where session_id = :session_id');
      $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
      $Qwho->bindValue(':session_id', $id);
      $Qwho->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }
  }
?>

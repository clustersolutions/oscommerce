<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Index\RPC;

  use osCommerce\OM\Core\Site\RPC\Controller as RPC;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class ShortcutAjaxVisit {
    public static function execute() {

     if(isset($_GET['module']) && $_GET['module']){

      $OSCOM_Database = Registry::get('Database');
      $site = OSCOM::getSite();

      $Qshortcuts = $OSCOM_Database->query('UPDATE :table_administrator_shortcuts SET lastvisit = :lastvisit WHERE administrators_id = :administrators_id AND module = :module');
      $Qshortcuts->bindInt(':administrators_id', $_SESSION[$site]['id']);
      $Qshortcuts->bindInt(':lastvisit', time());
      $Qshortcuts->bindValue(':module', $_GET['module']);
      $Qshortcuts->execute();
echo $_GET['module'];
     }
    }
  }

?>
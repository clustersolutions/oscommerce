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

  class ShortcutAjax {
    public static function execute() {

      $OSCOM_Database = Registry::get('Database');
      $site = OSCOM::getSite();

      $shortcuts = array();

      $Qshortcuts = $OSCOM_Database->query('select module, lastvisit from :table_administrator_shortcuts where administrators_id = :administrators_id');
      $Qshortcuts->bindInt(':administrators_id', $_SESSION[$site]['id']);
      $Qshortcuts->execute();

      while ( $Qshortcuts->next() ) {
        $shortcuts[$Qshortcuts->value('module')] = $Qshortcuts->value('lastvisit');
      }

      $result = array();
      if ( isset($_SESSION[$site]['id']) ) {
        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true && $data['shortcut_callback'] ) {
	   //echo \osCommerce\OM\Core\Site\Admin\Application\ErrorLog\ErrorLog::new_errors();
           require_once('osCommerce/OM/Core/Site/Admin/Application/'.$data['module'].'/'.$data['module'].'.php');
           $result[$data['module']] = call_user_func('\osCommerce\OM\Core\Site\Admin\Application\\'.$data['module'].'\\'.$data['module'].'::'.$data['shortcut_callback'], $shortcuts[$data['module']]);
          }
        }
      }
      echo json_encode($result);
    }
  }

?>
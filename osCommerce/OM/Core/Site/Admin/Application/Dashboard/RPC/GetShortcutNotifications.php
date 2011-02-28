<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Index\RPC;

  use osCommerce\OM\Core\Site\Admin\Application\Index\Index;
  use osCommerce\OM\Core\OSCOM;

  class GetShortcutNotifications {
    public static function execute() {
      $site = OSCOM::getSite();

      $result = array();

      if ( isset($_SESSION[$site]['id']) ) {
        if ( isset($_GET['reset']) && !empty($_GET['reset']) && OSCOM::siteApplicationExists($_GET['reset']) ) {
          Index::updateAppDateOpened($_SESSION[$site]['id'], $_GET['reset']);
        }

        $shortcuts = array();

        foreach ( Index::getShortcuts($_SESSION[$site]['id']) as $app ) {
          $shortcuts[$app['module']] = $app['last_viewed'];
        }

        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true ) {
            if ( method_exists('osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . $data['module'] . '\\' . $data['module'], 'getShortcutNotification') || class_exists('osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . $data['module'] . '\\Model\\getShortcutNotification') ) {
              $result[$data['module']] = call_user_func(array('osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . $data['module'] . '\\' . $data['module'], 'getShortcutNotification'), $shortcuts[$data['module']]);
            }
          }
        }
      }

      echo json_encode($result);
    }
  }
?>

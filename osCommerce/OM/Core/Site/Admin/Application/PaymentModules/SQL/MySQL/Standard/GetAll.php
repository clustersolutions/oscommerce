<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetAll {
    public static function execute() {
      $OSCOM_Database = Registry::get('PDO');

      $result = array();

      $Qpm = $OSCOM_Database->prepare('select code from :table_templates_boxes where modules_group = :modules_group order by code');
      $Qpm->bindValue(':modules_group', 'Payment');
      $Qpm->execute();

      $result['entries'] = $Qpm->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>

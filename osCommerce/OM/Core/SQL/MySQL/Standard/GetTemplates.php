<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetTemplates {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtemplates = $OSCOM_PDO->query('select id, code, title from :table_templates');
      $Qtemplates->setCache('templates');
      $Qtemplates->execute();

      return $Qtemplates->fetchAll();
    }
  }
?>

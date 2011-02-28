<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetLanguage {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $Qlanguage = $OSCOM_Database->prepare('select * from :table_languages where languages_id = :languages_id');
      $Qlanguage->bindInt(':languages_id', $data['id']);
      $Qlanguage->execute();

      return $Qlanguage->fetch();
    }
  }
?>

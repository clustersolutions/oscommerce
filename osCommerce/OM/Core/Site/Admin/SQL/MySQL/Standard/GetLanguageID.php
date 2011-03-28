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

  class GetLanguageID {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qlanguage = $OSCOM_PDO->prepare('select languages_id from :table_languages where code = :code');
      $Qlanguage->bindValue(':code', $data['code']);
      $Qlanguage->execute();

      return $Qlanguage->fetch();
    }
  }
?>

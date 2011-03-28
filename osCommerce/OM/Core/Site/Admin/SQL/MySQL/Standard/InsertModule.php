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

  class InsertModule {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qinstall = $OSCOM_PDO->prepare('insert into :table_modules (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindValue(':title', $data['title']);
      $Qinstall->bindValue(':code', $data['code']);
      $Qinstall->bindValue(':author_name', $data['author_name']);
      $Qinstall->bindValue(':author_www', $data['author_www']);
      $Qinstall->bindValue(':modules_group', $data['group']);
      $Qinstall->execute();

      return !$Qinstall->isError();
    }
  }
?>

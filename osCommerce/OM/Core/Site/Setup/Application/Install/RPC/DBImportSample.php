<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\RPC;

  use osCommerce\OM\Core\Site\Setup\Application\Install\Install;

  class DBImportSample {
    public static function execute() {
      $data = array('server' => trim(urldecode($_POST['server'])),
                    'username' => trim(urldecode($_POST['username'])),
                    'password' => trim(urldecode($_POST['password'])),
                    'database' => trim(urldecode($_POST['name'])),
                    'port' => trim(urldecode($_POST['port'])),
                    'class' => str_replace('_', '\\', trim(urldecode($_POST['class']))),
                    'table_prefix' => trim(urldecode($_POST['prefix']))
                   );

      try {
        Install::importSampleDB($data);

        $result = array('result' => true);
      } catch ( \Exception $e ) {
        $result = array('result' => false,
                        'error_message' => $e->getMessage());
      }

      echo json_encode($result);
    }
  }
?>

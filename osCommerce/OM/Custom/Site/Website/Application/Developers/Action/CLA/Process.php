<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Website\Application\Developers\Action\CLA;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\HttpRequest;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\Registry;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $data = array('full_name' => (isset($_POST['full_name']) ? HTML::sanitize($_POST['full_name']) : null),
                    'profile_username' => (isset($_POST['profile_username']) ? HTML::sanitize($_POST['profile_username']) : null),
                    'profile_password' => (isset($_POST['profile_password']) ? HTML::sanitize($_POST['profile_password']) : null),
                    'github_userprofile' => (isset($_POST['github_userprofile']) ? HTML::sanitize($_POST['github_userprofile']) : null),
                    'address' => (isset($_POST['address']) ? HTML::sanitize($_POST['address']) : null),
                    'telephone_number' => (isset($_POST['telephone_number']) ? HTML::sanitize($_POST['telephone_number']) : null));

      $error = false;

      foreach ( $data as $k => $v ) {
        if ( ($k != 'github_userprofile') && empty($v) ) {
          $error = true;
          $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Error: Please fill in all field details.');
          break;
        }
      }

      if ( !empty($data['github_userprofile']) ) {
        $response = json_decode(HttpRequest::getResponse(array('url' => 'https://github.com/api/v2/json/user/show/' . $data['github_userprofile'],
                                                                      'method' => 'get')));

        if ( isset($response->error) ) {
          $error = true;

          $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Error: Please verify your Github Profile Username.');
        }
      }

      if ( $error === false ) {
        $request = xmlrpc_encode_request('verifyMember', array('api_key' => '2d76094909effa426f008ede12fc5e16',
                                                               'api_module' => 'oscommerce',
                                                               'username' => $data['profile_username'],
                                                               'password' => md5($data['profile_password'])));

        $response = xmlrpc_decode(HttpRequest::getResponse(array('url' => 'http://forums.oscommerce.com/interface/board/index.php',
                                                                 'parameters' => $request)));

        if ( !isset($response['result']) || ($response['result'] !== true) ) {
          $error = true;

          $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Error: Please verify your osCommerce Profile credentials.');
        } else {
          $OSCOM_PDO = PDO::initialize();

          $Qcheck = $OSCOM_PDO->prepare('select id from osc_cla where profile_id = :profile_id');
          $Qcheck->bindInt(':profile_id', $response['member_id']);
          $Qcheck->execute();

          $result = $Qcheck->fetch();

          if ( ($result !== false) || !empty($result) ) {
            $error = true;

            $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Warning: You have already agreed to the CLA.', 'warning');

            OSCOM::redirect(OSCOM::getLink(null, null, 'CLA&Success'));
          } else {
            $Qinsert = $OSCOM_PDO->prepare('insert into osc_cla (name, profile_id, profile_username, github_profile, address, telephone, date_created) values (:name, :profile_id, :profile_username, :github_profile, :address, :telephone, now())');
            $Qinsert->bindValue(':name', $data['full_name']);
            $Qinsert->bindInt(':profile_id', $response['member_id']);
            $Qinsert->bindValue(':profile_username', $data['profile_username']);
            $Qinsert->bindValue(':github_profile', $data['github_userprofile']);
            $Qinsert->bindValue(':address', $data['address']);
            $Qinsert->bindValue(':telephone', $data['telephone_number']);
            $Qinsert->execute();

            if ( !$Qinsert->isError() ) {
              $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Success! Thanks for your submission! We look forward to working with you!', 'success');

              Cache::clear('cla_list');

              OSCOM::redirect(OSCOM::getLink(null, null, 'CLA&Success'));
            } else {
              $OSCOM_MessageStack->add(OSCOM::getSiteApplication(), 'Error: A general error has occurred. Please try again later.');
            }
          }
        }
      }
    }
  }
?>

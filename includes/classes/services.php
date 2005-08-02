<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services {
    var $services,
        $started_services;

    function osC_Services() {
      $this->services = explode(';', MODULE_SERVICES_INSTALLED);
    }

    function startServices() {
      $this->started_services = array();

      foreach ($this->services as $service) {
        $this->startService($service);
      }
    }

    function stopServices() {
/*
  ugly workaround to force the output_compression/GZIP service module to be stopped last
  to make sure all content in the buffer is compressed and sent to the client
*/
      if ($this->isStarted('output_compression')) {
        $key = array_search('output_compression', $this->started_services);
        unset($this->started_services[$key]);

        $this->started_services[] = 'output_compression';
      }

      foreach ($this->started_services as $service) {
        $this->stopService($service);
      }
    }

    function startService($service) {
      include('includes/modules/services/' . $service . '.php');

      if (@call_user_func(array('osC_Services_' . $service, 'start'))) {
        $this->started_services[] = $service;
      }
    }

    function stopService($service) {
      @call_user_func(array('osC_Services_' . $service, 'stop'));
    }


    function isStarted($service) {
      return in_array($service, $this->started_services);
    }
  }
?>

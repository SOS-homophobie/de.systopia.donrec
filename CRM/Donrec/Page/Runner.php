<?php
/*-------------------------------------------------------+
| SYSTOPIA Donation Receipts Extension                   |
| Copyright (C) 2013-2014 SYSTOPIA                       |
| Author: B. Endres (endres -at- systopia.de)            |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| TODO: License                                          |
+--------------------------------------------------------*/

require_once 'CRM/Core/Page.php';

/**
 * This class generates the front end controller page for the
 * donation receipt engine.
 *
 * @param (via $_REQUEST)   final      if not set, this is a test run
 * @param (via $_REQUEST)   bulk       if set, this accumulates contributions by contact
 * @param (via $_REQUEST)   exporters  cs list of exporter identifiers to run
 */
class CRM_Donrec_Page_Runner extends CRM_Core_Page {
  function run() {
    // extract the parameters
    $parameters = array();
    $parameters['test'] = empty($_REQUEST['final'])?1:0;
    $parameters['bulk'] = empty($_REQUEST['bulk'])?0:1;
    $parameters['exporters'] = empty($_REQUEST['exporters'])?array('Dummy'):explode(',', $_REQUEST['exporters']);

    // get the snapshot_id
    if (empty($_REQUEST['sid'])) {
      $this->assign('error', ts("No snapshot ID given. Please call this page from a proper selection."));
    } else {
      // Init the engine
      $sid = (int) $_REQUEST['sid'];
      $engine = new CRM_Donrec_Logic_Engine();
      $engine_error = $engine->init($sid, $parameters);
      if ($engine_error) {
        $this->assign('error', $engine_error);
      } else {
        $this->assign('sid', $sid);
        if ($parameters['test']) {
          // if this is a test-run: restart
          $engine->resetTestRun();
        }

        $this->assign('bulk', $parameters['bulk']);
        $this->assign('test', $parameters['test']);
        $this->assign('exporters', implode('', $parameters['exporters']));
      }
    }

    parent::run();
  }
}
<?php
use CRM_Kavagroepsaankopen_ExtensionUtil as E;

function _civicrm_api3_kavagroepsaankopen_Importfromfile_spec(&$spec) {
  $spec['file']['api.required'] = 1;
}

function civicrm_api3_kavagroepsaankopen_Importfromfile($params) {
  if (empty($params['file'])) {
    throw new API_Exception('missing parameter "file"', 999);
  }

  try {
    [$numImported, $numSkipped] = CRM_Kavagroepsaankopen_Import::fromFile($params['file']);
    $msg = "Aantal geïmporteerd: $numImported, aantal overgeslagen: $numSkipped";
    return civicrm_api3_create_success($msg, $params, 'Kavagroepsaankopen', 'Importfromfile');
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), 999);
  }
}

<?php

require_once 'kavagroepsaankopen.civix.php';

use CRM_Kavagroepsaankopen_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function kavagroepsaankopen_civicrm_config(&$config): void {
  _kavagroepsaankopen_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function kavagroepsaankopen_civicrm_install(): void {
  _kavagroepsaankopen_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function kavagroepsaankopen_civicrm_enable(): void {
  _kavagroepsaankopen_civix_civicrm_enable();
}

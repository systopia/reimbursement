<?php
declare(strict_types = 1);

use Civi\Reimbursement\ExpenseLoader;
use Civi\Reimbursement\ExpensePersister;
use Civi\Reimbursement\Form\ReimbursementCreateDataTransformer;
use Civi\Reimbursement\Form\ReimbursementDataTransformer;
use Civi\Reimbursement\Form\ReimbursementFormSpecFactory;
use Civi\Reimbursement\Helper\CustomFieldsHelper;
use Civi\Reimbursement\Helper\ExpenseTypeLoader;
use Civi\Reimbursement\Helper\FieldsLoader;
use Civi\Reimbursement\ReimbursementProfile;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
require_once 'reimbursement.civix.php';
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function reimbursement_civicrm_config(CRM_Core_Config $config): void {
  _reimbursement_civix_civicrm_config($config);
}

function reimbursement_civicrm_container(ContainerBuilder $container): void {
  $container->addResource(new FileResource(__FILE__));

  $container->autowire(CustomFieldsHelper::class);
  $container->autowire(ExpenseTypeLoader::class);
  $container->autowire(FieldsLoader::class);

  $container->autowire(ReimbursementCreateDataTransformer::class);
  $container->autowire(ReimbursementDataTransformer::class);
  $container->autowire(ReimbursementFormSpecFactory::class);

  $container->autowire(ExpenseLoader::class);
  $container->autowire(ExpensePersister::class);

  $container->autowire(ReimbursementProfile::class)
    ->addTag(ReimbursementProfile::SERVICE_TAG);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function reimbursement_civicrm_install(): void {
  _reimbursement_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function reimbursement_civicrm_enable(): void {
  _reimbursement_civix_civicrm_enable();
}

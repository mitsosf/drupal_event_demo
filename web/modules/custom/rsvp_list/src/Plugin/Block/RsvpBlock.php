<?php

namespace Drupal\rsvp_list\Plugin\Block;


use Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Session\AccountInterface;
use Drupal\rsvp_list\Service\EnablerService;

/**
 * Provides RSVP List Block
 * @package Drupal\rsvp_list\Plugin\Block
 * @Block(
 *   id = "rsvp_list_block",
 *   admin_label = @Translation("RSVP List Block"),
 *   )
 */
class RsvpBlock extends BlockBase
{

  public function build()
  {
    return Drupal::formBuilder()->getForm('Drupal\rsvp_list\Form\RsvpForm');
  }

  public function blockAccess(AccountInterface $account)
  {
    $node = Drupal::routeMatch()->getParameter('node');
    if (!empty($node)) {
      $nid = $node->nid->value;

      /** @var EnablerService $enabler */
      $enabler = Drupal::service('rsvp_list.enabler');

      if (is_numeric($nid) && $enabler->isEnabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvp_list');
      }
    }

    return AccessResult::forbidden();
  }
}

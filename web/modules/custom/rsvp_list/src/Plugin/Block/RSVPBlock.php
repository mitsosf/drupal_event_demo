<?php

namespace Drupal\rsvp_list\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;

/**
 * Provides RSVP List Block
 * @package Drupal\rsvp_list\Plugin\Block
 * @Block(
 *   id = "rsvp_list_block",
 *   admin_label = @Translation("RSVP List Block"),
 *   )
 */
class RSVPBlock extends BlockBase{

  public function build()
  {
    return ['#markup' => t('RSVP List Block')];
  }
}

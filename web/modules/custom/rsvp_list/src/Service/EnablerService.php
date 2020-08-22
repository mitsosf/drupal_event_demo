<?php

namespace Drupal\rsvp_list\Service;

use Drupal\Core\Database\Database;
use Drupal\jsonapi\JsonApiResource\Data;
use Drupal\node\Entity\Node;

class EnablerService
{
  public function __construct()
  {

  }

  public function enableNode(Node $node): bool
  {
    if (!$this->getNodeStatus($node)) {
      try {
        $query = Database::getConnection()->insert('rsvp_list_enabled');
        $query->fields(['nid'], [$node->id()]);
        $query->execute();
        return true;
      } catch (\Exception $e) {
        //Log exception
      }
    }

    return false;
  }

  public function disableNode(Node $node): bool
  {
    try {
      $query = Database::getConnection()->delete('rsvp_list_enabled');
      $query->condition('nid', $node->id());
      $query->execute();

      return true;
    } catch (\Exception $e) {
      //Log exception
    }

    return false;
  }

  public function getNodeStatus(Node $node): bool
  {
    if ($node->isNew()) {
      return false;
    }

    $result = false;
    try {
      $query = Database::getConnection()->select('rsvp_list_enabled', 're');
      $query->fields('re', ['nid']);
      $query->condition('nid', $node->id());
      $result = $query->execute();
    } catch (\Exception $e) {
      //Log expection
    }

    return !empty($result->fetchCol());
  }
}

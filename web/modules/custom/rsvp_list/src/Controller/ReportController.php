<?php

namespace Drupal\rsvp_list\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use PDO;

class ReportController extends ControllerBase
{
  protected function getRsvps(): array
  {
    $query = Database::getConnection()->select('rsvp_list', 'r');
    $query->join('users_field_data', 'u', 'r.uid = u.uid');
    $query->join('node_field_data', 'n', 'r.nid = n.nid');
    $query->addField('u', 'name', 'username');
    $query->addField('n', 'title');
    $query->addField('r', 'mail');
    return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
  }

  public function report(): array
  {
    $rsvps = $this->getRsvps();
    $entries = [];
    foreach ($rsvps as $rsvp) {
      // Sanitize each entry.
      $entries[] = $rsvp;
    }

    $content['message'] = [
      '#markup' => $this->t('Below is a list of all Event RSVPs including username, email address and the name of the event they will be attending.'),
    ];
    $content['table'] = [
      '#type' => 'table',
      '#header' => [
        t('Name'),
        t('Event'),
        t('Email'),
      ],
      '#rows' => $entries,
      '#empty' => t('No entries available.'),
    ];

    //Avoid caching page
    $content['#cache']['max-age'] = 0;
    return $content;
  }

}

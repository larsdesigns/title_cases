<?php

declare(strict_types=1);

namespace Drupal\title_cases\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\String\UnicodeString;

/**
 * Returns responses for title_cases routes.
 */
final class TitleCasesController extends ControllerBase {

  /**
   * Generate and Set AP Title Case formatted node titles.
   *
   * @param \Drupal\node\Entity\Node $vars
   *   Sets the AP converted node title.
   *
   * @return object
   *   Return node Variables.
   */
  public static function titleCasesNodeApTitle(&$vars): object {
    /** @var \Drupal\node\Entity\Node $vars['node'] */
    $node = $vars['node'];
    $ap_title = self::titleCasesApTitle($node);

    return $node->setTitle($ap_title);
  }

  /**
   * Generate AP Title Case formatted titles.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The Drupal node objects.
   *
   * @return string
   *   AP Title Case String.
   */
  public static function titleCasesApTitle(&$node): string {
    /** @var \Drupal\node\Entity\Node $node */        
    $ap_title = $node->getTitle();

    // Only convert title when content type is selected in configuration.
    $node_type = $node->getType();
    $types = \Drupal::config('title_cases.settings')->get('node_types');

    if (isset($types)) {
      if (in_array($node_type, $types)) {
        $title = new UnicodeString($node->getTitle());
        $case_title = $title->title(TRUE)->toString();
        $subjects = ['a', 'an', 'the', 'and', 'as', 'but', 'for', 'if', 'nor', 'or',
          'so', 'yet', 'at', 'by', 'for', 'in', 'of', 'off', 'on', 'per', 'to',
          'up', 'via',
        ];
        for ($count = 0; $count <= count($subjects) && !empty($subjects[$count]); $count++) {
          $case_title = str_replace(' ' . ucfirst($subjects[$count] . ' '), ' ' . lcfirst($subjects[$count] . ' '), $case_title);
        }
        $ap_title = ucfirst($case_title);
        $node->setTitle($ap_title);
      }      
    }
    return $ap_title;
  }

}

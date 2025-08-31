<?php

declare(strict_types=1);

namespace Drupal\title_cases\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Title Cases settings for this site.
 */
final class TitleCasesSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'title_cases_title_cases_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['title_cases.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\Core\Form\ConfigFormBase $form */

    // Get content types for options.
    $types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('node');
    foreach ($types as $key => $type) {
      $type_options[$key] = implode($type);
    }

    $form['node_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content Types'),
      '#options' => $type_options,
      '#prefix' => $this->t('Apply title case style to selected content type page titles:'),
      '#default_value' => $this->config('title_cases.settings')->get('node_types') ?? [],
    ];

    $form['html_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Apply title case to the HTML document title tag'),
      '#prefix' => sprintf('<b>%s</b>', $this->t('HTML Dcoument Title')),
      '#default_value' => $this->config('title_cases.settings')->get('html_title') ?? FALSE,
    ];

    $form['style_guide'] = [
      '#type' => 'radios',
      '#title' => $this->t('Style Guide'),
      '#options' => [
        'ap' => 'Associated Press (AP)',
        'uc' => 'Uppercase Title',
        // 'mla' => 'Modern Language Associateion (MLA)',
        // 'chicago' => 'Chicago',
      ],
      '#default_value' => $this->config('title_cases.settings')->get('style_guide') ?? 'ap',
    ];

    $form['instructions'] = [
      '#type' => '#markup',
      '#markup' => sprintf('<p><i>%s</i></p>', $this->t('
        The Title Case module only alters the presentation of the title field
        and does not change the title field value as stored in the database.
      ')),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('title_cases.settings')
      ->set('style_guide', $form_state->getValue('style_guide'))
      ->set('html_title', $form_state->getValue('html_title'))
      ->set('node_types', $form_state->getValue('node_types'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}

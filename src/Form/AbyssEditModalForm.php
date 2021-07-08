<?php

namespace Drupal\abyss\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Test module.
 */
class AbyssEditModalForm extends FormBase {

  /**
   * Array of table values.
   *
   * @var array
   */
  protected array $values = [];

  /**
   * Array saving table headers.
   *
   * @var array
   */
  private static array $fields = [
    'Jan',
    'Feb',
    'Mar',
    'Q1',
    'Apr',
    'May',
    'Jun',
    'Q2',
    'Jul',
    'Aug',
    'Sep',
    'Q3',
    'Oct',
    'Nov',
    'Dec',
    'Q4',
    'YTD',
  ];

  /**
   * Contained array headers name.
   *
   * @var array
   */
  private static array $headers;

  /**
   * Used fo generate array $headers value.
   *
   * @param array $list
   *   Contained months naming.
   *
   * @return array
   *   Return array headers name.
   */
  private function headerGenerate(array $list): array{
    if (empty(self::$headers)) {
      array_unshift($list, 'Year');
      self::$headers = array_map(
        fn (string $item) => $this->t($item),
        $list);
    }

    return self::$headers;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result"></div>',
    ];

    $form['#tree'] = TRUE;
    $form['list'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'columns-wrapper'],
    ];

    $form['list']['add_table'] = [
      '#type' => 'button',
      '#name' => 'add_table',
      '#value' => $this->t('Add Table'),
      '#ajax' => [
        'callback' => '::addMoreCallback',
        'event' => 'click',
        'wrapper' => 'columns-wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Wait, we adding table...'),
        ],
      ],
    ];

    $tables = $form_state->get('tables');
    $check_element = $form_state->getTriggeringElement();
    $tables = empty($tables) ? [1] : $tables;

    if (!empty($check_element)) {
      if ($check_element['#name'] === 'add_table') {
        $tables[] = 1;
      }
      else if (str_contains($check_element['#name'], 'add_rows')) {
        $tables[$check_element['#table']]++;
      }
    }

    $form_state->set('tables', $tables);
    $list = $form_state->getValue('list');
    $tables_count = count($tables);

    for ($i = 0; $i < $tables_count; $i++) {
      $this->createTable($form, $i, $tables, $list);
    }

    $form['list']['actions'] = [
      '#type' => 'actions',
    ];

    $form['list']['actions']['confirm'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send form'),
      '#ajax' => [
        'callback' => '::showStatus',
        'event' => 'click',
        'wrapper' => 'result',
      ],
    ];
    $form['#attached']['library'][] = 'abyss/form';

    return $form;
  }

  private function createTable(array &$form, $i, $tables, $list): void {
    $form['list'][$i] = [
      '#type' => 'fieldgroup',
    ];
    $form['list'][$i]['add_row'] = [
      '#name' => 'add_rows_' . $i,
      '#type' => 'button',
      '#table' => $i,
      '#value' => $this->t('Add Year'),
      '#submit' => ['::addRow'],
      '#ajax' => [
        'callback' => '::addMoreCallback',
        'event' => 'click',
        'wrapper' => 'columns-wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Wait, we adding row for you...'),
        ],
      ],
    ];

    $form['list'][$i]['table'] = [
      '#type' => 'table',
      '#header' => $this->headerGenerate(self::$fields),
    ];
    $form['list'][$i]['table']['#attributes']['class'][] = 'abyss-table';

    for ($j = $tables[$i]; $j > 0; $j--) {
      $this->createRow($form, $i, $j, $list);
    }
  }

  private function createRow(array &$form, $i, $j, $list): void {
    $form['list'][$i]['table'][$j]['Year'] = [
      '#plain_text' => date('Y') - $j + 1,
    ];

    foreach (self::$fields as $field) {
      $form['list'][$i]['table'][$j][$field] = [
        '#type' => 'number',
        '#title' => $field,
        '#step' => 0.01,
        '#title_display' => 'invisible',
      ];

      if (str_contains($field, 'Q') || str_contains($field, 'YTD')) {
        $form['list'][$i]['table'][$j][$field]['#wrapper_attributes']['class'][] = 'abyss-quarter';
        if (!empty($list)) {
          $form['list'][$i]['table'][$j][$field]['#attributes']['data-value'] = $list[$i]['table'][$j][$field];
        }
        else {
          $form['list'][$i]['table'][$j][$field]['#attributes']['data-value'] = '';
        }
        continue;
      }
      $form['list'][$i]['table'][$j][$field]['#wrapper_attributes']['class'][] = 'abyss-table-element';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'abyss_table_form';
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   *
   * @param array $form
   *   Contains a form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Contains variables and data that have been saved in the form.
   *
   * @return array
   *   Displays information about the save status.
   */
  public function addMoreCallback(array &$form, FormStateInterface $form_state): array {
    return $form['list'];
  }

  /**
   * Callback for AjaxForm.
   *
   * @param array $form
   *   Contains a form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Contains variables and data that have been saved in the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Displays information about the save status.
   */
  public function showStatus(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $valid = $form_state->get('valid');

    if ($valid) {
      $response->addCommand(new MessageCommand($this->t('Valid'),
        '.result', ['type' => 'status']));
    }
    else {
      $response->addCommand(new MessageCommand($this->t('Invalid'),
        '.result', ['type' => 'error']));
    }

    return $response;
  }

  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   *
   * @param array $form
   *   Contain form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Contains data stored in the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $num_of_tables = $form_state->get('tables');
    $value_row_group = [];
    $start = 0;
    $end = 0;
    $table_count = count($num_of_tables);
    $list = $form_state->getValue('list');

    for ($i = 0; $i < $table_count; $i++) {
      $this->gapValidation($list[$i]['table'], $num_of_tables[$i], $value_row_group);

      if ($i === 0) {
        $start = array_key_first($value_row_group);
        $end = array_key_last($value_row_group);
      }

      if (
      array_key_first($value_row_group) !== $start
      || array_key_last($value_row_group) !== $end
      || (
        array_key_first($value_row_group) + count($value_row_group)
        !== array_key_last($value_row_group) + 1
      )
      ) {
        $form_state->set('valid', FALSE);
        return;
      }
    }

    $form_state->set('valid', TRUE);
  }

  /**
   * Function of combining arrays of table data into one array.
   *
   * @param array $tmp
   *   Contains data from tables.
   * @param int $num_of_rows
   *   Contain rows num.
   * @param array $fields
   *   Used to return a grouped data set.
   */
  private function gapValidation(array $tmp, int $num_of_rows, array &$fields): void {
    $revers_fields = array_reverse(self::$fields);
    $fields = [];
    $i = 0;

    for ($j = 1; $j <= $num_of_rows; $j++) {
      foreach ($revers_fields as $field) {
        if (!(str_contains($field, 'Q') || str_contains($field, 'YTD'))) {
          if ('' !== $item = $tmp[$j][$field]['#value']) {
            $fields[$i] = $item;
          }
          $i++;
        }
      }
    }
  }

}

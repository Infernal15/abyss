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
   * Form with 'add more' and 'remove' buttons.
   *
   * This example shows a button to "add more" - add another textfield, and
   * the corresponding "remove" button.
   */

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
    //$tables = empty($tables) ?? [0 => 1];
    if (empty($tables)) {
      $tables = [];
      $tables[] = 1;
    }
    if (!empty($check_element)) {
      if ($check_element['#name'] == 'add_table') {
        $tables[] = 1;
      }
      if ($check_element) {
        if (str_contains($check_element['#name'], 'add_rows')) {
          $tables[$check_element['#table']]++;
        }
      }
    }

    $form_state->set('tables', $tables);

    for ($i = 0; $i < count($tables); $i++) {
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
        '#header' => [
          $this->t('Year'),
          $this->t('Jan'),
          $this->t('Feb'),
          $this->t('Mar'),
          [
            'class' => 'abyss-quarter',
            'data' => $this->t('Q1'),
          ],
          $this->t('Apr'),
          $this->t('May'),
          $this->t('Jun'),
          [
            'class' => 'abyss-quarter',
            'data' => $this->t('Q2'),
          ],
          $this->t('Jul'),
          $this->t('Aug'),
          $this->t('Sep'),
          [
            'class' => 'abyss-quarter',
            'data' => $this->t('Q3'),
          ],
          $this->t('Act'),
          $this->t('Nov'),
          $this->t('Dec'),
          [
            'class' => 'abyss-quarter',
            'data' => $this->t('Q4'),
          ],
          [
            'class' => 'abyss-quarter',
            'data' => $this->t('YTD'),
          ],
        ],
      ];
      $form['list'][$i]['table']['#attributes']['class'][] = 'abyss-table';

      for ($j = $tables[$i]; $j > 0; $j--) {
        $form['list'][$i]['table'][$j]['Year'] = [
          '#plain_text' => date('Y') - $j + 1,
        ];

        foreach (self::$fields as $field) {
          if (str_contains($field, 'Q') || str_contains($field, 'YTD')) {
            $form['list'][$i]['table'][$j][$field] = [
              '#type' => 'number',
              '#step' => 0.01,
              '#title' => $field,
              '#title_display' => 'invisible',
              '#wrapper_attributes' => [
                'class' => [
                  'abyss-quarter',
                ],
              ],
            ];
            $temp = $form_state->getValue('list');
            if (!empty($temp)) {
              $form['list'][$i]['table'][$j][$field]['#attributes']['data-value'] = $temp[$i]['table'][$j][$field];
            }
            else {
              $form['list'][$i]['table'][$j][$field]['#attributes']['data-value'] = '';
            }
            continue;
          }
          $form['list'][$i]['table'][$j][$field] = [
            '#type' => 'number',
            '#title' => $field,
            '#title_display' => 'invisible',
            '#wrapper_attributes' => [
              'class' => [
                'abyss-table-element',
              ],
            ],
          ];
        }
      }
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

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
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
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Displays information about the save status.
   */
  public function addMoreCallback(array &$form, FormStateInterface $form_state) {
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $num_of_tables = $form_state->get('tables');
    $value_row_group = [];
    $start = 0;
    $end = 0;

    for ($i = 0; $i < count($num_of_tables); $i++) {
      $tmp = $form_state->getValue('list')[$i]['table'];
      $this->gapValidation($tmp, $num_of_tables[$i], $value_row_group);

      $value_row_group = array_filter($value_row_group, function ($v) {
        return $v !== '';
      });

      if ($i === 0) {
        $start = array_key_first($value_row_group);
        $end = array_key_last($value_row_group);
      }

      if (
        array_key_first($value_row_group) + count($value_row_group)
        !== array_key_last($value_row_group) + 1
      ) {
        $form_state->set('valid', FALSE);

        return;
      }
      if (
        array_key_first($value_row_group) != $start
        || array_key_last($value_row_group) != $end
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
  private function gapValidation(array $tmp, int $num_of_rows, array &$fields) {
    $revers_fields = array_reverse(self::$fields);
    $fields = [];

    for ($j = 1; $j <= $num_of_rows; $j++) {
      foreach ($revers_fields as $field) {
        if (!(str_contains($field, 'Q') || str_contains($field, 'YTD'))) {
          array_push($fields, $tmp[$j][$field]['#value']);
        }
      }
    }
  }

}

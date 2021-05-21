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
  protected array $values;

  /**
   * Array saving table headers.
   *
   * @var array
   */
  protected array $fields;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->values = [];
    $this->fields = [
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
      '#type' => 'submit',
      '#value' => $this->t('Add Table'),
      '#submit' => ['::addTable'],
      '#ajax' => [
        'callback' => '::addMoreCallback',
        'event' => 'click',
        'wrapper' => 'columns-wrapper',
      ],
    ];

    $num_of_tables = $form_state->get('num_of_tables');
    if (empty($num_of_tables)) {
      $num_of_tables = 1;
      $form_state->set('num_of_tables', $num_of_tables);
    }

    for ($i = 0; $i < $num_of_tables; $i++) {
      $num_of_rows[$i] = $form_state->get("num_of_rows$i");
      if (empty($num_of_rows[$i])) {
        $num_of_rows[$i] = 1;
        $form_state->set("num_of_rows$i", $num_of_rows[$i]);
      }
    }

    for ($i = 0; $i < $num_of_tables; $i++) {
      $form['list'][$i] = [
        '#type' => 'fieldgroup',
      ];
      $form['list'][$i]['add_row'] = [
        '#name' => 'op ' . $i,
        '#type' => 'submit',
        '#value' => $this->t('Add Year'),
        '#submit' => ['::addRow'],
        '#ajax' => [
          'callback' => '::addMoreCallback',
          'event' => 'click',
          'wrapper' => 'columns-wrapper',
        ],
      ];

      $form['list'][$i]['table'] = [
        '#type' => 'table',
        '#header' => [
          $this
            ->t('Year'),
          $this
            ->t('Jan'),
          $this
            ->t('Feb'),
          $this
            ->t('Mar'),
          [
            'class' => 'abyss-quarter',
            'data' => $this
              ->t('Q1'),
          ],
          $this
            ->t('Apr'),
          $this
            ->t('May'),
          $this
            ->t('Jun'),
          [
            'class' => 'abyss-quarter',
            'data' => $this
              ->t('Q2'),
          ],
          $this
            ->t('Jul'),
          $this
            ->t('Aug'),
          $this
            ->t('Sep'),
          [
            'class' => 'abyss-quarter',
            'data' => $this
              ->t('Q3'),
          ],
          $this
            ->t('Act'),
          $this
            ->t('Nov'),
          $this
            ->t('Dec'),
          [
            'class' => 'abyss-quarter',
            'data' => $this
              ->t('Q4'),
          ],
          [
            'class' => 'abyss-quarter',
            'data' => $this
              ->t('YTD'),
          ],
        ],
      ];
      $form['list'][$i]['table']['#attributes']['class'][] = 'abyss-table';

      for ($j = 0; $j < $num_of_rows[$i]; $j++) {
        $form['list'][$i]['table'][$j]['Year'] = [
          '#plain_text' => date("Y") - $num_of_rows[$i] + $j + 1,
        ];

        foreach ($this->fields as $field) {
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
              '#field_prefix' => '-',
              '#field_suffix' => '+',
            ];
            $form['list'][$i]['table'][$j][$field]['#attributes']['data-value'] = $this->values[$i][$j][$field] ? $this->values[$i][$j][$field] : '';
            continue;
          }
          $form['list'][$i]['table'][$j][$field] = [
            '#type' => 'textfield',
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
      for ($j = 0; $j < $num_of_rows[$i]; $j++) {
        $form_state->getValue('list')[$i]['table'][$j] = [];
        foreach ($this->fields as $field) {
          $form['list'][$i]['table'][$j][$field]['#value'] = $this->values[$i][$j - 1][$field] ? $this->values[$i][$j - 1][$field] : '';
        }
      }
    }

    $form['list']['actions'] = [
      '#type' => 'actions',
    ];

    $form['list']['actions']['confirm'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send form'),
      '#submit' => ['::confirmForm'],
      '#ajax' => [
        'callback' => '::showStatus',
        'event' => 'click',
        'wrapper' => 'result',
      ],
    ];
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
   */
  public function addMoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['list'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addTable(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_of_tables');
    $add_field = $name_field + 1;
    $form_state->set('num_of_tables', $add_field);
    $this->saveRows($form_state);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addRow(array &$form, FormStateInterface $form_state) {
    $table_id = $form_state->getTriggeringElement()['#name'];
    $table_id = explode(" ", $table_id)[1];
    $num_of_rows = $form_state->get("num_of_rows$table_id");
    $news_rows = $num_of_rows + 1;
    $form_state->set("num_of_rows$table_id", $news_rows);
    $this->saveRows($form_state, $table_id);
    $form_state->setRebuild();
  }

  /**
   * Table data saving function.
   */
  private function saveRows(FormStateInterface $form_state, int $table_id = -1) {
    $num_of_tables = $form_state->get('num_of_tables');
    for ($i = 0; $i < $num_of_tables; $i++) {
      if ($table_id !== $i) {
        $size = count($form_state->getValue('list')[$i]['table']);
        $arr = $form_state->getValue('list')[$i]['table'];
        $temp = [];
        for ($j = 0; $j < $size; $j++) {
          $temp[$j - 1] = $arr[$j];
        }
        $this->values[$i] = $temp;
      }
      else {
        $this->values[$i] = $form_state->getValue('list')[$i]['table'];
      }
    }
  }

  /**
   * Table validation function.
   */
  public function confirmForm(array &$form, FormStateInterface $form_state) {
    $num_of_tables = $form_state->get('num_of_tables');
    for ($i = 0; $i < $num_of_tables; $i++) {
      $num_of_rows[$i] = $form_state->get("num_of_rows$i");
    }

    $start = FALSE;
    $end = FALSE;
    $unset = FALSE;
    $valueRowGroup = [];
    $error = [];

    for ($i = 0; $i < $num_of_tables; $i++) {
      $tmp = $form_state->getValue('list')[$i]['table'];
      $valueRowGroup[$i] = [];
      $this->gapValidation($tmp, $num_of_rows[$i], $valueRowGroup[$i]);
    }
    for ($i = 0; $i < count($valueRowGroup[$i]); $i++) {
      $setCheck = FALSE;
      $endCheck = FALSE;
      $error[$i] = FALSE;
      for ($j = 0; $j < count($valueRowGroup[$i]); $j++) {
        if (!$setCheck && $valueRowGroup[$i][$j] !== '') {
          $setCheck = TRUE;
          if ($i === 0) {
            $start = $j;
          }
          elseif ($start !== FALSE && $start !== $j) {
            $error[$i] = TRUE;
          }
          if ($unset) {
            $error[$i] = TRUE;
          }
          continue;
        }

        if ($setCheck && $valueRowGroup[$i][$j] === '') {
          if ($i === 0 && $end === FALSE) {
            $end = $j;
          }
          elseif ($end !== FALSE && $end !== $j && !$endCheck) {
            $error[$i] = TRUE;
          }
          $endCheck = TRUE;
          continue;
        }

        if ($setCheck && $endCheck && $valueRowGroup[$i][$j] !== '') {
          $error[$i] = TRUE;
        }
      }
      if ($start !== FALSE && $end === FALSE) {
        $end = count($valueRowGroup[$i]) - 1;
      }
      elseif (count($valueRowGroup[$i]) < $end && !$endCheck) {
        $error[$i] = TRUE;
      }
      if ($start === FALSE) {
        $unset = TRUE;
      }
      elseif (!$unset && !$setCheck) {
        $error[$i] = TRUE;
      }
    }
    $form_state->set("error", array_search(TRUE, $error) !== FALSE ? $this->t('Invalid') : $this->t('Valid'));
  }

  /**
   * Function of combining arrays of table data into one array.
   */
  private function gapValidation(array $tmp, int $num_of_rows, &$fields) {
    $reversFields = array_reverse($this->fields);
    for ($j = $num_of_rows - 1; $j >= 0; $j--) {
      foreach ($reversFields as $field) {
        if (!(str_contains($field, 'Q') || str_contains($field, 'YTD'))) {
          array_push($fields, $tmp[$j][$field]['#value']);
        }
      }
    }
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
   *
   *
   *   {@inheritdoc}
   *   Displays information about the save status.
   */
  public function showStatus(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $error = $form_state->get("error");

    if ($error === 'Invalid') {
      $response->addCommand(new MessageCommand($error, '.result', ['type' => 'error']));
    }
    else {
      $response->addCommand(new MessageCommand($error, '.result', ['type' => 'status']));
    }

    return $response;
  }

  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}

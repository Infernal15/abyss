//Author @abyss

Drupal.behaviors.abyss = {

  attach: function (context, settings) {
    (function ($, Drupal) {
      /**
       * Used to check correct insert value to quarter or year input field.
       */
      $('#columns-wrapper td .abyss-quarter input').once().bind('change', function (main) {
        const element = main.target;
        if (!element.dataset?.value) {
          element.value = '';
          return;
        }

        const max = (parseFloat(element.dataset.value) + parseFloat('0.05')).toFixed(2);
        const min = (parseFloat(element.dataset.value) - parseFloat('0.05')).toFixed(2);

        if (element.value < min || element.value > max) {
          const diff = element.value < min
            ? (min - element.value).toFixed(2)
            : (element.value - max).toFixed(2);
          alert(Drupal.t(
            'Value exceeded by @diff, basic value -  \'@val\' deviation available on 0.05',
            {'@diff': diff, '@val': element.dataset.value}
            )
          );
          element.value = element.dataset.value;
        }

        const quarter = $($(main.target).closest('tr')).children('.abyss-quarter');

        if (!main.target.id.includes('ytd')) {
          quarterSet(quarter);
        }
      });

      /**
       * Used to change the quarter value.
       */
      $('.abyss-table .abyss-table-element input').once().bind('change', function (main) {
        const parent = $($(main.target).closest('tr'));
        const elem = parent.children('.abyss-table-element');
        const quarter = parent.children('.abyss-quarter');
        const quarter_count = quarter.length - 1;
        const month_in_quarter = elem.length / quarter_count;
        let temp_array = [];
        let first_quarter_month;
        let second_quarter_month;
        let third_quarter_month;
        let quarter_value;

        for (let i = 0; i < quarter_count; i++) {
          for (let j = 0; j < month_in_quarter; j++) {
            temp_array.push(parseFloat($(elem.get(j + i * 3)).find('input').val()).toFixed(2));
          }

          [first_quarter_month, second_quarter_month, third_quarter_month] = temp_array;
          temp_array = [];

          first_quarter_month = +first_quarter_month || 0;
          second_quarter_month = +second_quarter_month || 0;
          third_quarter_month = +third_quarter_month || 0;

          if (first_quarter_month === 0 && second_quarter_month === 0 && third_quarter_month === 0) {
            $(quarter.get(i)).find('input').val('');
            $(quarter.get(i)).find('input').get(0).dataset.value = '';
          }
          else {
            quarter_value = parseFloat((first_quarter_month + second_quarter_month + third_quarter_month + 1)) / 3;
            $(quarter.get(i)).find('input').val(quarter_value.toFixed(2));
            $(quarter.get(i)).find('input').get(0).dataset.value = quarter_value.toFixed(2);
          }
        }

        quarterSet(quarter);
      });

      /**
       * Used to change the year value.
       *
       * @param quarter
       *   Contained quarter list.
       */
      function quarterSet(quarter) {
        let quarter_value = 0.00;
        let temp;

        for (let i = 0; i < 4; i++) {
          temp = parseFloat($(quarter[i]).find('input').val()).toFixed(2);
          quarter_value += +temp || 0;
        }

        if (quarter_value === 0) {
          $(quarter.get(4)).find('input').val('');
          $(quarter.get(4)).find('input').get(0).dataset.value = '';
        }
        else {
          temp = (quarter_value + 1) / 4;
          $(quarter.get(4)).find('input').val(temp.toFixed(2));
          $(quarter.get(4)).find('input').get(0).dataset.value = temp.toFixed(2);
        }
      }
    }(jQuery, Drupal));
  }
};

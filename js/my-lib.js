//Author @abyss

Drupal.behaviors.abyss = {

  attach: function (context, settings) {

    (function ($, Drupal) {
      $('#columns-wrapper td .abyss-quarter input').once().bind('change', function (main) {
        let element = main.target;
        if (!element.dataset?.value) {
          element.value = '';
          return;
        }

        let max = (parseFloat(element.dataset.value) + parseFloat('0.05')).toFixed(2);
        let min = (parseFloat(element.dataset.value) - parseFloat('0.05')).toFixed(2);

        if (element.value < min || element.value > max) {
          alert(`Значення перевищене на ${element.value < min ?
            (min - element.value).toFixed(2)
            : (element.value - max).toFixed(2)}, ` +
            `базове значення - \'${element.dataset.value}\'\n` +
            `доступне відхилення на 0.05`
          );
          element.value = element.dataset.value;
        }

        let quarter = $($(main.target).closest('tr')).children('.abyss-quarter');

        if (!main.target.id.includes('ytd'))
          quarterSet(quarter);
      });

      $('.abyss-table .abyss-table-element input').once().bind('change', function (main) {
        let parent = $($(main.target).closest('tr'));
        let elem = parent.children('.abyss-table-element');
        let quarter = parent.children('.abyss-quarter');
        let first;
        let second;
        let third;

        for (let i = 0; i < (quarter.length - 1) * 3; i += 3) {
          first = parseFloat($(elem.get(i)).find('input').val()).toFixed(2);
          first = +first || 0;
          second = parseFloat($(elem.get(i + 1)).find('input').val()).toFixed(2);
          second = +second || 0;
          third = parseFloat($(elem.get(i + 2)).find('input').val()).toFixed(2);
          third = +third || 0;
          if (first === 0 && second === 0 && third === 0) {
            $(quarter.get(i/3)).find('input').val('');
            $(quarter.get(i/3)).find('input').get(0).dataset.value = '';
          }
          else {
            let temp = parseFloat((first + second + third + 1)) / 3;
            $(quarter.get(i/3)).find('input').val(temp.toFixed(2));
            $(quarter.get(i/3)).find('input').get(0).dataset.value = temp.toFixed(2);
          }
        }

        quarterSet(quarter);
      });

      function quarterSet(quarter) {
        let quarter_value = 0.00;

        for (let i = 0; i < 4; i++) {
          let temp = parseFloat($(quarter[i]).find('input').val()).toFixed(2);
          quarter_value += +temp || 0;
        }

        if (quarter_value === 0) {
          $(quarter.get(4)).find('input').val('');
          $(quarter.get(4)).find('input').get(0).dataset.value = '';
        }
        else {
          let temp = (quarter_value + 1) / 4;
          $(quarter.get(4)).find('input').val(temp.toFixed(2));
          $(quarter.get(4)).find('input').get(0).dataset.value = temp.toFixed(2);
        }
      }
    }(jQuery, Drupal));
  }
};

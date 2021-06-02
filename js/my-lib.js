//Author @abyss

Drupal.behaviors.abyss = {

  attach: function (context, settings) {

    (function ($, Drupal) {
      $('#columns-wrapper td .abyss-quarter span[class^="field"]').once().bind('click', function (main) {
        let setter = $($(main.target).closest('div.abyss-quarter')).children('input');
        let element = $($(main.target).closest('div.abyss-quarter')).children('input').get(0);
        let point = main.target.textContent;
        let quarter = $($(main.target).closest('tr')).children('.abyss-quarter');
        if (!element.dataset.value)
          return;
        let max = (parseFloat(element.dataset.value) + parseFloat('0.05')).toFixed(2);
        let min = (parseFloat(element.dataset.value) - parseFloat('0.05')).toFixed(2);

        if (point === '+' && element.value < max) {
          let temp = parseFloat(element.value) + parseFloat('0.01');
          setter.val(temp.toFixed(2));
        }
        else if (point === '-' && element.value > min) {
          let temp = parseFloat(element.value) - parseFloat('0.01');
          setter.val(temp.toFixed(2));
        }
        let first = parseFloat($(quarter.get(0)).find('input').val()).toFixed(2);
        first = +first || 0;
        let second = parseFloat($(quarter.get(1)).find('input').val()).toFixed(2);
        second = +second || 0;
        let third = parseFloat($(quarter.get(2)).find('input').val()).toFixed(2);
        third = +third || 0;
        let fourth = parseFloat($(quarter.get(3)).find('input').val()).toFixed(2);
        fourth = +fourth || 0;
        if (first === 0 && second === 0 && third === 0 && fourth === 0) {
          $(quarter.get(4)).find('input').val('');
          $(quarter.get(4)).find('input').get(0).dataset.value = '';
        }
        else {
          let temp = parseFloat((first + second + third + fourth + 1)) / 4;
          $(quarter.get(4)).find('input').val(temp.toFixed(2));
          $(quarter.get(4)).find('input').get(0).dataset.value = temp.toFixed(2);
        }
      });

      $('.abyss-table').once().bind('change', function (main) {
        let parent = $($(main.target).closest('tr'));
        let elem = parent.children('.abyss-table-element');
        let quarter = parent.children('.abyss-quarter');

        for (let i = 0; i < (quarter.length - 1) * 3; i += 3) {
          let first = parseFloat($(elem.get(i)).find('input').val()).toFixed(2);
          first = +first || 0;
          let second = parseFloat($(elem.get(i + 1)).find('input').val()).toFixed(2);
          second = +second || 0;
          let third = parseFloat($(elem.get(i + 2)).find('input').val()).toFixed(2);
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
        let first = parseFloat($(quarter.get(0)).find('input').val()).toFixed(2);
        first = +first || 0;
        let second = parseFloat($(quarter.get(1)).find('input').val()).toFixed(2);
        second = +second || 0;
        let third = parseFloat($(quarter.get(2)).find('input').val()).toFixed(2);
        third = +third || 0;
        let fourth = parseFloat($(quarter.get(3)).find('input').val()).toFixed(2);
        fourth = +fourth || 0;
        if (first === 0 && second === 0 && third === 0 && fourth === 0) {
          $(quarter.get(4)).find('input').val('');
          $(quarter.get(4)).find('input').get(0).dataset.value = '';
        }
        else {
          let temp = parseFloat((first + second + third + fourth + 1)) / 4;
          $(quarter.get(4)).find('input').val(temp.toFixed(2));
          $(quarter.get(4)).find('input').get(0).dataset.value = temp.toFixed(2);
        }
      });
    }(jQuery, Drupal));
  }
};

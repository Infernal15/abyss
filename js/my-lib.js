// //Author @abyss
//
// Client-side validation functions.
//
// Drupal.behaviors.abyss = {
//
//   attach: function (context, settings) {
//
//     (function ($, Drupal) {
//       //Name validate
//       $($('[id*="abyss-form"]').find('[id*="edit-name"]')).on('change', function (param) {
//         let element = $(param.target);
//         if (element.val().length <= 2)
//         {
//           element.attr("placeholder", "Required field");
//           element.css("box-shadow","inset 0 0 13px rgba(228, 106, 106, 0.75)");
//           element.css("border","1px solid #f5c6c6");
//           return 0;
//         }
//         else if (element.val().length > 2)
//         {
//           element.attr("placeholder", "");
//           element.css("box-shadow","");
//           element.css("border","1px solid #e1e1e1");
//           return 1;
//         }
//       });
//
//       //Email validate
//       $($('[id*="abyss-form"]').find('[id*="edit-email"]')).on('change', function (param) {
//         let element = $(param.target);
//         var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
//         var valid = re.test(element.val());
//         if (element.val().length === 0 || !valid) {
//           element.attr("placeholder", "Required field");
//           element.css("box-shadow", "inset 0 0 13px rgba(228, 106, 106, 0.75)");
//           element.css("border", "1px solid #f5c6c6");
//         } else {
//           element.attr("placeholder", "");
//           element.css("box-shadow", "");
//           element.css("border", "1px solid #e1e1e1");
//         }
//       });
//
//       //Phone validate
//       $($('[id*="abyss-form"]').find('[id*="edit-phone"]')).on('change', function (param) {
//         let element = $(param.target);
//         var re = /^\d[\d\(\)\ -]{4,14}\d$/;
//         var valid = re.test(element.val());
//         if (element.val().length === 0 || !valid)
//         {
//           $(element).once().before('<span class="abyss-form-error">Required field</span>');//.css("box-shadow","inset 0 0 13px rgba(228, 106, 106, 0.75)");
//           element.css("border","1px solid #f5c6c6");
//         }
//         else
//         {
//           element.attr("placeholder", "");
//           element.css("box-shadow","");
//           element.css("border","1px solid #e1e1e1");
//         }
//       });
//
//       //Phone validate
//       $($('[id*="abyss-form"]').find('[id*="edit-response"]')).on('change', function (param) {
//         let element = $(param.target);
//         if (element.val().length === 0)
//         {
//           element.attr("placeholder", "Required field");
//           element.css("box-shadow","inset 0 0 13px rgba(228, 106, 106, 0.75)");
//           element.css("border","1px solid #f5c6c6");
//           return 0;
//         }
//         else
//         {
//           element.attr("placeholder", "");
//           element.css("box-shadow","");
//           element.css("border","1px solid #e1e1e1");
//           return 1;
//         }
//       });
//     }(jQuery, Drupal));
//   }
// };

Drupal.behaviors.abyss = {

  attach: function (context, settings) {

    (function ($, Drupal) {
      //
      // $($('[id*="abyss-form"]').find('[id*="edit-delete"]')).on('click', function (param) {
      //   let element = $(param.target);
      //   let route = $(element.parent('[id*="abyss-form"]'));
      //   route = $(route.parent('[id*="drupal-dialog-abyssform"]'));
      //   route = $(route.parent('.ui-dialog'));
      //   route.empty();
      // });
      // // Function to add / remove a style at the touch of a button.
      // $('.abyss-hidden').once().on('click', function (param){
      //   let element = $(param.target);
      //   $($(element.parent()).children('ul')).toggleClass('abyss-active');
      //   element.toggleClass('abyss-button-active');
      // });
      // // Function to remove the style from the button, when you hover over the response.
      // $('.admin-guest').once().on('mouseleave', function (){
      //   let element = $('.admin-guest');
      //   element = $(element.children('.contextual'));
      //   let element_list = $(element.children('ul'));
      //   element_list.removeClass('abyss-active');
      //   $(element.children('button')).removeClass('abyss-button-active');
      // });
      // console.log('2');
      // let list = $($('#abyss-table-form #columns-wrapper').children('fieldset'));
      // console.log(list);
      // list.each(function (index) {
      //   let temp = index.find('tbody');
      //   console.log(temp);
      // });
      // $('#abyss-table-form').once().bind('load', function (main) {
      //   console.log('2');
      //   let list = $($('#abyss-table-form #columns-wrapper').children('fieldset'));
      //   console.log(list);
      //   list.each(function (index) {
      //     let temp = $(index).find('tbody');
      //     console.log(temp);
      //   });
      // });
      $('.wrapper .input-group .input-group-text').once().bind('click', function (main) {
        let setter = $($(main.target).closest('.input-group')).children('input');
        let element = $($(main.target).closest('.input-group')).children('input').get(0);
        let point = main.target.textContent;
        let quarter = $($(main.target).closest('tr')).children('.abyss-quarter');
        console.log(main.target.textContent === '+');
        console.log($(main.target).closest('.input-group'));
        console.log(element.value);
        console.log(setter);
        console.log(element);
        let max = (parseFloat(element.dataset.value) + parseFloat('0.05')).toFixed(2);
        let min = (parseFloat(element.dataset.value) - parseFloat('0.04')).toFixed(2);
        console.log(max);
        console.log(min);
        if (point === '+' && element.value < max) {
          let temp = parseFloat(element.value) + parseFloat('0.01');
          console.log(temp);
          setter.val(temp);
        }
        else if (point === '-' && element.value > min) {
          let temp = parseFloat(element.value) - parseFloat('0.01');
          console.log(temp);
          setter.val(temp);
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

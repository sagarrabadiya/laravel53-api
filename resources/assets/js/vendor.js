require('./bootstrap');

$(function() {
   $('.dropdown').on('shown.bs.dropdown', function() {
       if($(this).find('input').length) {
           $(this).find('input').focus();
       }
   });
});

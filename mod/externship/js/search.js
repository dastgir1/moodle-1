$(document).ready(function () {
    $('#search').on('keyup',function(){
        var searchtext = $(this).val().toLowerCase();
        
        $('#entrytable tr').filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(searchtext) > -1); 
          });
    });
});
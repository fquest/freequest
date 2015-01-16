/**
 * Created by sivashchenko on 12/25/2014.
 */
$('[data-role="event-list-reloader"]').click(function() {
    var searchQuery = $('[data-role="search"]').val();
    if (searchQuery) {
        var input = $('<input type="text" name="query" hidden="hidden">');
        input.val(searchQuery);
        $('#list_filter').append(input);
    }
    $('#list_filter').submit();
});
$('[data-role="search"]').keyup(function(event){
    if(event.keyCode == 13){
        var searchQuery = $('[data-role="search"]').val();
        if (searchQuery) {
            var input = $('<input type="text" name="query" hidden="hidden">');
            input.val(searchQuery);
            $('#list_filter').append(input);
        }
        $('#list_filter').submit();
    }
});
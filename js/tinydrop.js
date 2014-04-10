/**
 * Created by Edo on 09/04/14.
 */
$( document ).ready(function() {
    if ( $('.process').length > 0 ) {
        $('.process').remove();
    }

    $('#checkall').click(function() {
        $('input:checkbox').prop('checked', true);
    });

    $('#uncheckall').click(function() {
        $('input:checkbox').prop('checked', false);
    });
});

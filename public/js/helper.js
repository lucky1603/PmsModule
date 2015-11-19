/**
 * Called when the document load is completed.
 * @param {type} param
 */
$(document).ready(function() {
    // Definition id from entity definitions.
    fill('#definition_id');
    $('#definition_id').on('change load', function(evt) {
       fill('#definition_id');
    });
    
    // Client id from clients.
    fill('#client_id');        
    $('#client_id').on('change load', function(evt) {
        fill('#client_id');
    }); 
    
    $('.reservation-date').datepicker();
});

/**
 * Function which calls ajax depending on id's of the elements
 * and fills other depending elements with content.
 * @param {type} what
 * @returns {undefined}
 */
function fill(what) {
    if(what == '#definition_id')
    {
        var id = $('select#definition_id option:selected').val();
        if(id)
        {
           $.get('/pms/entity-definition/preview/' + id, function(data) {
                $('.attribute-content').html(data); 
            }); 
        }        
    }
    if(what == '#client_id')
    {
        var id = $('select#client_id option:selected').val();
        if(id) 
        {
            $.get('/pms/client/preview/' + id, function(data) {
                $('.client-content').html(data); 
            });    
        }        
    }
}


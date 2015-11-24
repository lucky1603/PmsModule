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
    
    $('#entity_definition_id').on('change', function(evt) {
        var code = $('#entity_definition_id option:selected').val();
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        
        $.get('/pms/ajax/getAvailableRooms?from='+date_from+'&to='+date_to+'&type='+code, function(data) {
            var what = JSON.parse(data);
//            dump(what);

            $('#entity_id').empty();
            for(var i in what)
            {
                $('#entity_id').append($("<option></option>").attr('value', i).text(what[i]));
            }
        });        
    });
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

/**
 * Dumps the contents of an object to the message box.
 * @param {type} obj
 * @returns {undefined}
 */
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);

//    // or, if you wanted to avoid alerts...
//
//    var pre = document.createElement('pre');
//    pre.innerHTML = out;
//    document.body.appendChild(pre)
}


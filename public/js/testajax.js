//function fill(what) {
//    if(what === '#client_id')
//    {
//        var id = $('select#client_id option:selected').val();
//        if(id) 
//        {
//            $.get('/pms/client/preview/' + id, function(data) {
//                $('.client-content').html(data); 
//            });    
//        }        
//    }
//};

$(function() {
    // Client id from clients.f
//    fill('#client_id');        
    $('#client_id').on('change', function() {
        var id = $('select#client_id option:selected').val();
        if(id) 
        {
//            $.get('/pms/client/preview/' + id, function(data) {
//                $('.client-content').html(data); 
//            });    
        }
    }); 
    
    
});




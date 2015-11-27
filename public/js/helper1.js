$(document).ready(function() {
   //fillContent();
   $('#client_id').on('change load', function(evt) {
         fillContent();        
    }); 
});

var fillContent = function() {
    var id = $('select#client_id option:selected').val();
    $.get('/pms/client/preview/' + id, function(data) {
        $('.client-content').html(data); 
    });
};

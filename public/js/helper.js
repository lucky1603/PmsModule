$(document).ready(function() {
   fillContent();
   $('#definition_id').on('change', function(evt) {
         fillContent();        
    }); 
});

var fillContent = function() {
//    var text = $('select option:selected').text();
    var id = $('select option:selected').val();
    $.get('/pms/entity-definition/preview/' + id, function(data) {
        $('.attribute-content').html(data); 
    });
};

/**
 * @description This file handles actions about the update and mangement of value options of a html form select element.
 * @type String
 * @author Sinisa
 */

var optionsArea;
var counter = 1;
var model;

$(document).ready(function() {
    var counter = 1;    
    optionsArea = '<div class="optionsArea"><button id="addButton">Add</button></div>';
    var optionsEntry = '<div><input type="text" id="val"' + counter + '>value here...</input><input type="text" id="val"'+counter+'>text here...</input><button id="removeEntries">remove</button>';
    $('.attr-type').on('change', function(evt) {
        initEntries();
    });

});

function initEntries(attributeModel)
{
    if(attributeModel != null)
    {
        model = attributeModel;
    }
        
    if($('.attr-type').val() == 'select')
    {   
        $('.attr-type').after($(optionsArea));
        $('#addButton').click(function(evt) {
            evt.preventDefault();
            var optionsEntry = '<div class="option-value"><input type="text" name="val'+counter+'" id="val' + counter + '" value="' + this.value + '"><input type="text" name="text'+counter+'" id="text'+counter+'" value="' + this.text + '"><button class="removeEntries" id="btn'+counter+'">remove</button>';
            $('.optionsArea').append(optionsEntry);
            $('.optionsArea div.option-value button.removeEntries[id="btn'+counter+'"]').on('click', function(evt) {
                evt.preventDefault();
                $(evt.target).parent('div').remove();
                //counter--;
                $('#counter').val(--counter);
            });
            $('#counter').val(counter++);
        });

        if(model)
        {
            $.each(model.optionValues, function(idx, val) {                
                var optionsEntry = '<div class="option-value"><input type="text" name="val'+counter+'" id="val' + counter + '" value="' + this.value + '"><input type="text" name="text'+counter+'" id="text'+counter+'" value="' + this.text + '"><button class="removeEntries" id="btn'+counter+'">remove</button>';                
                $('.optionsArea').append(optionsEntry);              
                $('.optionsArea div.option-value button.removeEntries[id="btn'+counter+'"]').on('click', function(evt) {
                    evt.preventDefault();
                    $(evt.target).parent('div').remove();
                    //counter--;
                    $('#counter').val(--counter);
                });
                console.log($('.optionsArea div.option-value button.removeEntries').get(counter - 1));
                //++counter;      
                $('#counter').val(counter++);
            }); 
        }                                   
    }  
    else 
    {
        if(model == null)
        {
            model = {};
        }

        model.optionValues = [];  
        for(var i = 1; i <= $('.option-value').length; i++)
        {
            var idx = $('.option-value input#val' + i).val();
            var text = $('.option-value input#text' + i).val();
            model.optionValues[idx] = {attribute_id:0, id:0, text:text, value:idx};
        }

        $('.optionsArea').remove();
        counter = 1;
        $('#counter').val(counter);
    }    
}


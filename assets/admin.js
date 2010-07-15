window.addEvent('domready', function(){
    
    $$('input.delete').addEvent('click', function(e){
        if (!confirm('Are you sure?')) e.preventDefault();
    });
    
    new DatePicker('.datetime', {
        timePicker: true,
        format: 'd F Y H:i',
        inputOutputFormat: 'd F Y H:i',
        positionOffset: {x:1, y:-1}
    });
    
});

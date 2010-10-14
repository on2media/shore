window.addEvent('domready', function(){
    
    $$('a[href=#]').addEvent('click', function(e){e.preventDefault();});
    
    $$('a[rel=_blank]').removeEvents('click').addEvent('click', function(e){
        e.preventDefault();
        window.open(this.getProperty('href'));
    });
    
    $$('input.delete').addEvent('click', function(e){
        if (!confirm('Are you sure?')) e.preventDefault();
    });
    
    new DatePicker('.datetime', {
        timePicker: true,
        format: 'd F Y H:i',
        inputOutputFormat: 'd F Y H:i',
        positionOffset: {x:1, y:-1}
    });
    
    $$('span[class^=cb_sel_]').each(function(el){
        
        var className = el.getProperty('class');
        
        // not really efficient, but stops multitple events occuring if we have two or more sets of controls
        $$('span.'+className+' a').removeEvents();
        
        $$('span.'+className+' a').addEvent('click', function(e){
            e.preventDefault();
            var checkboxes = $$('input.'+className);
            var inRange = false; var eofRange = false;
            switch (this.getProperty('rel')) {
                
                case 'all':
                    checkboxes.each(function(el){el.setProperty('checked', 'checked');});
                    break;
                
                case 'range':
                    checkboxes.each(function(el){
                        if (inRange == true) {
                            if (el.getProperty('checked')) {
                                inRange = false;
                                eofRange = true;
                            }
                            el.setProperty('checked', 'checked');
                        }
                        if (eofRange == false && inRange == false && el.getProperty('checked')) inRange = true;
                    });
                    
                    inRange = false; eofRange = false;
                    break;
                
                case 'invert':
                    checkboxes.each(function(el){
                        if (el.getProperty('checked')) {
                            el.removeProperty('checked');
                        } else {
                            el.setProperty('checked', 'checked');
                        }
                    });
                    break;
                
                case 'none':
                    checkboxes.each(function(el){el.removeProperty('checked');});
                    break;
                
            }
        });
        
    });    
    
});

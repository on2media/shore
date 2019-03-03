window.addEvent('domready', function(){

    $$('a[href=#]').addEvent('click', function(e){if(e)e.preventDefault();});

    $$('a[rel=_blank]').removeEvents('click').addEvent('click', function(e){
        e.preventDefault();
        window.open(this.getProperty('href'));
    });

    $$('input.delete').addEvent('click', function(e){
        if (!confirm('Are you sure?')) e.preventDefault();
    });

    try {
        new DatePicker('.datetime', {
            timePicker: true,
            format: 'd F Y H:i',
            inputOutputFormat: 'd F Y H:i',
            positionOffset: {x:0, y:-1}
        });
    }catch(e) {}

    try {
        new DatePicker('.date', {
            timePicker: false,
            format: 'd F Y',
            inputOutputFormat: 'd F Y',
            positionOffset: {x:0, y:-1}
        });
    }catch(e) {}

    $$('span[class^=cb_sel_]').each(function(el){

        var className = el.getProperty('class').split(' ')[0];
        var allOrNone = $$('.'+className+' input[type=checkbox]')[0];

        // not really efficient, but stops multitple events occuring if we have two or more sets of controls
        $$('span.'+className+' a').removeEvents();

        if (allOrNone) {

            allOrNone.removeEvents();

            allOrNone.addEvent('change', function(e){

                if (this.getProperty('checked')) {
                    $$('span.'+className+' a[rel=all]')[0].fireEvent('click');
                } else {
                    $$('span.'+className+' a[rel=none]')[0].fireEvent('click');
                }

            });

        }

        $$('span.'+className+' a').addEvent('click', function(e){

            if (e) e.preventDefault();
            var checkboxes = $$('input.'+className);
            var inRange = false; var eofRange = false;

            if (allOrNone) allOrNone.removeProperty('checked');

            switch (this.getProperty('rel')) {

                case 'all':
                    checkboxes.each(function(el){el.setProperty('checked', 'checked');});
                    if (allOrNone) allOrNone.setProperty('checked', 'checked');
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

    $$('.ssl_change').addEvent('click', function(e){

        if (e) e.preventDefault();

        if (this.getProperty('text') == 'Change') {

            this.setProperty('text', 'Cancel');
            this.getParent('.ssl_wrapper').getElement('.ssl_search').setStyle('display', 'block');

         } else {

            this.setProperty('text', 'Change');
            this.getParent('.ssl_wrapper').getElement('.ssl_search').setStyle('display', 'none');

         }

    });

    $$('.ssl_search input').addEvent('keydown', function(e){

        if (e && e.key == 'enter') {
            e.preventDefault();
            this.getNext('.ssl_searchbutton').fireEvent('click');
        }

    });

    $$('.ssl_searchbutton').addEvent('click', function(e){

        if (e) e.preventDefault();

        var wrapper = this.getParent('.ssl_wrapper');

        var searchInput = this.getPrevious('input');

        if (searchInput) {

            var q = searchInput.getProperty('value');
            var name = searchInput.getProperty('name').substr('search__'.length);

            if (q.length < 3) {

                alert('Enter at least 3 characters to search!');

            } else {

                var target = this.getNext('.ssl_searchresult');

                if (target) {

                    target.empty();
                    target.setStyle('display', 'none');

                    var loading = this.getNext('.ssl_loading');
                    if (loading) loading.setProperty('text', 'Please Wait…');

                    new Request.JSON({
                        url: window.location.href,
                        method: 'get',
                        data: {
                            'do': 'sslsearch',
                            'field': name,
                            'search': q
                        },
                        noCache: true,
                        onSuccess: function(response, text){

                            if (loading) loading.empty();

                            var json = new Hash(response || {});

                            if ((json.get('status') == 'OK') && json.get('results') && target) {

                                target.setStyle('display', 'block');

                                var results = new Hash(json.get('results'));

                                results.each(function(value, key){

                                    new Element('label', {
                                        'data-id': key,
                                        'text': value,
                                        'events': {
                                            'click': function(e){

                                                var cb = wrapper.getElement('input[type=checkbox]');
                                                var cite = wrapper.getElement('.ssl_cite');

                                                cb.setProperty('value', this.getProperty('data-id'));
                                                cb.setProperty('checked', 'checked');
                                                cite.setProperty('text', this.getProperty('text'));

                                                target.setStyle('display', 'none');
                                                wrapper.getElement('.ssl_change').fireEvent('click');

                                            }
                                        }
                                    }).inject(target);

                                });

                            }

                        }
                    }).send();

                }

            }

        }

    });

});

window.AutoComplete = Class.create({
    data_url: null,
    field: '',
    results: null,
    onComplete: null,
    cc: 0,
    data: null,
    search_value: '',
    initialize: function (field, config) {
        this.data_url = config.data_url;
        this.field = field;
        if(config.onComplete) {
            this.onComplete = config.onComplete;
        }

        // first of all, check if the field is not already an autocomplete
        //console.log(field.parentNode);
        if ($(field.parentNode).select('.fa-search').length == 0) {


            var container_div = new Element('div');
            container_div.setStyle({float: 'left'});
            $(field.parentNode).insert(container_div);
            container_div.insert(field);

            var search_icon = new Element('i');
            search_icon.addClassName('fa');
            search_icon.addClassName('fa-search');
            search_icon.addClassName('rm-search');
            container_div.insert(search_icon);
        }
        else {
            container_div = $(field.parentNode);
        }


        Event.observe($(field), 'keyup', this.search.bind(this));
        Event.observe($(field), 'blur', this.remove.bind(this));

        var results_div = new Element('div');
        results_div.addClassName('results-div');
        var ul = new Element('ul');
        results_div.insert(ul);
        $(container_div).insert(results_div);

        var clearing = new Element('div');
        clearing.setStyle({clear: 'both'});
        $(container_div.parentNode).insert(clearing);

        this.results = ul;

        //Event.observe($(field), 'change', this.search.bind(this));

    },
    search: function(elem) {
        this.cc++;
        setTimeout(this.doSearch.bind(this), 200);
        this.search_value = elem.target.value;
    },
    remove: function(elem) {
        var tthis = this;
        setTimeout(function() {
            tthis.results.update('');
            tthis.cc = 0;
        }, 200);

    },
    doSearch:function() {
        this.cc--;
        if(this.cc <= 0) {
            var tthis = this;
            new Ajax.Request(this.data_url, {
                parameters: {
                    value: this.search_value
                },
                onSuccess: function(transport) {
                    tthis.results.update('');
                    tthis.data = transport.responseJSON;
                    for (i in transport.responseJSON) {
                        s = transport.responseJSON[i];
                        if (!s.title) continue;
                        var li = new Element('li');
                        li.setAttribute('data-rel', i);
                        li.innerHTML = s.title;
                        tthis.results.insert(li);
                        Event.observe(li, 'click', tthis.select.bind(tthis));
                    }



                }
            });
        }
    },
    select: function(elem) {
        d = this.data[elem.target.getAttribute('data-rel')];
        for (i in d.data) {
            if ($(i)) {
                $(i).value = d.data[i];
            }
        }
        if (this.onComplete) {
            this.onComplete();
        }
    }
});
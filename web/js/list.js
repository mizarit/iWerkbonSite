window.GenericList = Class.create({
    data: [],
    all_data: null,
    search_value: '',
    search_field: null,
    element: 'list-container',
    config: {},
    pages: 0,
    page: 0,
    per_page: 10,
    instance: null,
    data_url: '',
    sort_order: 'asc',
    date_filter: false,

    initialize: function (config) {
        this.element = config.container;
        this.config.headers = config.headers;
        this.config.actions = config.actions;
        this.instance = this;
        this.data_url = config.data_url;
        this.date_filter = config.date_filter;

        per_page = localStorage.getItem('per_page_'+this.element);
        if (per_page) { this.per_page = per_page }

        list_data = JSON.parse(localStorage.getItem('list_data_'+this.element));
        list_data_expires = localStorage.getItem('expire_'+this.element);
        now = Date.now() / 1000 | 0;
        if (!list_data || list_data_expires < (now - 3600)) { // 1 hours
            this.forcedReload();

        }
        else {
            data = list_data;
            this.data = data;
            this.renderList();
        }
    },

    forcedReload: function()
    {
        var tthis = this;
        new Ajax.Request(this.data_url, {
            onSuccess: function (transport) {
                console.log(transport);
                tthis.data = transport.responseJSON.data;
                now = Date.now() / 1000 | 0;
                localStorage.setItem('list_data_'+tthis.element, Object.toJSON(tthis.data));
                localStorage.setItem('expire_'+tthis.element, now);

                if (transport.responseJSON.total > tthis.data.length) {
                    tthis.loadRecursive(transport.responseJSON.limit, 0);
                }

                tthis.renderList();
            }
        });
    },

    loadRecursive: function (limit, run) {
        var tthis = this;
        new Ajax.Request(this.data_url, {
            parameters: {
                offset: run * limit
            },
            onSuccess: function(transport) {
                for (i in transport.responseJSON.data) {
                    if (transport.responseJSON.data.hasOwnProperty(i)) {
                        tthis.data.push(transport.responseJSON.data[i]);
                    }
                }

                now = Date.now() / 1000 | 0;
                localStorage.setItem('list_data_'+tthis.element, Object.toJSON(tthis.data));
                localStorage.setItem('expire_'+tthis.element, now);

                tthis.renderList();
                if (tthis.data.length < transport.responseJSON.total) {
                    tthis.loadRecursive(transport.responseJSON.limit, run + 1);
                }
            }
        });
    },

    initPager: function()
    {
        d = this.data.length % this.per_page;
        pages = Math.ceil(this.data.length / this.per_page);
        this.pages = pages;
    },

    renderList: function()
    {
        this.initPager();

        $(this.element).innerHTML = '';

        search = this.renderSearch();
        $(this.element).insert(search);

        /*
        if (this.date_filter) {
            filter = this.renderDateFilter();
            search.insert(filter);
        }
*/
        //exporto = this.renderExport();
        //$(this.element).insert(exporto);

        pager = this.renderPager();
        $(this.element).insert(pager);

        var breaker = new Element('div');
        breaker.setStyle({clear:'both'});
        $(this.element).insert(breaker);

        table = this.renderTable();
        $(this.element).insert(table);

        page_size = this.renderPageSize();
        $(this.element).insert(page_size);

        pager = this.renderPager();
        $(this.element).insert(pager);

        var breaker = new Element('div');
        breaker.setStyle({clear:'both'});
        $(this.element).insert(breaker);
    },

    renderPager: function ()
    {
        if (this.pages == 1) return;

        var ul = new Element('ul');
        ul.addClassName('pager');
        if (this.page > 0) {
            var li = new Element('li');
            var i = new Element('i');
            i.addClassName('fa fa-angle-double-left');
            li.id = this.element+'-pagx-0';
            $(li).observe('click', this.loadPage.bind(this));
            li.insert(i);
            ul.insert(li);

            var li = new Element('li');
            var i = new Element('i');
            i.addClassName('fa fa-angle-left');
            li.id = this.element+'-pagx-'+(this.page - 1);

            $(li).observe('click', this.loadPage.bind(this));
            li.insert(i);
            ul.insert(li);
        }
        if (this.pages < 10) {
            for (i = 0; i < this.pages; i++) {
                var li = new Element('li');
                li.innerHTML = i + 1;
                if (this.page == i) {
                    li.addClassName('active-item');
                }
                li.id = this.element + '-page-' + (this.page - 1);
                $(li).observe('click', this.loadPage.bind(this));

                ul.insert(li);
            }
        }
        else {
            // more than 10 pages
            s = this.page - 3;
            e = this.page + 3;
            if (s < 0) {
                s = 0;
            }
            if (e > (this.pages - 1)) {
                e = this.pages - 1;
            }
            if (s > 0) {
                var li = new Element('li');
                li.innerHTML = '1';
                li.id = this.element + '-pagy-0';
                $(li).observe('click', this.loadPage.bind(this));

                ul.insert(li);

                var li = new Element('li');
                li.innerHTML = '...';
                li.setStyle({cursor:'default'});
                ul.insert(li);
            }
            for (i = s; i < e; i++) {
                var li = new Element('li');
                li.innerHTML = i + 1;
                if (this.page == i) {
                    li.addClassName('active-item');
                }
                li.id = this.element + '-page-' + i;
                $(li).observe('click', this.loadPage.bind(this));

                ul.insert(li);
            }
            if (e < (this.pages - 1)) {
                var li = new Element('li');
                li.innerHTML = '...';
                li.setStyle({cursor:'default'});
                ul.insert(li);

                var li = new Element('li');
                li.innerHTML = this.pages - 1;
                li.id = this.element + '-pagy-' + (this.pages - 2);
                $(li).observe('click', this.loadPage.bind(this));

                ul.insert(li);
            }
        }
        if ((this.page+1) < (this.pages-1)) {
            var li = new Element('li');
            var i = new Element('i');
            i.addClassName('fa fa-angle-right');
            li.id = this.element+'-pagx-'+(this.page + 1);
            $(li).observe('click', this.loadPage.bind(this));

            li.insert(i);
            ul.insert(li);

            var li = new Element('li');
            var i = new Element('i');
            i.addClassName('fa fa-angle-double-right');
            li.id = this.element+'-pagx-'+(this.pages-2);
            $(li).observe('click', this.loadPage.bind(this));

            li.insert(i);
            ul.insert(li);


        }

        return ul;
    },

    renderExport: function()
    {
        var ul = new Element('ul');
        ul.addClassName('export');
        var li = new Element('li');
        var i = new Element('i');
        i.addClassName('fa fa-file-excel-o');
        li.insert(i);
        ul.insert(li);
        var li = new Element('li');
        var i = new Element('i');
        i.addClassName('fa fa-file-zip-o');
        li.insert(i);
        ul.insert(li);
        return ul;
    },

    renderPageSize: function()
    {
       var div = new Element('div');
        div.innerHTML = 'Regels per pagina: ';
        div.setStyle({float:'right'});
        var select = new Element('select');
        select.id = 'pagesize-'+this.element;
        select.setStyle({width:'4em', 'margin-top':'0.2em'});
        var opt = [10,25,50,100];
        for (i in opt) {
            if (!opt.hasOwnProperty(i)) continue;
            var option = new Element('option');
            option.value = opt[i];
            option.innerHTML = opt[i];
            if (opt[i] == this.per_page) {
                option.selected = 'selected';
            }
            select.insert(option);
        }
        $(select).observe('change', function(event) {
            elem = event.target;

            this.per_page = elem.value;
            localStorage.setItem('per_page_'+this.element, this.per_page);
            this.renderList();
        }.bind(this));

        div.insert(select);
        return div;
    },

    renderSearch: function()
    {
        var div = new Element('div');
        div.addClassName('form-row');
        var input = new Element('input');
        input.addClassName('search');
        input.value = this.search_value;
        this.search_field = input;
        $(input).observe('keyup', this.search.bind(this));
        var i = new Element('i');
        i.addClassName('fa fa-search rm-search');
        div.insert(input);
        div.insert(i);
        return div;
    },

    renderDateFilter: function()
    {
        var container = new Element('div');
        var input = new Element('input');
        input.id = 'list-date-filter';
        container.insert(input);

        return container;
    },
    renderTable: function()
    {
        var table = new Element('table');
        var thead = new Element('thead');
        var tr = new Element('tr');
        c_width = 100 / (this.config.headers.length + 1);

        first = true;
        for (i in this.config.headers) {
            if(this.config.headers.hasOwnProperty(i)) {
                var th = new Element('th');
                th.innerHTML = this.config.headers[i];
                if (!first) {
                    th.setStyle({width: c_width + '%'});
                }
                first = false;
                th.id = 'header-'+this.element+'-'+i;
                $(th).observe('click', this.headerSort.bind(this));
                tr.insert(th);
            }
        }
        var th = new Element('th');
        th.innerHTML = 'Acties';
        w = (1.3 * (Object.values(this.config.actions).length)).toFixed(1);
        th.setStyle({width: w+'em'});
        tr.insert(th);
        thead.insert(tr);
        table.insert(thead);

        var tbody = new Element('tbody');
        offset = this.per_page * this.page;
        for (i = 0; i < this.per_page; i++) {
            row = this.data[offset + i];
            if (!row) continue;

            var tr = new Element('tr');
            for (x = 0; x < this.config.headers.length; x++) {
                var td = new Element('td');
                td.innerHTML = row[x+1]; // skip Id column
                tr.insert(td);
            }
            var td = new Element('td');

            if(this.config.actions.view) {
                eval("Event.observe(tr, 'click', function() { "+this.config.actions['view']+"("+(offset + i)+", "+Object.toJSON(row)+")});");
            }
            for (action in this.config.actions) {
                if (this.config.actions.hasOwnProperty(action)) {
                    var a = new Element('a');
                    var io = new Element('i');
                    io.addClassName('fa');
                    switch(action) {
                        case 'edit':
                            io.addClassName('fa-edit');
                            break;
                        case 'remove':
                            io.addClassName('fa-remove');
                            break;
                        case 'view':
                            io.addClassName('fa-search');
                            break;
                        case 'notes':
                            io.addClassName('fa-comment');
                            break;
                        case 'settings':
                            io.addClassName('fa-wrench');
                            break;
                        case 'photos':
                            io.addClassName('fa-photo');
                            break;
                        case 'documents':
                            io.addClassName('fa-file-pdf-o');
                            break;
                        case 'search':
                            io.addClassName('fa-search');
                            break;
                    }
                    eval("Event.observe(a, 'click', function(event) { "+this.config.actions[action]+"("+(offset + i)+", "+Object.toJSON(row)+"); Event.stop(event); });");
                    a.insert(io);
                    td.insert(a);
                    td.insert('&nbsp;');
                }
            }
            tr.insert(td);
            table.insert(tr);
        }
        return table;
    },

    headerSort: function(event)
    {
        elem = event.target;
        var sort_col = elem.id.substr(8+this.element.length);
        this.sort_order = this.sort_order == 'asc' ? 'desc' : 'asc';
        var sort_order = this.sort_order;

        this.data.sort(function(a,b) {
            v1 = a[sort_col];
            v2 = b[sort_col];
            var test = [v1, v2];
            if(v1==v2) return 0; // they are the same
            test.sort();
            return (test[0]==v1) ? (sort_order=='asc'?-1:1) : (sort_order=='asc'?1:-1);
        });
        this.renderList();
    },

    loadPage: function(event)
    {
        p = event.target.id.substr(19);
        if (isNaN(parseInt(p))) p = 0;
        this.page = parseInt(p);
        this.renderList();
    },

    search: function(event)
    {
        elem = event.target;

        if (!this.all_data) {
            this.all_data = this.data; // make backup of full list
        }
        var v = $(elem).value;

        this.search_value = v;
        if (v.length == 0) {
            this.data = this.all_data;
        }
        else {
            tmp_data = [];
            for (i in this.all_data) {
                var tthis = this;
                if (this.all_data.hasOwnProperty(i)) {
                    row = this.all_data[i];
                    row2 = row.filter(function(v1) {
                        v2 = $(tthis.search_field).value;
                        if(typeof(v1)!='string') return false;
                        return v1.toLowerCase().search(v2.toLowerCase()) != -1;
                    });
                    if (row2.length > 0) {
                        tmp_data.push(row);
                    }
                }
            }
            this.data = tmp_data;
        }

        this.renderList();
        $(this.search_field).focus();
    }
});


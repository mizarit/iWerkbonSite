var list_data = null;
var customer_list = null;
var Customer = 'test';

Event.observe(window, 'load', function() {
    var CustomerObject = Class.create(AdminBase, {
        settings: {
            dataURL: customer_data_url
        },
        new: function() {
            this.renderForm('Nieuwe klant toevoegen', $('customer-form'), {
                dataURL: this.settings.dataURL
            });
        },

        edit: function(row, data, callback) {
            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                title: 'customer-title',
                address: 'customer-address',
                zipcode: 'customer-zipcode',
                city: 'customer-city',
                email: 'customer-email',
                phone: 'customer-phone'
            };

            data.listView = customer_list;
            data.listMap = data.dataMap;

            this.renderForm('Klantgegevens bewerken', $('customer-form'), data);
            if (callback) {
                buttons = $$('#dialog-content .form-buttons').first();

                button = new Element('button');
                button.update('Terug');
                var current_callback = callback;
                var current_row = row;
                var current_data = data;
                Event.observe(button, 'click', function() {
                    current_callback(current_row, current_data);
                });
                buttons.insert({top: button});
            }
        },

        remove: function(row, data) {
            var d_this = this;
            var d_row = row;
            var d_data = data;
            var d_id = data[0];
            this.renderConfirm('Klant verwijderen', 'Weet je zeker dat je deze klant wilt verwijderen? Alle gekoppelde werkbonnen worden hiermee ook verwijderd.', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    d_this.renderAlert('Verwijderen van klant is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    // remove from local copy
                                    // render list again
                                    $(list_data).each(function(s,i) {

                                        if(d_id == s[0]) {
                                            removed = list_data.splice(i,1);
                                            localStorage.setItem('list_data_customer-list', Object.toJSON(list_data));
                                            customer_list.renderList();
                                        }
                                    });
                                    break;
                            }
                        }

                    })
                },
                onCancel: function() {
                }
            });
        },

        view: function(row, data) {

            var d_row = row;
            var d_data = data;

            data.row = row;
            data.dataURL = '/admin/customersData';
            data.dataMap = {
                title: 'customer-view-title',
                address: 'customer-view-address',
                zipcode: 'customer-view-zipcode',
                city: 'customer-view-city',
                email: 'customer-view-email',
                phone: 'customer-view-phone'
            };

            data.customRender = function(data)
            {
                $('customer-workorders').innerHTML = '';
                if (data.workorders.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Status').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Gereed'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('customer-workorders').insert(table);

                    $(data.workorders).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.status));
                        tr.insert(new Element('td').update(row.ready?'Ja':'Nee'));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-search');
                        eval("Event.observe(a, 'click', function() { Customer.showWorkorder("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('customer-workorders').insert(new Element('p').update('Deze klant heeft geen werkbonnen'));
                }

                $('customer-invoices').innerHTML = '';
                if (data.invoices.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Status').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('customer-invoices').insert(table);

                    $(data.invoices).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.status));
                        tr.insert(new Element('td').update(row.total));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-file-pdf-o');
                        eval("Event.observe(a, 'click', function() { Customer.downloadInvoice("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('customer-invoices').insert(new Element('p').update('Deze klant heeft geen facturen'));
                }

                $('customer-notes').innerHTML = '';
                if (data.notes.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'120px'}));
                    tr.insert(new Element('th').update('Notitie'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'2.6em'}));
                    table.insert(tr);
                    $('customer-notes').insert(table);

                    $(data.notes).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.note));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        eval("Event.observe(a, 'click', function() { Customer.editNote("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        eval("Event.observe(a, 'click', function() { Customer.removeNote("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('customer-notes').insert(new Element('p').update('Deze klant heeft geen notities'));
                }


                $('customer-photos').innerHTML = '';
                if (data.photos.length > 0) {

                    var monthNames = ['januari', 'februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
                    var ul = new Element('ul');
                    $('customer-photos').insert(ul);
                    $(data.photos).each(function(row, i){
                        d = new Date(row.date);
                        dstr = d.getDate()+' '+ monthNames[d.getMonth()]+' '+ d.getFullYear();
                        var a = new Element('a');
                        a.addClassName('lightwindow');
                        a.writeAttribute('href', row.path);
                        a.writeAttribute('params', 'lightwindow_width=800,lightwindow_height=600');
                        a.writeAttribute('rel' , 'Datum['+dstr+']');
                        a.writeAttribute('caption', 'Situatiefoto op '+dstr)
                        a.writeAttribute('title', 'Situatiefoto op '+dstr)
                        var li = new Element('li');
                        var img = new Element('img');
                        img.src = row.thumb;
                        var remove = new Element('span');
                        remove.writeAttribute('remove-id', i);
                        remove.addClassName('fa fa-remove');
                        Event.observe(remove, 'click', Customer.removePhoto);
                        li.insert(remove);
                        ul.insert(li.insert(a.insert(img)));
                    });

                    myLightWindow = new lightwindow();
                }

                Event.observe($('customer-edit-link'), 'click', function() {
                    Customer.edit(d_row, d_data, Customer.view);
                });

                Event.observe($('customer-add-note'), 'click', function() {
                    Customer.addNote();
                });
            }

            data.onEdit = function() {
                Customer.edit(d_row, d_data, Customer.view);
            }
            Customer.renderView('Klantgegevens', $('customer-view'), data);
        },

        removePhoto: function(elem)
        {
            var target = $(elem.target);
            Customer.renderConfirm('Foto verwijderen', 'Weet je zeker dat je deze foto wilt verwijderen?', {
                onConfirm: function () {
                    new Ajax.Request('/admin/customersData', {
                        parameters: {
                            form: 'photo',
                            method: 'delete',
                            customer_id: 5, // TODO
                            id: target.readAttribute('remove-id')
                        },
                        onSuccess: function(transport) {
                            $('customer-photos').innerHTML = '';
                            if (transport.photos.length > 0) {

                                var monthNames = ['januari', 'februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
                                var ul = new Element('ul');
                                $('customer-photos').insert(ul);
                                $(transport.photos).each(function(row, i){
                                    d = new Date(row.date);
                                    dstr = d.getDate()+' '+ monthNames[d.getMonth()]+' '+ d.getFullYear();
                                    var a = new Element('a');
                                    a.addClassName('lightwindow');
                                    a.writeAttribute('href', row.path);
                                    a.writeAttribute('params', 'lightwindow_width=800,lightwindow_height=600');
                                    a.writeAttribute('rel' , 'Datum['+dstr+']');
                                    a.writeAttribute('caption', 'Situatiefoto op '+dstr)
                                    a.writeAttribute('title', 'Situatiefoto op '+dstr)
                                    var li = new Element('li');
                                    var img = new Element('img');
                                    img.src = row.thumb;
                                    var remove = new Element('span');
                                    remove.writeAttribute('remove-id', i);
                                    remove.addClassName('fa fa-remove');
                                    Event.observe(remove, 'click', Customer.removePhoto);
                                    li.insert(remove);
                                    ul.insert(li.insert(a.insert(img)));
                                });

                                myLightWindow = new lightwindow();

                                $('modal').addClassName('active');
                            }
                        }
                    });
                },
                onCancel: function () {
                }
            });
        },

        removeNote: function(which) {
            var current_note = which;
            Customer.renderConfirm('Notitie verwijderen', 'Weet je zeker dat je deze klant notitie wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/customersData', {
                        parameters: {
                            form: 'note',
                            method: 'delete',
                            id: current_note
                        },
                        onSuccess: function(transport) {
                            Customer.renderAlert('De notitie is verwijderd.');
                        }
                    });
                },
                onCancel: function() {

                }
            });
        },

        addNote: function() {
            Customer.renderMicroedit('Notitie toevoegen', 'microedit-note', {
                onSave: function()
                {
                    Customer.saveNote(true);
                }
            });

            new MY.DatePicker({
                input: 'note-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });
        },

        editNote: function(which) {
            var current_row = which;

            invoice.renderMicroedit('Notitie bewerken', 'microedit-note', {
                onSave: function()
                {
                    invoice.saveNote(current_row);
                },
                dataURL: '/admin/customersData?form=note&method=load',
                dataMap: {
                    date: 'note-date',
                    text: 'note-text'
                },
                0: current_row
            });
        },

        saveNote: function(isNew)
        {
            new Ajax.Request('/admin/customersData', {
                parameters: {
                    form: 'note',
                    method: 'save',
                    id: isNew ? false : current_row,
                    customer_id: 5, // TODO
                    date: $('note-date').value,
                    text: $('note-text').value
                },
                onSuccess: function(transport) {

                    console.log(transport.responseJSON.status);
                    switch(transport.responseJSON.status) {
                        case 'success':
                            Customer.renderAlert('De notitie is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                Customer.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
                                $('modal-micro').removeClassName('active');
                            }
                            break;
                    }


                },
                onFailure: function() {
                    $('modal-micro').removeClassName('active');
                }
            });
        },

        showWorkorder: function(which) {
            window.location.href = '/admin/workorders?detail='+which;
        },

        downloadInvoice: function(which) {
            window.location.href = '/admin/workorders?download='+which;
        }
    });

    Customer = new CustomerObject({});

    customer_list = new GenericList({
        container: 'customer-list',
        data_url: customer_ajax_url,
        headers: [
        'Naam',
        'Adres',
        'Postcode',
        'Plaats',
        'Email',
        'Telefoon'
    ],
        actions: {
        'edit': 'Customer.edit',
            'remove': 'Customer.remove',
            'view': 'Customer.view'
        //'notes': 'Customer.notes',
        //'settings': 'Customer.settings',
        //'photos': 'Customer.photos',
        //'documents': 'Customer.documents'
    }
});

    Event.observe($('customer-add-link'), 'click', function() { Customer.new(); } );




});
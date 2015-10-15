var list_data = null;
var invoice_list = null;
var Invoice = 'test';

Event.observe(window, 'load', function() {
    var InvoiceObject = Class.create(AdminBase, {
        settings: {
            dataURL: invoice_data_url
        },
        new: function() {
            this.renderForm('Nieuwe factuur toevoegen', $('invoice-form'), {
                dataURL: this.settings.dataURL
            });
        },

        edit: function(row, data) {
            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                status: 'invoice-status'
            };

            data.listView = invoice_list;
            data.listMap = data.dataMap;

            data.customRender = function(data)
            {

                $('invoice-payments').innerHTML = '';
                if (data.payments.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Betaalmethode'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('invoice-payments').insert(table);

                    $(data.payments).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));
                        tr.insert(new Element('td').update(row.paymethod));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        eval("Event.observe(a, 'click', function() { invoice.editPayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        eval("Event.observe(a, 'click', function() { invoice.removePayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('invoice-payments').insert(new Element('p').update('Deze factuur heeft geen betalingen.'));
                }
            }

            this.renderForm('Factuur bewerken', $('invoice-form'), data);

            Event.observe($('payment-add-link'), 'click', function() {
                invoice.addPayment();
            });

        },

        remove: function(row, data) {
            var d_this = this;
            var d_row = row;
            var d_data = data;
            var d_id = data[0];
            this.renderConfirm('Factuur verwijderen', 'Weet je zeker dat je deze factuur wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    d_this.renderAlert('Verwijderen van factuur is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    // remove from local copy
                                    // render list again
                                    $(list_data).each(function(s,i) {

                                        if(d_id == s[0]) {
                                            removed = list_data.splice(i,1);
                                            localStorage.setItem('list_data_invoice-list', Object.toJSON(list_data));
                                            invoice_list.renderList();
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
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                statusstr: 'invoice-view-status',
                total: 'invoice-view-total',
                no: 'invoice-view-no',
                date: 'invoice-view-date',
                title: 'invoice-view-title',
                address: 'invoice-view-address',
                zipcode: 'invoice-view-zipcode',
                city: 'invoice-view-city'
            };

            data.customRender = function(data)
            {
                $('invoice-orderrows').update('');
                if (data.orderrows.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Omschrijving'));
                    tr.insert(new Element('th').update('Aantal').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Prijs').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal').setStyle({width:'220px'}));
                    table.insert(tr);
                    $('invoice-orderrows').insert(table);

                    $(data.orderrows).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.description));
                        tr.insert(new Element('td').update(row.amount));
                        tr.insert(new Element('td').update(row.price));
                        tr.insert(new Element('td').update(row.total));

                        table.insert(tr);
                    });
                }
                else {
                    $('invoice-orderrows').insert(new Element('p').update('Deze factuur heeft geen orderregels'));
                }
                Event.observe($('invoice-edit-link'), 'click', function() {
                    invoice.edit(d_row, d_data, invoice.view);
                });

                $('invoice-payments').innerHTML = '';
                if (data.payments.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Betaalmethode'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('invoice-payments').insert(table);

                    $(data.payments).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));
                        tr.insert(new Element('td').update(row.paymethod));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        eval("Event.observe(a, 'click', function() { invoice.editPayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        eval("Event.observe(a, 'click', function() { invoice.removePayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('invoice-payments').insert(new Element('p').update('Deze factuur heeft geen betalingen.'));
                }

                Event.observe($('payment-add-link'), 'click', function() {
                    invoice.addPayment();
                });
            }

            data.onEdit = function() {
                invoice.edit(d_row, d_data, invoice.view);
            }
            this.renderView('Factuur details', $('invoice-view'), data);

            var current_row = row;
            var current_data = data;

            var has_download = false;
            $$('#dialog-content .form-buttons button').each(function(s) {
                if(s.innerHTML=='Download factuur') {
                    has_download = true;
                }
            })

            if (!has_download) {
                button = new Element('button');
                Event.observe(button, 'click', function () {
                    invoice.downloadInvoice(current_row, current_data);
                });
                button.addClassName('button-4');
                button.update('Download factuur');
                buttons = $$('#dialog-content .form-buttons').first();
                buttons.insert({top: button});
            }

        },

        downloadInvoice: function(row, data) {
            window.location.href = '/admin/workorders?download='+data[0];
        },

        addPayment: function()
        {
            invoice.renderMicroedit('Betaling toevoegen', 'microedit-payment', {
                onSave: function()
                {
                    invoice.savePayment(true);
                }
            });

            new MY.DatePicker({
                input: 'payment-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });
        },

        removePayment: function(which) {
            var current_row = which;
            invoice.renderConfirm('Betaling verwijderen', 'Weet je zeker dat je deze betaling wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/adminData', {
                        parameters: {
                            form: 'payment',
                            method: 'delete',
                            id: current_row
                        },
                        onSuccess: function(transport) {
                            invoice.renderAlert('De betaling is verwijderd.');
                            //Settings.renderProducts(transport.responseJSON.products);
                        }
                    });
                },
                onCancel: function() {

                }
            });
        },

        editPayment: function(which) {
            var current_row = which;

            invoice.renderMicroedit('Betaling bewerken', 'microedit-payment', {
                onSave: function()
                {
                    invoice.savePayment(product_id);
                },
                dataURL: '/admin/adminData?form=payment&method=load',
                dataMap: {
                    date: 'payment-date',
                    total: 'payment-total',
                    paymethod: 'payment-paymethod'
                },
                0: current_row
            });

            new MY.DatePicker({
                input: 'payment-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });
        },

        savePayment: function(isNew)
        {
            new Ajax.Request('/admin/adminData', {
                parameters: {
                    form: 'payment',
                    method: 'save',
                    id: isNew ? false : current_row,
                    invoice_id: 1, // TODO
                    date: $('payment-date').value,
                    total: $('payment-total').value,
                    paymethod: $('payment-paymethod').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            invoice.renderAlert('De betaling is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            //Settings.renderProducts(transport.responseJSON.products);
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                invoice.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
                                $('modal-micro').removeClassName('active');
                            }
                            break;
                    }


                },
                onFailure: function() {
                    $('modal-micro').removeClassName('active');
                }
            });
            //alert('save payment '+row_id);
            //$('modal-micro').removeClassName('active');
        }
    });

    invoice = new InvoiceObject({});

    var InvoiceList = Class.create(GenericList, {
        filterDate: function(date, dataCol) {
            elem = event.target;

            if (!this.all_data) {
                this.all_data = this.data; // make backup of full list
            }

            tmp_data = [];
            var current_dataCol = dataCol;
            var current_date = date;

            for (i in this.all_data) {
                var tthis = this;
                if (this.all_data.hasOwnProperty(i)) {
                    row = this.all_data[i];
                    date = row[current_dataCol];
                    if(date==current_date) {
                        tmp_data.push(row);
                    }
                }
            }
            this.data = tmp_data;
            this.renderList();
        }
    });


    invoice_list = new InvoiceList({
        container: 'invoice-list',
        data_url: invoice_ajax_url,
        headers: [
            'Datum',
            'Totaalbedrag',
            'Status'
        ],
        actions: {
            'edit': 'invoice.edit',
            //'remove': 'invoice.remove',
            'view': 'invoice.view',
            'documents': 'invoice.downloadInvoice'
        }
    });
});
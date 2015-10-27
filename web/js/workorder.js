var list_data = null;
var workorder_list = null;
var current_list = null;
var workorder = 'test';

Event.observe(window, 'load', function() {
    var workorderObject = Class.create(AdminBase, {
        current_invoice: null,
        current_workorder: null,
        current_row: null,
        current_data: null,
        settings: {
            dataURL: workorder_data_url
        },
        new: function() {
            /*
            this.renderForm('Nieuwe werkbon toevoegen', $('workorder-form'), {
                dataURL: this.settings.dataURL,
                customRender: function(data)
                {
                    new MY.DatePicker({
                        input: 'workorder-date',
                        format: 'dd-MM-yyyy',
                        showWeek: true
                    });
                }
            });
            */
            window.location.href = '/admin/planboard?method=new';

        },

        edit: function(row, data, callback) {
            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                status: 'workorder-status',
                date: 'workorder-date',
                remarks: 'workorder-remarks',
                ready: 'workorder-ready'
            };
            $$('.extra-field').each(function(s,i) {
                data.dataMap['extra_'+s.id.substr(16)] = s.id
            });

            data.listView = workorder_list;
            data.listMap = data.dataMap;

            data.customRender = function(data)
            {
                new MY.DatePicker({
                    input: 'workorder-date',
                    format: 'dd-MM-yyyy',
                    showWeek: true
                });
                if (data.extra_fields) {
                    for (i in data.extra_fields) {
                        if ($('workorder-extra-'+i)) {
                            $('workorder-extra-' + i).value = data.extra_fields[i];
                        }
                    }
                }
            }
            this.renderForm('Werkbon details bewerken', $('workorder-form'), data);
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
            this.renderConfirm('Werkbon verwijderen', 'Weet je zeker dat je deze werkbon wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    d_this.renderAlert('Verwijderen van werkbon is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    // remove from local copy
                                    // render list again
                                    $(list_data).each(function(s,i) {

                                        if(d_id == s[0]) {
                                            removed = list_data.splice(i,1);
                                            localStorage.setItem('list_data_workorder-list', Object.toJSON(list_data));
                                            workorder_list.renderList();
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

            this.current_row = row;
            this.current_data = data;

            var d_row = row;
            var d_data = data;

            workorder.current_workorder = data[0];

            data.row = row;
            data.dataURL = workorder.settings.dataURL;
            data.dataMap = {
                status: 'workorder-view-status',
                date: 'workorder-view-date',
                resource_name: 'workorder-view-resource',
                remarks: 'workorder-view-remarks',
                signature: 'workorder-view-signature',
                ready: 'workorder-view-ready'
            };
            $$('.extra-field').each(function(s,i) {
                data.dataMap['extra_'+s.id.substr(18)] = s.id
            });

            data.customRender = function(data)
            {
                if (data.extra_fields) {
                    for (i in data.extra_fields) {
                        if ($('workorder-view-extra-'+i)) {
                            $('workorder-view-extra-' + i).innerHTML = data.extra_fields[i];
                        }
                    }
                }

                workorder.currentData = data;

                statusses = {
                  started: 'Gestart',
                  success: 'Afgerond',
                  cancelled: 'Geannuleerd',
                  scheduled: 'Ingepland'
                };
                $('workorder-view-status').update(statusses[data.status]);
                $('customer-view-title').update(data.name);
                $('customer-view-address').update(data.address);
                $('customer-view-zipcode').update(data.zipcode);
                $('customer-view-city').update(data.city);
                $('customer-view-title').update(data.name);
                $('customer-view-title').update(data.name);
                $('customer-view-email').update(data.email);
                $('customer-view-phone').update(data.phone);
                $('workorder-invoices').innerHTML = '';

                if (data.checklist.length > 0) {
                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Controlelijst').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Controlepunt'));
                    tr.insert(new Element('th').update('Afgevinkt').setStyle({width:'220px'}));
                    table.insert(tr);
                    $('workorder-checklist').insert(table);
                    $(data.checklist).each(function(row) {
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.checklist));
                        tr.insert(new Element('td').update(row.row));
                        tr.insert(new Element('td').update(row.checked));
                        table.insert(tr);
                    });
                }
                else {
                    $('workorder-checklist').insert(new Element('p').update('Deze werkbon heeft geen controlepunten'));
                }

                if (data.invoices.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('workorder-invoices').insert(table);

                    $(data.invoices).each(function(row){
                        workorder.current_invoice = row.id;
                        var tr = new Element('tr');
                        eval("Event.observe(tr, 'click', function() { workorder.downloadInvoice("+row.id+"); });");
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-file-pdf-o');
                        io.setAttribute('title', 'PDF downloaden');
                        eval("Event.observe(a, 'click', function() { workorder.downloadInvoice("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('workorder-invoices').insert(new Element('p').update('Deze werkbon heeft geen factuur'));
                }

                $('workorder-orderrows').innerHTML = '';
                if (data.orderrows.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Aantal').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Omschrijving'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('workorder-orderrows').insert(table);

                    $(data.orderrows).each(function(row, i){
                        var tr = new Element('tr');
                        eval("Event.observe(tr, 'click', function(event) { workorder.editRow("+i+"); Event.stop(event); }); ");
                        tr.insert(new Element('td').update(row.c));
                        tr.insert(new Element('td').update(row.d));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        io.setAttribute('title', 'Bewerken');
                        eval("Event.observe(a, 'click', function(event) { workorder.editRow("+i+"); Event.stop(event); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        io.setAttribute('title', 'Verwijderen');
                        eval("Event.observe(a, 'click', function(event) { workorder.removeRow("+i+"); Event.stop(event); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('workorder-orderrows').insert(new Element('p').update('Deze werkbon heeft geen orderregels'));
                }


                $('workorder-payments').innerHTML = '';
                if (data.payments.length > 0) {

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Totaal').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Betaalmethode'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('workorder-payments').insert(table);

                    $(data.payments).each(function(row){
                        var tr = new Element('tr');
                        eval("Event.observe(tr, 'click', function(event) { workorder.editPayment("+row.id+"); Event.stop(event);});");
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));
                        tr.insert(new Element('td').update(row.paymethod));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a1 = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        io.setAttribute('title', 'Bewerken');
                        eval("Event.observe(a1, 'click', function(event) { workorder.editPayment("+row.id+"); Event.stop(event);});");
                        a1.insert(io);
                        td.insert(a1);
                        td.insert('&nbsp;');

                        var a2 = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        io.setAttribute('title', 'Verwijderen');
                        eval("Event.observe(a2, 'click', function(event) { workorder.removePayment("+row.id+"); Event.stop(event);});");
                        a2.insert(io);
                        td.insert(a2);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('workorder-payments').insert(new Element('p').update('Deze werkbon heeft geen betaling'));
                }



                Event.observe($('workorder-add-link'), 'click', function() {
                    workorder.addRow();
                });

                Event.observe($('workorder-edit-link'), 'click', function() {
                    workorder.edit(d_row, d_data, workorder.view);
                });
                Event.observe($('customer-edit-link'), 'click', function() {
                    workorder.showCustomer(data.customer_id);
                });

                Event.observe($('payment-edit-link'), 'click', function() {
                    workorder.addPayment();
                });

                Event.observe($('export-btn'), 'click', function() {
                    workorder.export();
                });

                Event.observe($('print-btn'), 'click', function() {
                    workorder.print();
                });

                Event.observe($('download-btn'), 'click', function() {
                    workorder.download();
                });

                $('workorder-photos').innerHTML = '';
                if (data.photos.length > 0) {

                    var monthNames = ['januari', 'februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
                    var ul = new Element('ul');
                    $('workorder-photos').insert(ul);
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
                        remove.setAttribute('title', 'Verwijderen');
                        Event.observe(remove, 'click', workorder.removePhoto);
                        li.insert(remove);
                        ul.insert(li.insert(a.insert(img)));
                    });

                    myLightWindow = new lightwindow();
                }
            }

            data.onEdit = function() {
                workorder.edit(d_row, d_data, workorder.view);
            }
            workorder.renderView('Werkbon', $('workorder-view'), data);
        },
        export: function()
        {
            window.location.href = '/admin/workorders?export='+workorder.current_workorder;
        },
        print: function()
        {
            window.open('/admin/workorders?print='+workorder.current_workorder);
        },
        download: function()
        {
            window.location.href = '/admin/workorders?download='+workorder.current_workorder;
        },
        /*
        removePhoto: function(elem)
        {
            workorder.renderConfirm('Foto verwijderen', 'Weet je zeker dat je deze foto wilt verwijderen?', {
                onConfirm: function () {
                    console.log($(elem.target).readAttribute('remove-id'));
                    alert('todo');
                },
                onCancel: function () {
                }
            });
        },*/

        removePhoto: function(elem)
        {
            var target = $(elem.target);
            workorder.renderConfirm('Foto verwijderen', 'Weet je zeker dat je deze foto wilt verwijderen?', {
                onConfirm: function () {
                    new Ajax.Request('/admin/workordersData', {
                        parameters: {
                            form: 'photo',
                            method: 'delete',
                            workorder_id: workorder.current_data[0],
                            id: target.readAttribute('remove-id')
                        },
                        onSuccess: function(transport) {
                            $('workorder-photos').innerHTML = '';
                            if (transport.responseJSON.photos.length > 0) {
                                var monthNames = ['januari', 'februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
                                var ul = new Element('ul');
                                $('workorder-photos').insert(ul);
                                $(transport.responseJSON.photos).each(function(row, i){
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
                                    Event.observe(remove, 'click', workorder.removePhoto);
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


        showCustomer: function(which) {
            window.location.href = '/admin/customers?detail='+which;
        },

        downloadInvoice: function(which) {
            window.location.href = '/admin/workorders?download-invoice='+which;
        },

        addRow: function()
        {
            workorder.renderMicroedit('Orderregel toevoegen', 'microedit-orderrow', {
                onSave: function()
                {
                    workorder.saveRow(false);
                }
            });

            workorder.initFormElements();
        },

        initFormElements: function()
        {
            Event.observe($('orderrow-type'), 'change', function() {
                workorder.setOrderrowType($('orderrow-type').value);
            });

            workorder.setOrderrowType($('orderrow-type').value);

            currency.initField($('orderrow-price'));

            var ac1 = new AutoComplete($('orderrow-description'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=products',
                onComplete: function() {
                    workorder.setOrderrowType($('orderrow-type').value);
                    $('orderrow-price').value = accounting.formatMoney($('orderrow-price').value, "", 2, ".", ",");
                }
            });
        },

        setOrderrowType: function(type)
        {
            switch(type) {
                case 'product':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').hide();
                    $('orderrow-amount-container').show();
                    break;
                case 'service':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').hide();
                    $('orderrow-amount-container').show();
                    break;
                case 'hours':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').show();
                    $('orderrow-amount-container').hide();
                    break;
            }
        },

        removeRow: function(which) {
            var current_row = which;
            workorder.renderConfirm('Orderregel verwijderen', 'Weet je zeker dat je deze orderregel wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/workordersData', {
                        parameters: {
                            form: 'orderrow',
                            method: 'delete',
                            id: current_row,
                            workorder_id: workorder.current_workorder

                        },
                        onSuccess: function (transport) {

                            switch (transport.responseJSON.status) {
                                case 'success':
                                    workorder.renderAlert('De orderregel is verwijderd.');
                                    $('modal-micro').removeClassName('active');
                                    workorder.view(workorder.current_row, workorder.current_data);
                                    break;
                            }
                        }
                    });
                },
                onCancel: function() {

                }
            });
        },

        editRow: function(which) {
            var current_row = which;
            workorder.renderMicroedit('Orderregel bewerken', 'microedit-orderrow', {
                onSave: function()
                {
                    workorder.saveRow(current_row);
                }
            });

            d = workorder.currentData.orderrows[which];
            $('orderrow-description').value = d.d;
            $('orderrow-price').value = d.p;
            $('orderrow-duration').value = d.c;
            $('orderrow-amount').value = d.c;
            $('orderrow-type').value = d.t;

            workorder.initFormElements();


        },

        saveRow: function(row_id)
        {
            //alert('save row '+row_id);
            //$('modal-micro').removeClassName('active');

            new Ajax.Request('/admin/workordersData', {
                parameters: {
                    form: 'orderrow',
                    method: 'save',
                    id: row_id,
                    workorder_id: workorder.current_workorder,
                    description: $('orderrow-description').value,
                    price: $('orderrow-price').value,
                    duration: $('orderrow-duration').value,
                    amount: $('orderrow-amount').value,
                    type: $('orderrow-type').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            workorder.renderAlert(row_id?'De orderregel is gewijzigd.':'De orderregel is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            workorder.view(workorder.current_row, workorder.current_data);
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                workorder.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
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

        addPayment: function()
        {
            workorder.renderMicroedit('Betaling toevoegen', 'microedit-payment', {
                onSave: function()
                {
                    workorder.savePayment(false);
                }
            });

            new MY.DatePicker({
                input: 'payment-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });

            currency.initField($('payment-total'));
        },

        removePayment: function(which) {
            var current_row = which;
            console.log('remove payment');
            workorder.renderConfirm('Betaling verwijderen', 'Weet je zeker dat je deze betaling wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/workordersData', {
                        parameters: {
                            form: 'payment',
                            method: 'delete',
                            id: current_row,
                            invoice_id: workorder.current_invoice

                        },
                        onSuccess: function (transport) {

                            switch (transport.responseJSON.status) {
                                case 'success':
                                    workorder.renderAlert('De betaling is verwijderd.');
                                    $('modal-micro').removeClassName('active');
                                    workorder.view(workorder.current_row, workorder.current_data);
                                    break;
                            }
                        }
                    });
                },
                onCancel: function() {

                }
            });
        },

        editPayment: function(which) {
            console.log('edit payment');
            var current_row = which;
            workorder.renderMicroedit('Betaling bewerken', 'microedit-payment', {
                onSave: function()
                {
                    workorder.savePayment(current_row);
                },
                customRender: function(response)
                {
                    $('payment-total').value = accounting.formatMoney($('payment-total').value, "", 2, ".", ",");
                }
            });

            for ( i in workorder.currentData.payments) {
                if (workorder.currentData.payments.hasOwnProperty(i)) {
                    if(workorder.currentData.payments[i].id == which) {
                        d = workorder.currentData.payments[i];

                        $('payment-total').value = d.totalv;
                        $('payment-paymethod').value = d.paymethodv;
                        $('payment-date').value = d.date;
                    }
                }
            }

            new MY.DatePicker({
                input: 'payment-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });

            currency.initField($('payment-total'));
        },

        savePayment: function(row_id)
        {
            new Ajax.Request('/admin/workordersData', {
                parameters: {
                    form: 'payment',
                    method: 'save',
                    id: row_id,
                    invoice_id: workorder.current_invoice,
                    date: $('payment-date').value,
                    total: $('payment-total').value,
                    paymethod: $('payment-paymethod').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            workorder.renderAlert('De betaling is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            workorder.view(workorder.current_row, workorder.current_data);
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                workorder.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
                                $('modal-micro').removeClassName('active');
                            }
                            break;
                    }


                },
                onFailure: function() {
                    $('modal-micro').removeClassName('active');
                }
            });
        }
    });

    workorder = new workorderObject({});

    var WorkorderList = Class.create(GenericList, {
      filterDate: function(date, dataCol) {
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

    workorder_list = new WorkorderList({
        container: 'workorder-list',
        data_url: workorder_ajax_url,
        date_filter: true,
        headers: [
            'Klant',
            'Adres',
            'Datum',
            'Medewerker',
            'Opmerkingen',
            'Gereed'
        ],
        actions: {
            'edit': 'workorder.edit',
            'view': 'workorder.view',
            'remove': 'workorder.remove'
        }
    });

    Event.observe($('workorder-add-link'), 'click', function() { workorder.new(); } );
});

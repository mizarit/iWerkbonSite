var list_data = null;
var workorder_list = null;
var current_list = null;
var workorder = 'test';

Event.observe(window, 'load', function() {
    var workorderObject = Class.create(AdminBase, {
        settings: {
            dataURL: workorder_data_url
        },
        new: function() {
            this.renderForm('Nieuwe werkbon toevoegen', $('workorder-form'), {
                dataURL: this.settings.dataURL
            });
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

            data.listView = workorder_list;
            data.listMap = data.dataMap;

            data.customRender = function(data)
            {
                new MY.DatePicker({
                    input: 'workorder-date',
                    format: 'dd-MM-yyyy',
                    showWeek: true
                });
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

            var d_row = row;
            var d_data = data;

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

            data.customRender = function(data)
            {
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
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-file-pdf-o');
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
                        tr.insert(new Element('td').update(row.c));
                        tr.insert(new Element('td').update(row.d));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        eval("Event.observe(a, 'click', function() { workorder.editRow("+i+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        eval("Event.observe(a, 'click', function() { workorder.removeRow("+i+"); });");
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
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.total));
                        tr.insert(new Element('td').update(row.paymethod));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        eval("Event.observe(a, 'click', function() { workorder.editPayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        eval("Event.observe(a, 'click', function() { workorder.removePayment("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
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

        removePhoto: function(elem)
        {
            workorder.renderConfirm('Foto verwijderen', 'Weet je zeker dat je deze foto wilt verwijderen?', {
                onConfirm: function () {
                    console.log($(elem.target).readAttribute('remove-id'));
                },
                onCancel: function () {
                }
            });
        },

        showCustomer: function(which) {
            window.location.href = '/admin/customers?detail='+which;
        },

        downloadInvoice: function(which) {
            window.location.href = '/admin/workorders?download='+which;
        },

        addRow: function()
        {
            workorder.renderMicroedit('Orderregel toevoegen', 'microedit-orderrow', {
                onSave: function()
                {
                    workorder.saveRow();
                }
            });

            Event.observe($('orderrow-type'), 'change', function() {
                switch($('orderrow-type').value) {
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

            });
        },

        removeRow: function(which) {
            var current_row = which;
            workorder.renderConfirm('Orderregel verwijderen', 'Weet je zeker dat je deze orderregel wilt verwijderen?', {
                onConfirm: function() {
                    alert('remove row ' + current_row);
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

            Event.observe($('orderrow-type'), 'change', function() {
                switch($('orderrow-type').value) {
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

            });

            d = workorder.currentData.orderrows[which];
            $('orderrow-description').value = d.d;
            $('orderrow-price').value = d.p;
            $('orderrow-duration').value = d.c;
            $('orderrow-amount').value = d.c;
            $('orderrow-type').value = d.t;


        },

        saveRow: function(row_id)
        {
            alert('save row '+row_id);
            $('modal-micro').removeClassName('active');
        },

        addPayment: function()
        {
            workorder.renderMicroedit('Betaling toevoegen', 'microedit-payment', {
                onSave: function()
                {
                    workorder.savePayment();
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
            workorder.renderConfirm('Betaling verwijderen', 'Weet je zeker dat je deze betaling wilt verwijderen?', {
                onConfirm: function() {
                    alert('remove payment '+current_row);
                },
                onCancel: function() {

                }
            });
        },

        editPayment: function(which) {
            var current_row = which;
            workorder.renderMicroedit('Betaling bewerken', 'microedit-payment', {
                onSave: function()
                {
                    workorder.savePayment(current_row);
                }
            });

            for ( i in workorder.currentData.payments) {
                if (workorder.currentData.payments.hasOwnProperty(i)) {
                    if(workorder.currentData.payments[i].id == which) {
                        d = workorder.currentData.payments[i];

                        $('payment-total').value = d.totalv;
                        $('payment-paymethod').value = d.paymethod;
                        $('payment-date').value = d.date;
                    }
                }
            }

            new MY.DatePicker({
                input: 'payment-date',
                format: 'dd-MM-yyyy',
                showWeek: true
            });
        },

        savePayment: function(row_id)
        {
            alert('save payment '+row_id);
            $('modal-micro').removeClassName('active');
        }
    });

    workorder = new workorderObject({});

    var WorkorderList = Class.create(GenericList, {
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
            'remove': 'workorder.remove',
            'view': 'workorder.view'
        }
    });

    Event.observe($('workorder-add-link'), 'click', function() { workorder.new(); } );
});

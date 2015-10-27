var list_data_3 = null;
var checklist_list = null;
var Checklist = 'test';

Event.observe(window, 'load', function() {
    var ChecklistObject = Class.create(AdminBase, {
        current_form: null,
        current_checklist: null,
        current_row: null,
        current_data: null,
        checklist_data: null,
        settings: {
            dataURL: checklist_data_url
        },
        new: function() {
            this.renderForm('Nieuwe controlelijst toevoegen', $('checklist-form'), {
                dataURL: this.settings.dataURL,
                dataMap: {
                    title: 'checklist-title'
                },
                listView: checklist_list,
                customRender: function(data)
                {
                    $('checklist-checklist-container').hide();
                }
            });

            button = $$('#dialog-content .form-buttons button').first();

            if (!button.hasClassName('button-4')) {
                button.hide(); // back button
            }

            button = new Element('button');
        },

        edit: function(row, data, callback) {
            checklist.current_checklist = data[0];
            checklist.current_row = row;
            checklist.current_data = data;


            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                title: 'checklist-title'
            };

            data.listView = checklist_list;
            data.listMap = data.dataMap;

            data.customRender = function(data) {
                $('checklist-checklist').innerHTML = '';
                if (data.checklist) {

                    checklist.checklist_data = data.checklist;

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Controlepunt'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('checklist-checklist').insert(table);

                    for (i in data.checklist) {
                        if (!data.checklist.hasOwnProperty(i)) continue;

                        row = data.checklist[i];

                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.title));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-edit');
                        io.setAttribute('title', 'Bewerken');
                        eval("Event.observe(a, 'click', function() { checklist.editRow("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');

                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-remove');
                        io.setAttribute('title', 'Verwijderen');
                        eval("Event.observe(a, 'click', function() { checklist.removeRow("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    };
                }
                else {
                    $('checklist-checklist').insert(new Element('p').update('Er zijn geen controlepunten toegevoegd aan deze controlelijst.'));
                }

                Event.observe($('checklist-add-row-link'), 'click', function() {
                    checklist.addRow();
                });

            }

            checklist.current_form = this.renderForm('Controlelijst bewerken', $('checklist-form'), data);
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

        addRow: function()
        {
            checklist.renderMicroedit('Controlepunt toevoegen', 'microedit-checklist-checklist', {
                onSave: function()
                {
                    checklist.saveRow(false);
                }
            });
        },

        removeRow: function(which) {
            var current_row = which;
            checklist.renderConfirm('Controlepunt verwijderen', 'Weet je zeker dat je dit controlepunt wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/checklistData', {
                        parameters: {
                            form: 'checklist',
                            method: 'delete',
                            id: current_row,
                            checklist_id: checklist.current_checklist

                        },
                        onSuccess: function (transport) {

                            switch (transport.responseJSON.status) {
                                case 'success':
                                    checklist.renderAlert('Het controlepunt is verwijderd.');
                                    $('modal-micro').removeClassName('active');
                                    checklist.edit(checklist.current_row, checklist.current_data);
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
            checklist.renderMicroedit('Controlepunt bewerken', 'microedit-checklist-checklist', {
                onSave: function()
                {
                    checklist.saveRow(current_row);
                }
            });

            for (i in checklist.checklist_data) {
                if (checklist.checklist_data[i].id == current_row) {
                    $('checklist-checklist-title').value = checklist.checklist_data[i].title;
                }
            }
        },

        saveRow: function(row_id)
        {
            new Ajax.Request('/admin/checklistData', {
                parameters: {
                    form: 'checklist',
                    method: 'save',
                    id: row_id,
                    checklist_id: checklist.current_checklist,
                    title: $('checklist-checklist-title').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            checklist.renderAlert(row_id?'Het controlepunt is gewijzigd.':'Het controlepunt is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            checklist.edit(checklist.current_row, checklist.current_data);
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                checklist.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
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

        remove: function(row, data) {
            var d_this = this;
            var d_row = row;
            var d_data = data;
            var d_id = data[0];
            this.renderConfirm('Controlelijst verwijderen', 'Weet je zeker dat je deze controlelijst wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    checklist.renderAlert('Verwijderen van controlelijst is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    checklist_list.forcedReload();
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
            data.dataURL = checklist.settings.dataURL;
            data.dataMap = {
                title: 'checklist-view-title'
            };

            data.customRender = function(data)
            {
                $('checklist-view-checklist').innerHTML = '';
                if (data.checklist) {

                    checklist.checklist_data = data.checklist;

                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Controlepunt'));
                    table.insert(tr);
                    $('checklist-view-checklist').insert(table);

                    for (i in data.checklist) {
                        if (!data.checklist.hasOwnProperty(i)) continue;

                        row = data.checklist[i];

                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.title));

                        table.insert(tr);
                    };
                }
                else {
                    $('checklist-checklist').insert(new Element('p').update('Er zijn geen controlepunten toegevoegd aan deze controlelijst.'));
                }
            }

            data.onEdit = function() {
                checklist.edit(d_row, d_data, checklist.view);
            }
            checklist.renderView('Controlelijst', $('checklist-view'), data);

            Event.observe($('checklist-edit-link'), 'click', function() {
                checklist.edit(d_row, d_data, checklist.view);
            });
        }
    });

    checklist = new ChecklistObject({});

    checklist_list = new GenericList({
        container: 'checklist-list',
        data_url: checklist_ajax_url,
        headers: [
            'Naam'
        ],
        actions: {
            'edit': 'checklist.edit',
            'view': 'checklist.view',
            'remove': 'checklist.remove'
            //'notes': 'resource.notes',
            //'settings': 'resource.settings',
            //'photos': 'resource.photos',
            //'documents': 'resource.documents'
        }
    });

    Event.observe($('checklist-add-link'), 'click', function() { checklist.new(); } );
});
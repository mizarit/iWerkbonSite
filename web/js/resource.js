var list_data = null;
var resource_list = null;
var Resource = 'test';

Event.observe(window, 'load', function() {
    var ResourceObject = Class.create(AdminBase, {
        current_form: null,
        settings: {
            dataURL: resource_data_url
        },
        new: function() {
            this.renderForm('Nieuwe medewerker toevoegen', $('resource-form'), {
                dataURL: this.settings.dataURL,
                dataMap: {
                    title: 'resource-title',
                    email: 'resource-email',
                    phone: 'resource-phone',
                    username: 'resource-username',
                    password1: 'resource-password1',
                    password2: 'resource-password2',
                    'resource-oa': 'resource-oa',
                    active: 'resource-active'
                },
                listView: resource_list
            });
        },

        edit: function(row, data, callback) {
            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                title: 'resource-title',
                email: 'resource-email',
                phone: 'resource-phone',
                username: 'resource-username',
                password1: 'resource-password1',
                password2: 'resource-password2',
                'resource-oa': 'resource-oa',
                active: 'resource-active',
                cmethod: 'resource-method'
            };

            data.listView = resource_list;
            data.listMap = data.dataMap;

            resource.current_form = this.renderForm('Medewerker bewerken', $('resource-form'), data);
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
            Event.observe($('invite-btn'), 'click', resource.inviteResource);
        },

        remove: function(row, data) {
            var d_this = this;
            var d_row = row;
            var d_data = data;
            var d_id = data[0];
            this.renderConfirm('Medewerker verwijderen', 'Weet je zeker dat je deze medewerker wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    d_this.renderAlert('Verwijderen van medewerker is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    // remove from local copy
                                    // render list again
                                    $(list_data).each(function(s,i) {

                                        if(d_id == s[0]) {
                                            removed = list_data.splice(i,1);
                                            localStorage.setItem('list_data_resource-list', Object.toJSON(list_data));
                                            resource_list.renderList();
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
        inviteResource: function()
        {
            if($('resource-password1').value == '') {
                resource.renderAlert('Het is alleen mogelijk een uitnodiging te sturen als je het wachtwoord voor deze gebruiker invoert.');
                $('resource-password1').addClassName('error');
            }
            else if ($('resource-email').value=='') {
                $('resource-email').addClassName('error');
            }
            else {
                $('resource-method').value = 'invite';
                resource.current_form.onSave();
            }
        },

        view: function(row, data) {

            var d_row = row;
            var d_data = data;

            data.row = row;
            data.dataURL = resource.settings.dataURL;
            data.dataMap = {
                title: 'resource-view-title',
                email: 'resource-view-email',
                phone: 'resource-view-phone'
            };

            data.customRender = function(data)
            {
                $('resource-workorders').innerHTML = '';
                if (data.workorders.length > 0) {
                    var table = new Element('table');
                    var thead = new Element('thead');
                    var tr = new Element('tr');
                    tr.insert(new Element('th').update('Datum').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Klant'));
                    tr.insert(new Element('th').update('Adres'));
                    tr.insert(new Element('th').update('Status').setStyle({width:'220px'}));
                    tr.insert(new Element('th').update('Gereed'));
                    tr.insert(new Element('th').update('Acties').setStyle({width:'1.3em'}));
                    table.insert(tr);
                    $('resource-workorders').insert(table);

                    $(data.workorders).each(function(row){
                        var tr = new Element('tr');
                        tr.insert(new Element('td').update(row.date));
                        tr.insert(new Element('td').update(row.customer));
                        tr.insert(new Element('td').update(row.address));
                        tr.insert(new Element('td').update(row.status));
                        tr.insert(new Element('td').update(row.ready?'Ja':'Nee'));

                        var td = new Element('td').setStyle({textAlign:'right'});
                        var a = new Element('a');
                        var io = new Element('i');
                        io.addClassName('fa');
                        io.addClassName('fa-search');
                        io.setAttribute('title', 'Werkbon bekijken');
                        eval("Event.observe(a, 'click', function() { resource.showWorkorder("+row.id+"); });");
                        a.insert(io);
                        td.insert(a);
                        td.insert('&nbsp;');
                        tr.insert(td);

                        table.insert(tr);
                    });
                }
                else {
                    $('resource-workorders').insert(new Element('p').update('Deze medewerker heeft geen werkbonnen'));
                }
            }

            data.onEdit = function() {
                resource.edit(d_row, d_data, resource.view);
            }
            resource.renderView('Medewerker', $('resource-view'), data);

            Event.observe($('resource-edit-link'), 'click', function() {
                resource.edit(d_row, d_data, resource.view);
            });
        },

        addResource: function()
        {
        },

        showWorkorder: function(which)
        {
            window.location.href = '/admin/workorders?detail='+which;
        }
    });

    resource = new ResourceObject({});

    resource_list = new GenericList({
        container: 'resource-list',
        data_url: resource_ajax_url,
        headers: [
            'Naam',
            'Email',
            'Telefoon'
        ],
        actions: {
            'edit': 'resource.edit',
            'view': 'resource.view',
            'remove': 'resource.remove'
            //'notes': 'resource.notes',
            //'settings': 'resource.settings',
            //'photos': 'resource.photos',
            //'documents': 'resource.documents'
        }
    });

    Event.observe($('resource-add-link'), 'click', function() { resource.new(); } );



});
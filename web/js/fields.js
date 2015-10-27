var list_data_2 = null;
var fields_list = null;
var fields = 'test';

Event.observe(window, 'load', function() {
    var FieldsObject = Class.create(AdminBase, {
        current_form: null,
        settings: {
            dataURL: fields_data_url
        },
        new: function() {
            this.renderForm('Nieuw veld toevoegen', $('fields-form'), {
                dataURL: this.settings.dataURL,
                dataMap: {
                    title: 'fields-title',
                    form: 'fields-fform'
                },
                listView: fields_list
            });
        },

        edit: function(row, data, callback) {
            data.row = row;
            data.dataURL = this.settings.dataURL;
            data.dataMap = {
                title: 'fields-title',
                form: 'fields-fform'
            };

            data.listView = fields_list;
            data.listMap = data.dataMap;

            fields.current_form = this.renderForm('Veld bewerken', $('fields-form'), data);
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
            this.renderConfirm('Veld verwijderen', 'Weet je zeker dat je dit veld wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request(d_this.settings.dataURL, {
                        parameters: {
                            method: 'delete',
                            id: d_data[0]
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    fields.renderAlert('Verwijderen van veld is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    fields_list.forcedReload();
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
            data.dataURL = fields.settings.dataURL;
            data.dataMap = {
                title: 'fields-view-title',
                form: 'fields-view-form'
            };

            data.customRender = function(data)
            {

            }

            data.onEdit = function() {
                fields.edit(d_row, d_data, fields.view);
            }
            fields.renderView('Veld', $('fields-view'), data);

            Event.observe($('fields-edit-link'), 'click', function() {
                fields.edit(d_row, d_data, fields.view);
            });
        }
    });

    fields = new FieldsObject({});

    fields_list = new GenericList({
        container: 'fields-list',
        data_url: fields_ajax_url,
        headers: [
            'Naam',
            'Formulier'
        ],
        actions: {
            'edit': 'fields.edit',
            'view': 'fields.view',
            'remove': 'fields.remove'
            //'notes': 'resource.notes',
            //'settings': 'resource.settings',
            //'photos': 'resource.photos',
            //'documents': 'resource.documents'
        }
    });

    Event.observe($('fields-add-link'), 'click', function() { fields.new(); } );
});
var AdminBase = Class.create({
    initialize: function(name) {
        this.name = name;
    },

    renderMicroedit: function(title, form, data)
    {
        w = 680;
        h = 370;

        x = (max_modal_width - w) / 2;
        y = (max_modal_height - h ) / 2;
        l = (document.viewport.getDimensions().width - 50 - w) / 2;
        $('modal-inner-micro').setStyle({

            height:h+'px',
            width:w+'px',
            left: l+'px',
            top: y+'px'
        });
        $('dialog-title-micro').innerHTML = title;
        $('dialog-content-micro').innerHTML = '';
        form2 = $(form).clone(true);
        $('dialog-content-micro').insert(form2);

        $$('#dialog-content-micro .inplace-editor').each(function(s, i) {
            new Ajax.InPlaceEditor(s.id, '/admin/textEditor', {
                cancelText: 'Annuleren',
                okText: 'Opslaan'
            });
        });

        form2.show();

        if (data.dataURL) {
            var d_data = data;
            new Ajax.Request(data.dataURL, {
                parameters: {
                    id: data[0]
                },
                onSuccess: function(transport) {
                    $H(d_data.dataMap).each(function(field) {
                        if ($(field.value)) {
                            switch ($(field.value).type) {
                                case 'checkbox':
                                    if (transport.responseJSON[field.key]) {
                                        $(field.value).setAttribute('checked', 'checked');
                                    }
                                    break;
                                default:
                                    $(field.value).value = transport.responseJSON[field.key];
                                    break;
                            }
                        }

                    });
                    if(d_data.customRender) {
                        d_data.customRender(transport.responseJSON);
                    }
                }
            });
        }

        elem = $$('#dialog-content-micro .button-1').first();
        if (elem) {
            if (data.onClose) {
                Event.observe(elem, 'click', data.onClose);
            }
            Event.observe(elem, 'click', function() { $('modal-micro').removeClassName('active'); });
        }

        elem = $$('#dialog-content-micro .button-2').first();
        if (elem) {
            if (data.onSave) {
                Event.observe(elem, 'click', data.onSave);
            }
            else {
                Event.observe(elem, 'click', function () {
                    $('modal-micro').removeClassName('active');
                });
            }
        }

        $('modal-micro').addClassName('active');
    },


    renderForm: function(title, form, data)
    {
        w = max_modal_width - 50;
        h = max_modal_height - 50;

        l = (document.viewport.getDimensions().width - 50 - w) / 2;
        $('modal-inner').setStyle({

            height:h+'px',
            width:w+'px',
            left: l+'px',
            top: 25+'px'
        });
        $('dialog-title').innerHTML = title;
        $('dialog-content').innerHTML = '';
        form2 = form.clone(true);
        $('dialog-content').insert(form2);
        form2.show();

        $$('#dialog-content .inplace-editor').each(function(s, i) {
            new Ajax.InPlaceEditor(s.id, '/admin/textEditor', {
                cancelText: 'Annuleren',
                okText: 'Opslaan'
            });
        });

        $$('#dialog-content > div > div').first().setStyle({height:(h-100)+'px', overflow:'auto'});

        $$('.error').each(function(s,i){
            $(s).removeClassName('error');
        });
        if (data.dataURL) {
            var d_data = data;
            new Ajax.Request(data.dataURL, {
                parameters: {
                    id: data[0]
                },
                onSuccess: function(transport) {
                    $H(d_data.dataMap).each(function(field) {
                        if ($(field.value)) {
                            switch ($(field.value).type) {
                                case 'checkbox':
                                    if (transport.responseJSON[field.key]) {
                                        $(field.value).setAttribute('checked', 'checked');
                                    }
                                    break;
                                default:
                                    $(field.value).value = transport.responseJSON[field.key];
                                    break;
                            }
                        }

                    });
                    if(d_data.customRender) {
                        d_data.customRender(transport.responseJSON);
                    }
                }
            });
        }

        var d_data = data;
        var d_this = this;

        data.onSave = function() {
            var postData = {};
            $H(d_data.dataMap).each(function(field) {
                value = $(field.value).value;
                postData[field.key] = value;
            });
            postData.method = 'save';
            postData.id = d_data[0];
            new Ajax.Request(d_data.dataURL, {
                parameters: postData,
                onSuccess: function(transport) {
                    switch(transport.responseJSON.status) {
                        case 'success':
                            // set new item in storage and reload list
                            /*c = 0;
                            for ( i in d_data.listMap) {
                                c++;
                                d_data.listView.data[d_data.row][c] = postData[i];
                            }
                            d_data.listView.renderList();

                            localStorage.setItem('list_data_'+d_data.listView.element, Object.toJSON(d_data.listView.data));
                            */

                            if (localStorage.getItem('list_data_'+d_data.listView.element)) {
                                localStorage.removeItem('list_data_' + d_data.listView.element);
                            }
                            d_data.listView.forcedReload();
                            d_data.listView.renderList();
                            $('modal').removeClassName('active');
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                d_this.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
                                $('modal').removeClassName('active');
                            }
                            break;
                    }
                }
            });
        }

        $('modal').addClassName('active');

        elem = $$('#dialog-content .button-1').first();
        if (elem) {
            if (data.onClose) {
                Event.observe(elem, 'click', data.onClose);
            }
            Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });
        }

        elem = $$('#dialog-content .button-2').first();
        if (elem) {
            if (data.onSave) {
                Event.observe(elem, 'click', data.onSave);
            }
            else {
                Event.observe(elem, 'click', function () {
                    $('modal').removeClassName('active');
                });
            }
        }
    },

    renderView: function(title, view, data)
    {
        w = max_modal_width - 50;
        h = max_modal_height - 50;
        l = (document.viewport.getDimensions().width - 50 - w) / 2;
        $('modal-inner').setStyle({
            height:h+'px',
            width:w+'px',
            left: l+'px',
            top: 25+'px'
        });
        $('dialog-title').innerHTML = title;
        $('dialog-content').innerHTML = '';
        form2 = view.clone(true);
        $('dialog-content').insert(form2);
        form2.show();

        $$('#dialog-content .inplace-editor').each(function(s, i) {
            new Ajax.InPlaceEditor(s.id, '/admin/textEditor', {
                cancelText: 'Annuleren',
                okText: 'Opslaan'
            });
        });

        $$('#dialog-content > div > div').first().setStyle({height:(h-100)+'px', overflow:'auto'});

        if (data.dataURL) {
            var d_data = data;
            new Ajax.Request(data.dataURL, {
                parameters: {
                    id: data[0]
                },
                onSuccess: function(transport) {
                    $H(d_data.dataMap).each(function(field) {
                        if ($(field.value)) {
                            $(field.value).update(transport.responseJSON[field.key]);
                        }
                    });

                    if(d_data.customRender) {
                        d_data.customRender(transport.responseJSON);
                    }
                }
            });
        }

        $('modal').addClassName('active');

        elem = $$('#dialog-content .button-1').first();
        if (elem) {
            if (data.onClose) {
                Event.observe(elem, 'click', data.onClose);
            }
            Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });
        }

        elem = $$('#dialog-content .button-2').first();
        if (elem) {
            if (data.onEdit) {
                Event.observe(elem, 'click', data.onEdit);
                //Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });
            }
            else {
                elem.hide();
            }
        }
    },

    renderAlert: function(title)
    {
        w = 450;
        h = 270;

        l = (document.viewport.getDimensions().width - 50 - w) / 2;
        y = (max_modal_height - h ) / 2;

        $('modal-inner').setStyle({

            height:h+'px',
            width:w+'px',
            left: l+'px',
            top: y+'px'
        });
        $('dialog-title').innerHTML = 'Melding';

        $('dialog-content').innerHTML = '';
        form = $('confirm-form').clone(true);
        $('dialog-content').insert(form);
        $('confirm-caption').innerHTML = title;

        elem = $$('#dialog-content .button-1').first();
        elem.hide();
        elem = $$('#dialog-content .button-2').first();
        Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });


        form.show();
        $('modal').addClassName('active');
    },

    renderConfirm: function(title, caption, data)
    {
        w = 450;
        h = 270;

        l = (document.viewport.getDimensions().width - 50 - w) / 2;
        y = (max_modal_height - h ) / 2;

        $('modal-inner').setStyle({

            height:h+'px',
            width:w+'px',
            left: l+'px',
            top: y+'px'
        });
        $('dialog-title').innerHTML = title;
        $('confirm-caption').innerHTML = caption;
        $('dialog-content').innerHTML = '';
        form = $('confirm-form').clone(true);
        $('dialog-content').insert(form);

        elem = $$('#dialog-content .button-1').first();
        if (elem) {
            if (data.onCancel) {
                Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });
                Event.observe(elem, 'click', data.onCancel);

            }
            else {
                elem.hide();
            }
        }

        elem = $$('#dialog-content .button-2').first();
        if (elem) {
            if (data.onConfirm) {
                Event.observe(elem, 'click', function() { $('modal').removeClassName('active'); });
                Event.observe(elem, 'click', data.onConfirm);
            }
            else {
                elem.hide();
            }
        }
        form.show();

        $('modal').addClassName('active');
    },

    setButtons: function(buttons)
    {
        $('buttons-inner').update('');
        $(buttons).each(function(s,i) {
            button = new Element('button');
            Event.observe(button, 'click', s.callback);
            if (s.type) {
                button.addClassName(s.type);
            }
            else {
                button.addClassName('button-2');
            }
            button.update(s.caption);
            $('buttons-inner').insert(button);
        });
    },

    saveForm: function(fields, config)
    {
        var current_config = config;
        var current_fields = fields;
        var post = {};
        $(fields).each(function(s) {
            v = $(s).value;
            post[s] = v;
        });
        post.form = config.form;
        new Ajax.Request(config.dataURL, {
            parameters: post,
            onSuccess: function(transport) {
                $(current_fields).each(function(s) {
                    $(s).removeClassName('error');
                });
                if (transport.responseJSON.status == 'success') {
                    eval('current_config.onSuccess(transport.responseJSON)');
                }
                else {
                    for(i in transport.responseJSON.errors) {
                        $(i).addClassName('error');
                    };
                }
            }
        });
    }

});
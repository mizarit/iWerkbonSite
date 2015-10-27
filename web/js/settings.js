var list_data = null;
var settings_list = null;
var Settings = 'test';

Event.observe(window, 'load', function() {
    var SettingsObject = Class.create(AdminBase, {
        settings: {
            dataURL: settings_data_url,
            products: []
        },
        initialize: function (config) {

            this.settings.products = products;
        },

        invoices: function () {
            this.selectPanel('invoices');
        },

        general: function () {
            this.selectPanel('general');
        },

        products: function () {
            this.selectPanel('products');
        },

        resources: function () {
            this.selectPanel('resources');
        },

        checklists: function () {
            this.selectPanel('checklists');
        },

        fields: function () {
            this.selectPanel('fields');
        },

        app: function () {
            this.selectPanel('app');
        },

        login: function () {
            this.selectPanel('login');
        },

        selectPanel: function (which) {
            var wwhich = which;
            $$('.settings-panel').each(function (s) {
                s.id == 'settings-' + wwhich ? $(s).show() : $(s).hide();
            });
            switch (which) {
                case 'invoices':
                    this.setButtons([
                        {caption:'Voorbeeld', callback: Settings.exampleInvoice, type: 'button-3'},
                        {caption:'Opslaan', callback: Settings.saveInvoices, type: 'button-4'}
                    ]);
                    break;
                case 'general':
                    this.setButtons([
                        {caption:'Opslaan', callback: Settings.saveGeneral, type: 'button-4'}
                    ]);
                    break;
                case 'products':
                    this.setButtons([
                        {caption:'Volgorde opslaan', callback: Settings.saveProductOrder, type: 'button-4'}
                    ]);
                    this.renderProducts();
                    break;
                case 'resources':
                    this.setButtons([
                    ]);
                    break;
                case 'checklists':
                this.setButtons([
                ]);
                break;
                case 'fields':
                    this.setButtons([
                    ]);
                    break;
                case 'app':
                    this.setButtons([
                        {caption:'Opslaan', callback: Settings.saveApp, type: 'button-4'}
                    ]);
                    break;
                case 'login':
                    this.setButtons([
                        {caption:'Opslaan', callback: Settings.saveLogin, type: 'button-4'}
                    ]);
                    break;
            }
        },

        addProduct: function()
        {
            Settings.renderMicroedit('Product toevoegen', 'microedit-product', {
                onSave: function()
                {
                    Settings.saveProduct(false);
                }
            });
            currency.initField($('product-price'));
        },

        editProduct: function(event)
        {
            if(event.id) {
                var product_id = event.id.substr(8);
            }
            else {
                var product_id = this.parentNode.parentNode.id.substr(8);
            }
            Settings.renderMicroedit('Product bewerken', 'microedit-product', {
                onSave: function()
                {
                    Settings.saveProduct(product_id);
                },
                dataURL: '/admin/settingsData?form=products&method=load',
                dataMap: {
                    description: 'product-description',
                    price: 'product-price',
                    type: 'product-type'
                },
                customRender: function(response)
                {
                    $('product-price').value = accounting.formatMoney($('product-price').value, "", 2, ".", ",");
                },
                0: product_id
            });

            currency.initField($('product-price'));

            if(!event.id) {
                Event.stop(event);
            }
        },

        removeProduct: function(elem)
        {
            var product_id = this.parentNode.parentNode.id.substr(8);
            Settings.renderConfirm('Product verwijderen', 'Weet je zeker dat je dit product wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/settingsData', {
                        parameters: {
                            form: 'products',
                            method: 'delete',
                            id: product_id
                        },
                        onSuccess: function(transport) {
                            Settings.renderAlert('Het product is verwijderd.');
                            Settings.renderProducts(transport.responseJSON.products);
                        }
                    });
                },
                onCancel: function() {

                }
            });

        },

        renderProducts: function(products)
        {
            if (products) {
                Settings.settings.products = products;
            }

            $('sortable-tree').update('');
            this.recurseNode(Settings.settings.products);

            Sortable.create('sortable-tree', {
                tree: true,
                handle: 'fa-sort'
            });

            Event.observe('products-add-link', 'click', Settings.addProduct);
            $$('#sortable-tree .fa-edit').each(function(s, i) {
                Event.observe(s, 'click', Settings.editProduct);
            });
            $$('#sortable-tree .fa-remove').each(function(s, i) {
                Event.observe(s, 'click', Settings.removeProduct);
            });
        },

        recurseNode: function(node, depth, root)
        {
            var current_root = root ? root : $('sortable-tree');
            var current_depth = depth;

            $(node).each(function(s, i) {
                var li = new Element('li');
                li.id = 'product_'+ s.id;
                li.update(s.title);

                Event.observe(li, 'click', function(event) {
                    Settings.editProduct(event.target);
                });

                var span = new Element('span');
                var i0 = new Element('i');
                i0.addClassName('fa');
                i0.addClassName('fa-sort');
                i0.setAttribute('title', 'Sorteren');
                span.insert(i0);
                span.insert('&nbsp;')

                var i1 = new Element('i');
                i1.addClassName('fa');
                i1.addClassName('fa-edit');
                i1.setAttribute('title', 'Bewerken');
                span.insert(i1);
                span.insert('&nbsp;')
                var i2 = new Element('i');
                i2.addClassName('fa');
                i2.addClassName('fa-remove');
                i2.setAttribute('title', 'Verwijderen');
                span.insert(i2);
                li.insert(span);

                var ul = new Element('ul');
                if (s.children) {
                    Settings.recurseNode(s.children, current_depth + 1, ul);
                }
                else {
                    var li2 = new Element('li');
                    li2.addClassName('nested-droppable');
                    ul.insert(li2);
                }
                li.insert(ul);
                current_root.insert(li);
            });
        },

        saveProduct: function(product_id)
        {
            var isEdit = product_id;
            new Ajax.Request('/admin/settingsData', {
                parameters: {
                    form: 'products',
                    method: 'save',
                    id: product_id,
                    description: $('product-description').value,
                    price: $('product-price').value,
                    type: $('product-type').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            Settings.renderAlert(isEdit?'De wijzigingen zijn opgeslagen.':'Het product is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            Settings.renderProducts(transport.responseJSON.products);
                            break;

                        case 'error':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                Settings.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
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



        saveInvoices: function()
        {
            Settings.saveForm(['companyname', 'kvk', 'btw', 'iban', 'iban_name', 'site', 'email', 'color1', 'color2', 'logo-fld'], {
                dataURL: Settings.settings.dataURL,
                form: 'invoices',
                onSuccess: function (response) {
                    Settings.renderAlert(response.message);
                }
            });
        },

        saveGeneral: function()
        {
            Settings.saveForm(['companyname2', 'address', 'zipcode', 'city', 'phone1', 'sender_name', 'sender_email', 'admin_email', 'invoicedays', 'api_server', 'api_key', 'api_secret'], {
                dataURL: Settings.settings.dataURL,
                form: 'general',
                onSuccess: function (response) {
                    Settings.renderAlert(response.message);
                }
            });
        },

        saveApp: function()
        {
            Settings.saveForm(['app-setting-1', 'app-setting-2', 'app-setting-3', 'app-setting-4', 'app-setting-5', 'app-setting-6', 'app-setting-7', 'app-setting-8', 'app-setting-9', 'app-setting-10', 'app-setting-11', 'app-setting-12'], {
                dataURL: Settings.settings.dataURL,
                form: 'app',
                onSuccess: function (response) {
                    Settings.renderAlert(response.message);
                }
            });
        },

        saveProductOrder: function()
        {
            post = Sortable.serialize('sortable-tree', {
                tree: true
            });
            post += '&form=products&method=sortorder';

            new Ajax.Request(Settings.settings.dataURL, {
                parameters: post,
                onSuccess: function(transport) {
                    if (transport.responseJSON.status == 'success') {
                        Settings.renderAlert(transport.responseJSON.message);
                        console.log(transport.responseJSON);
                        Settings.renderProducts(transport.responseJSON.products);
                    }
                    else {
                        for(i in transport.responseJSON.errors) {
                            $(i).addClassName('error');
                        };
                    }
                }
            });
        },

        saveLogin: function()
        {
            Settings.saveForm(['admin-title', 'admin-email', 'admin-username', 'admin-password1', 'admin-password2'], {
                dataURL: Settings.settings.dataURL,
                form: 'login',
                onSuccess: function (response) {
                    Settings.renderAlert(response.message);
                }
            });
        },

        exampleInvoice: function()
        {

            Settings.saveForm(['companyname', 'kvk', 'btw', 'iban', 'iban_name', 'site', 'email', 'color1', 'color2', 'logo-fld'], {
                dataURL: Settings.settings.dataURL,
                form: 'invoices-preview',
                onSuccess: function (response) {
                    window.open(response['download-link']);
                }
            });
        }
    });

    Settings = new SettingsObject({});
    Settings.setButtons([
        {caption:'Opslaan', callback: Settings.saveGeneral, type: 'button-4'}
    ]);
});
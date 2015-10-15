var store = null;
var panel = null;
var selectedNodes = null;
Ext.onReady(function(){
    var xd = Ext.data;

    store = new Ext.data.JsonStore({
        url: zeusController + '/productadmin/imageupdate',
        root: 'images',
        fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date', dateFormat:'timestamp'}]
    });
    store.load();

    var tpl = new Ext.XTemplate(
		'<tpl for=".">',
            '<div class="thumb-wrap" id="{name}">',
		    '<div class="thumb"><div class="thumb-inner"><img src="{url}" title="{name}"></div></div>',
		    '<span class="x-editable">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>'
	);

    panel = new Ext.Panel({
        id:'images-view',
        frame:false,
        /*width:535,*/
        autoHeight:true,
        collapsible:false,
        layout:'fit',
        title:'Afbeeldingen',

        items: new Ext.DataView({
            store: store,
            tpl: tpl,
            /*autoHeight:true,*/
            multiSelect: true,
            overClass:'x-view-over',
            itemSelector:'div.thumb-wrap',
            emptyText: 'Geen afbeeldingen gevonden',

            plugins: [
                new Ext.DataView.DragSelector(),
                new Ext.DataView.LabelEditor({dataIndex: 'name'})
            ],

            prepareData: function(data){
                data.shortName = Ext.util.Format.ellipsis(data.name, 15);
                data.sizeString = Ext.util.Format.fileSize(data.size);
                data.dateString = data.lastmod.format("m-d-Y g:i a");
                return data;
            },
            
            listeners: {
            	selectionchange: {
            		fn: function(dv,nodes){
            			var l = nodes.length;
            			if (l > 0) {
            			  selectedNodes = nodes;
            			  $('delete-image-btn').removeClassName('zeus-button-disabled');
            			  $('main-image-btn').removeClassName('disabled');
            			  $('main-image-btn').disabled = false;
            			}
            			else {
            			  $('delete-image-btn').addClassName('zeus-button-disabled');
            			  $('main-image-btn').addClassName('disabled');
            			  $('main-image-btn').disabled = true;
            			}
            		}
            	}
            }
        })
    });
    
    panel.deleteItems = function(obj)
    {
      if (!obj.hasClassName('zeus-button-disabled')) {
        if (confirm('Weet je zeker dat je deze afbeeldingen wilt verwijderen?')) {
          files = '';
          for (i in selectedNodes) {
            
            if (selectedNodes[i].id) {
              files += selectedNodes[i].id + '_____';
            }
          }
          
          if (files != '') {
            new Ajax.Request(zeusController + '/productadmin/imagedelete/filename/' + files, {
              
              onSuccess: function()
              {
                store.load();
                panel.render('images-container');
              }
              
            });
          }
        }
      }
    }
    panel.render('images-container');

});

function setMainImage()
{
  file = '';
  for (i in selectedNodes) {
    if (selectedNodes[i].id) {
      file = selectedNodes[i].id;
    }
  }
  $('main-image').value = file;
}


Ext.DataView.LabelEditor = Ext.extend(Ext.Editor, {
    alignment: "tl-tl",
    hideEl : false,
    cls: "x-small-editor",
    shim: false,
    completeOnEnter: true,
    cancelOnEsc: true,
    labelSelector: 'span.x-editable',
    
    constructor: function(cfg, field){
        Ext.DataView.LabelEditor.superclass.constructor.call(this,
            field || new Ext.form.TextField({
                allowBlank: false,
                growMin:90,
                growMax:240,
                grow:true,
                selectOnFocus:true
            }), cfg
        );
    },
    
    init : function(view){
        this.view = view;
        view.on('render', this.initEditor, this);
        this.on('complete', this.onSave, this);
    },

    initEditor : function(){
        this.view.on({
            scope: this,
            containerclick: this.doBlur,
            click: this.doBlur
        });
        this.view.getEl().on('mousedown', this.onMouseDown, this, {delegate: this.labelSelector});
    },
    
    doBlur: function(){
        if(this.editing){
            this.field.blur();
        }
    },

    onMouseDown : function(e, target){
        if(!e.ctrlKey && !e.shiftKey){
            var item = this.view.findItemFromChild(target);
            e.stopEvent();
            var record = this.view.store.getAt(this.view.indexOf(item));
            this.startEdit(target, record.data[this.dataIndex]);
            this.activeRecord = record;
        }else{
            e.preventDefault();
        }
    },

    onSave : function(ed, value){
        this.activeRecord.set(this.dataIndex, value);
    }
});


Ext.DataView.DragSelector = function(cfg){
    cfg = cfg || {};
    var view, proxy, tracker;
    var rs, bodyRegion, dragRegion = new Ext.lib.Region(0,0,0,0);
    var dragSafe = cfg.dragSafe === true;

    this.init = function(dataView){
        view = dataView;
        view.on('render', onRender);
    };

    function fillRegions(){
        rs = [];
        view.all.each(function(el){
            rs[rs.length] = el.getRegion();
        });
        bodyRegion = view.el.getRegion();
    }

    function cancelClick(){
        return false;
    }

    function onBeforeStart(e){
        return !dragSafe || e.target == view.el.dom;
    }

    function onStart(e){
        view.on('containerclick', cancelClick, view, {single:true});
        if(!proxy){
            proxy = view.el.createChild({cls:'x-view-selector'});
        }else{
            proxy.setDisplayed('block');
        }
        fillRegions();
        view.clearSelections();
    }

    function onDrag(e){
        var startXY = tracker.startXY;
        var xy = tracker.getXY();

        var x = Math.min(startXY[0], xy[0]);
        var y = Math.min(startXY[1], xy[1]);
        var w = Math.abs(startXY[0] - xy[0]);
        var h = Math.abs(startXY[1] - xy[1]);

        dragRegion.left = x;
        dragRegion.top = y;
        dragRegion.right = x+w;
        dragRegion.bottom = y+h;

        dragRegion.constrainTo(bodyRegion);
        proxy.setRegion(dragRegion);

        for(var i = 0, len = rs.length; i < len; i++){
            var r = rs[i], sel = dragRegion.intersect(r);
            if(sel && !r.selected){
                r.selected = true;
                view.select(i, true);
            }else if(!sel && r.selected){
                r.selected = false;
                view.deselect(i);
            }
        }
    }

    function onEnd(e){
        if (!Ext.isIE) {
            view.un('containerclick', cancelClick, view);    
        }        
        if(proxy){
            proxy.setDisplayed(false);
        }
    }

    function onRender(view){
        tracker = new Ext.dd.DragTracker({
            onBeforeStart: onBeforeStart,
            onStart: onStart,
            onDrag: onDrag,
            onEnd: onEnd
        });
        tracker.initEl(view.el);
    }
};

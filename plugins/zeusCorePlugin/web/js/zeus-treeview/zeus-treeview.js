var serializedTree = '';
var tree = null;
var zeusSelectedRow = null;

Ext.onReady(function(){

  tree = new Ext.ux.tree.ColumnTree({
    height: 500,
    rootVisible:false,
    autoScroll:true,
    title: 'Navigatie',
    renderTo: 'menu-container',
    enableDD: true,
    columns:[{
      header:'Titel',
      width:300,
      dataIndex:'title'
    },{
      header:'Link',
      width:300,
      dataIndex:'value'
    }],
    
    loader: new Ext.tree.TreeLoader({
      dataUrl: zeusController + '/menuadmin/tree',
      uiProviders:{
        'col': Ext.ux.tree.ColumnNodeUI
      }
    }),
    
    root: new Ext.tree.AsyncTreeNode({
      text: 'Menu',
      draggable: false,
      id: 'source'
    }),
        
    listeners: {
      load: {
        fn: function(dv) {
          serializedTree = this.toString(dv);
        }
        
      },
      movenode: {
        fn: function(dv,nodes){
          serializedTree = this.toString(dv);
          $('saveorder-btn').removeClassName('zeus-button-disabled');
    		}
      },
      dblclick: {
        fn: function(node, ev){
          window.location.href = zeusController + '/menuadmin/edit/id/' + node.id;
    		}
      },
      click: {
        fn: function(node, ev){
          zeusSelectedRow = node.id;
          $('delete-btn').removeClassName('zeus-button-disabled');
    		}
      }
     },
     
     saveOrder: function() {
       new Ajax.Request(zeusController + '/menuadmin/saveorder', {
         parameters: 'data=' + serializedTree,
         method: 'post',
         onComplete: function()
         {
           $('saveorder-btn').addClassName('zeus-button-disabled');
         }
       });
     }
  });
  
  tree.expandAll();
});

Ext.ns('Ext.ux.tree');

Ext.ux.tree.ColumnTree = Ext.extend(Ext.tree.TreePanel, {
    lines : false,
    borderWidth : Ext.isBorderBox ? 0 : 2, // the combined left/right border for each cell
    cls : 'x-column-tree',

    onRender : function(){
        Ext.tree.ColumnTree.superclass.onRender.apply(this, arguments);
        this.headers = this.header.createChild({cls:'x-tree-headers'});

        var cols = this.columns, c;
        var totalWidth = 0;
        var scrollOffset = 19; // similar to Ext.grid.GridView default

        this.headers.setWidth('100%');
        
        
        for(var i = 0, len = cols.length; i < len; i++){
             c = cols[i];
             totalWidth += c.width;
             
             if ( i == len ) {
               w = c.width-this.borderWidth;
               w += (this.headers.getWidth() - totalWidth - 18);
             }
             else {
               w = c.width-this.borderWidth;
             }
             
             this.headers.createChild({
                 cls:'x-tree-hd ' + (c.cls?c.cls+'-hd':''),
                 cn: {
                     cls:'x-tree-hd-text',
                     html: c.header
                 },
                 style:'width:'+(w)+'px;'
             });
        }
        this.headers.createChild({cls:'x-clear'});
        // prevent floats from wrapping when clipped
        //this.headers.setWidth(totalWidth+scrollOffset);
        this.headers.setWidth('100%');
        this.innerCt.setWidth('100%');
        //this.innerCt.setWidth(totalWidth);
    },
    
    toString: function(){
	    return this.nodeToString(this.getRootNode());
	  },
	  
	  nodeFilter: function(node) {
	    return true;
	  },
	  
	  defaultNodeFilter: function(node) {
        return true;
    },

	  
	  standardAttributes: ["object", "expanded", "allowDrag", "allowDrop", "disabled", "icon",
    "cls", "iconCls", "href", "hrefTarget", "qtip", "singleClickExpand", "uiProvider", "allowChildren", "expandable"],

    
	  defaultAttributeFilter: function(attName, attValue) {
        return    (typeof attValue != 'function') &&
        (typeof attValue != 'object') &&
                (this.standardAttributes.indexOf(attName) == -1);
    },


  	nodeToString: function(node){
  //		Exclude nodes based on caller-supplied filtering function
  	    if (!this.nodeFilter(node)) {
  	        return '';
  	    }
  	    var c = false, result = "{";
  	    if (this.defaultAttributeFilter("id", node.id)) {
  	        result += '"id":"' + node.id + '"';
  	        c = true;
  	    }
  
  //		Add all user-added attributes unless rejected by the attributeFilter.
  	    for(var key in node.attributes) {
  	        if (this.defaultAttributeFilter(key, node.attributes[key])) {
  		        if (c) result += ',';
  		        v =  node.attributes[key];
  		        if (key == 'title') {
  		          v = v.replace('&', ' en ');
  		        }
  		        result += '"' + (this.attributeMap ? (this.attributeMap[key] || key) : key) + '":"' + v + '"';
  		        c = true;
  		    }
  	    }
  	
  //		Add child nodes if any
  	    var children = node.childNodes;
  	    var clen = children.length;

  	    if(clen != 0){
  	        if (c) result += ',';
  	        result += '"children":['
  	        for(var i = 0; i < clen; i++){
  	            if (i > 0) result += ',';
  	            result += this.nodeToString(children[i]);
  	        }
  	        result += ']';
  	    }
  	    return result + "}";
  	}
});

Ext.reg('columntree', Ext.ux.tree.ColumnTree);

//backwards compat
Ext.tree.ColumnTree = Ext.ux.tree.ColumnTree;

Ext.ux.tree.ColumnNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    focus: Ext.emptyFn, // prevent odd scrolling behavior

    renderElements : function(n, a, targetNode, bulkRender){
        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var t = n.getOwnerTree();
        var cols = t.columns;
        var bw = t.borderWidth;
        var c = cols[0];

        var buf = [
             '<li class="x-tree-node" id="object-',a['id'],'"><div ext:tree-node-id="',n.id,'" class="x-tree-node-el x-tree-node-leaf ', a.cls,'">',
                '<div class="x-tree-col" style="width:',c.width-bw,'px;">',
                    '<span class="x-tree-node-indent">',this.indentMarkup,"</span>",
                    '<img src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow">',
                    '<img src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon',(a.icon ? " x-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on">',
                    '<a hidefocus="on" class="x-tree-node-anchor" href="',a.href ? a.href : "#",'" tabIndex="1" ',
                    a.hrefTarget ? ' target="'+a.hrefTarget+'"' : "", '>',
                    '<span unselectable="on">', n.text || (c.renderer ? c.renderer(a[c.dataIndex], n, a) : a[c.dataIndex]),"</span></a>",
                "</div>"];
         for(var i = 1, len = cols.length; i < len; i++){
             c = cols[i];

             buf.push('<div class="x-tree-col ',(c.cls?c.cls:''),'" style="width:',c.width-bw,'px;">',
                        '<div class="x-tree-col-text">',(c.renderer ? c.renderer(a[c.dataIndex], n, a) : a[c.dataIndex]),"</div>",
                      "</div>");
         }
         buf.push(
            '<div class="x-clear"></div></div>',
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>");

        if(bulkRender !== true && n.nextSibling && n.nextSibling.ui.getEl()){
            this.wrap = Ext.DomHelper.insertHtml("beforeBegin",
                                n.nextSibling.ui.getEl(), buf.join(""));
        }else{
            this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf.join(""));
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.firstChild.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        this.anchor = cs[3];
        this.textNode = cs[3].firstChild;
    }
});

//backwards compat
Ext.tree.ColumnNodeUI = Ext.ux.tree.ColumnNodeUI;

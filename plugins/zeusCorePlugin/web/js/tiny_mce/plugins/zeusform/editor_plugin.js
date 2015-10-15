(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('zeusform');

	tinymce.create('tinymce.plugins.zeusformPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mcezeusform');
			ed.addCommand('mcezeusform', function() {
				ed.windowManager.open({
					file : '/formeditoradmin/browser',
					width : 520 + ed.getLang('zeusform.delta_width', 0),
					height : 280 + ed.getLang('zeusform.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register zeusform button
			ed.addButton('zeusform', {
				title : 'Formulier invoegen',
				cmd : 'mcezeusform',
				image : url + '/img/script.png'
			});

			ed.onNodeChange.add(function(ed, cm, n) {
			
			  fe = ed.selection.getNode();
			  cl = ed.dom.getAttrib(fe, 'class');
			  
				cm.setActive('zeusform', n.nodeName == 'IMG' && cl == 'form-preview');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'zeusform plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/zeusform',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('zeusform', tinymce.plugins.zeusformPlugin);
})();


function insertForm(form_id)
{
  var ed = tinyMCE.activeEditor;
  
  el = ed.selection.getNode();
  url = '/formeditoradmin/preview?id=' + form_id;
  
  ed.execCommand('mceInsertContent', false, '<img src="' + url + '" alt="" style="margin:5px 0;" class="form-preview">', {skip_undo : 1});
}
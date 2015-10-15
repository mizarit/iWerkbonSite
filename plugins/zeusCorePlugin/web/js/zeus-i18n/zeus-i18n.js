zeusI18N = Class.create();
zeusI18N.prototype =
{
  initialize: function (Options)
  {

    this.Options = {
      culture: 'nl_NL',
      default_culture: 'nl_NL',
      cultures: ['nl_NL'],
      types: [],
      data: [],
      form: 'zeus'
    };
    
    Object.extend(this.Options, Options || {});
   
    this.Init();
    
    if (this.Options.culture != this.Options.default_culture) {
      this.SwitchCulture(this.Options.culture);
    }
   
  },
  
  Init: function()
  {
    var el = document.createElement("input");
    el.type = 'hidden';
    el.name = 'i18n_values';
    el.id = 'i18n_values';
    $(this.Options.form).appendChild(el);
    
    var el = document.createElement("input");
    el.type = 'hidden';
    el.name = 'i18n_culture';
    el.id = 'i18n_culture';
    $(this.Options.form).appendChild(el);
    
    this.StoreData();
  },
  
  StoreData: function()
  {
    $('i18n_values').value = JSON.stringify(this.Options.data);
    $('i18n_culture').value = this.Options.culture;
  },
  
  SwitchCulture: function(culture)
  {
    var thiss = this;
    
    types = $H(this.Options.types);
    types.each(function(pair) {
      
      // store current data to internal array
      method = pair.value == 'rich' ? 'rich' : 'normal';
      
      thiss.Options.data[pair.key][thiss.Options.culture] = thiss.getValue(method, pair.key);
      
      // load new value from internal array
      // and if the internal value is empty, copy it from the default culture
      
      if (thiss.Options.data[pair.key][culture] == '') {
        thiss.setValue(method, pair.key, thiss.Options.data[pair.key][thiss.Options.default_culture]);
      }
      else {
        thiss.setValue(method, pair.key, thiss.Options.data[pair.key][culture]);
      }
    });
    
    // switch culture
    this.Options.culture = culture;
    
    // make sure we also set the selector in the ribbon if the switch was done from another source
    $('culture').value = culture;
    
    // enable/ disable the delete culture button
    if (culture == this.Options.default_culture) {
      $('delete-i18n-btn').addClassName('zeus-button-disabled');
    }
    else {
      $('delete-i18n-btn').removeClassName('zeus-button-disabled');
    }
    
    // save the current array to the hidden input field on the form
    this.StoreData();
  },
  
  deleteCulture: function()
  {
    // check if we are not deleting the default culture
    if (this.Options.culture != this.Options.default_culture) {
      if (confirm('Weet je zeker dat je deze vertaling wil verwijderen?')) {
        var thiss = this;
        
        types = $H(this.Options.types);
        types.each(function(pair) {
          
          method = pair.value == 'rich' ? 'rich' : 'normal';
          
          thiss.Options.data[pair.key][thiss.Options.culture] = null;
          
          thiss.setValue(method, pair.key, null);
          
        });
        
        this.StoreData();
        
        this.SwitchCulture(this.Options.default_culture);
      }
    }
  },
  
  getValue: function(method, field)
  {
    if (method == 'rich') {
      return tinyMCE.activeEditor.getContent();
    }
    return $(field).value;
  },
  
  setValue: function(method, field, value)
  {
    if (method == 'rich') {
 
      if (!value) value = '';
      tinyMCE.activeEditor.setContent(value);
     
      return;
    }
    
    $(field).value = value;
  },
  
  PrepareSubmit: function()
  {
    alert('woot');
  }
}

JSON.stringify = JSON.stringify || function (obj) { 
	 
	    var t = typeof (obj); 
	    if (t != "object" || obj === null) { 
	 
	        // simple data type 
	        if (t == "string") obj = '"'+obj+'"'; 
        return String(obj); 
 
	    } 
	    else { 
	 
	        // recurse array or object 
        var n, v, json = [], arr = (obj && obj.constructor == Array); 
	 
        for (n in obj) { 
            v = obj[n]; t = typeof(v); 
 
            if (t == "string") v = '"'+v+'"'; 
            else if (t == "object" && v !== null) v = JSON.stringify(v); 
 
            json.push((arr ? "" : '"' + n + '":') + String(v)); 
	        } 
 
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}"); 
    } 
}; 

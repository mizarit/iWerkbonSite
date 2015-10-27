<?php

class sfWidgetFormSchemaFormatterZeus extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"form-row\">\n  <div class=\"form-label\">%label%</div>\n  %field%%help%</div>\n\n",
    $errorRowFormat  = "%errors%\n",
    $helpFormat      = '<br>%help%',
    $decoratorFormat = "\n\n%content%\n\n";
    
    public function __construct(sfWidgetFormSchema $widgetSchema)
    {
    	parent::__construct($widgetSchema);
    	
    	$this->setErrorListFormatInARow("%errors%\n");
    }
  
}

<?php

class menuComponents extends baseMenuComponents
{
  public function executeTopmenu()
  {
    
  }
  
  public function executeCrumblepath()
  {
    $this->items = pvCrumblepath::get();
  }
}
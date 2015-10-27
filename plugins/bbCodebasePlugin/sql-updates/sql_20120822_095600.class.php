<?php

class sql_20120822_095600 implements SQL_update 
{
  public function up()
  {
    return <<<EOT
UPDATE route SET url = REPLACE(url, '/last-minutes/categorien', '/lastminutes/categorieen') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/last-minutes', '/lastminutes') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/last-minutes', '/lastminutes') WHERE object = 'Lastminute';
EOT;
  }
  
  public function down()
  {
    return <<<EOT
UPDATE route SET url = REPLACE(url, '/lastminutes/categorieen', '/last-minutes/categorien') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/lastminutes', '/last-minutes') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/lastminutes', '/last-minutes') WHERE object = 'Lastminute';
EOT;
  }
}
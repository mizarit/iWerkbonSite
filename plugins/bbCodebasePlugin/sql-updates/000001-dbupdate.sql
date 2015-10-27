UPDATE route SET url = REPLACE(url, '/last-minutes/categorien', '/lastminutes/categorieen') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/last-minutes', '/lastminutes') WHERE object = 'Category';
UPDATE route SET url = REPLACE(url, '/last-minutes', '/lastminutes') WHERE object = 'Lastminute';
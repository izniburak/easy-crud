# easy-crud

[![Total Downloads](https://poser.pugx.org/izniburak/easy-crud/d/total.svg)](https://packagist.org/packages/izniburak/easy-crud)
[![Latest Stable Version](https://poser.pugx.org/izniburak/easy-crud/v/stable.svg)](https://packagist.org/packages/izniburak/easy-crud)
[![Latest Unstable Version](https://poser.pugx.org/izniburak/easy-crud/v/unstable.svg)](https://packagist.org/packages/izniburak/easy-crud)
[![License](https://poser.pugx.org/izniburak/easy-crud/license.svg)](https://packagist.org/packages/izniburak/easy-crud)

## Install

composer.json file:
```json
{
    "require": {
        "izniburak/easy-crud": "dev-master"
    }
}
```
after run the install command.
```
$ composer install
```

OR run the following command directly.

```
$ composer require izniburak/easy-crud:dev-master
```

## Example Usage
```php
require 'vendor/autoload.php';

$crud = new \buki\easyCrud($pdoObject);

$records = $crud->prepare([
            'table' => 'test',
            'where' => [
              'id = ? AND status = ?', [10, 1]
            ],
            'orderBy' => ['id' => 'desc'],
            'limit' => 10
          ])->all();

var_dump($records);
```

## Docs 
Documentation coming soon...

## Support 
[izniburak's homepage][author-url]

[izniburak's twitter][twitter-url]

## Licence
[MIT Licence][mit-url]

[mit-url]: http://opensource.org/licenses/MIT
[doc-url]: https://github.com/izniburak/easy-crud/blob/master/DOCS.md
[author-url]: http://burakdemirtas.org
[twitter-url]: https://twitter.com/izniburak

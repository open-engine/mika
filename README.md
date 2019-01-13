[![Build Status](https://travis-ci.com/open-engine/mika.svg?branch=master)](https://travis-ci.com/open-engine/mika)
[![Latest Stable Version](https://img.shields.io/packagist/v/open-engine/mika.svg)](https://packagist.org/packages/open-engine/mika)
[![Code Quality](https://img.shields.io/scrutinizer/g/open-engine/mika.svg)](https://scrutinizer-ci.com/g/open-engine/mika)
[![Code intelligence](https://scrutinizer-ci.com/g/open-engine/mika/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/g/open-engine/mika)
[![Powered By Mika](https://img.shields.io/badge/powered%20by-mika-blue.svg)](https://github.com/open-engine/mika)
[![License](https://img.shields.io/badge/license-GPL%203-green.svg)](https://github.com/open-engine/mika/blob/master/LICENSE)

# Mika
Mika PHP Framework

## Templates
https://github.com/open-engine/mika-project-template


## Misc

To start tests run

```
bin/tests
```

## Route
```php
$routeConfig = new RouteConfig();
$routeConfig->register('default','App\Main\Controllers');
```

### Controller

```php
namespace App\Main\Controllers;

use App\Main\Models\Foo;
use Doctrine\ORM\EntityManagerInterface;
use OpenEngine\Mika\Core\Components\Http\Message\Response\Response;

/**
 * Class DefaultController
 * @package App\Main\Controllers
 */
class DefaultController
{
    /**
     * @return Response
     */
    public function defaultAction(): Response
    {
        return new Response('Hello World!');
    }
    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    public function fooAction(EntityManagerInterface $em, int $id): Response
    {
        $em->getRepository(Foo::class)->find($id);
        
        // ... code....

        return new Response('Doctrine Test');
    }
}
```

### Events

Route has 2 events - before and after calling controller action 

```php
BeforeCallActionEvent::class;
AfterCallActionEvent::class;
```

There is you can find all Events. Link (todo)


# Thor routing

## Create a route and a controller

A **HelloWorld** example.

1. In ```app/res/static/routes.yml``` :

```yaml
hello-world:
    path: "/hello/$name"
    method: GET
    parameters:
        name:
            regex: "[A-Za-z0-9 -]+"
    action:
        class: App\Actions\MainController
        method: hello
```

2. In ```app/src/Actions/MainController.php``` :

```php
namespace App\Actions;

use Thor\Http\BaseController;
use Thor\Http\Response\Response;

final class MainController extends BaseController
{
    
    public function hello(string $name = 'world'): Response
    {
        return new Response("Hello $name");
    }
    
}
```

### Alternative with ```#[Route] attribute```

1. In ```app/res/static/routes.yml``` :

```yaml
load:
    - App\Actions\MainController
```

2. In ```app/src/Actions/MainController.php``` :

```php
namespace App\Actions;

use Thor\Http\Request\Request;
use Thor\Http\Routing\Route;
use Thor\Http\BaseController;
use Thor\Http\Response\Response;

final class MainController extends BaseController
{
 
    #[Route('hello-world', '/hello/$name', Request::GET, ['name' => ['regex' => '[A-Za-z0-9 -]+']])]   
    public function hello(string $name = 'world'): Response
    {
        return new Response("Hello $name");
    }
    
}
```

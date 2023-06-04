# LOO
## UTFPR - Luiz Schons

### 1. Introdução

A Orientação a Objetos é um paradigma de programação que tem como objetivo a representação de entidades do mundo real em forma de objetos.
Esses objetos possuem características e comportamentos que são definidos por meio de atributos e métodos, respectivamente.

### 1.1. Pilares da Orientação a Objetos

A orientação a objetos possui quatro pilares que são: encapsulamento, herança, polimorfismo e abstração.

Nesse trabalho, busquei exemplificar esses pilares no contexto de um sistema de gerenciamento de dados de uma loja.

### 2. Exemplos

### 2.1. Encapsulamento

O encapsulamento é um mecanismo que permite a ocultação de informações de um objeto, ou seja, o acesso a essas informações é restrito.

No exemplo abaixo, temos a classe `Table`

```php

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    protected string $resource;
    protected array $columns;
    protected string $edit;
    protected string $delete;

    public function render()
    {
        return view('livewire.table', [
            'items' => app("App\Models\\" . $this->resource)->paginate(10)
        ]);
    }

    public function delete($id)
    {
        $model = app("App\Models\\" . $this->resource)->find($id);
        $model->delete();
    }
}

```

A classe `Table` possui os atributos `$resource`, `$columns`, `$edit` e `$delete`. Esses atributos são protegidos, ou seja, não podem ser acessados diretamente fora da classe.

### 2.2. Herança

A herança é um mecanismo que permite a criação de novas classes a partir de classes já existentes. A classe que é herdada é chamada de classe pai ou superclasse e a classe que herda é chamada de classe filha ou subclasse.

No exemplo abaixo, temos a classe `User` que herda da classe `Authenticatable` do framework Laravel.

```php

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class);
    }
}
    
```
Dessa forma, a classe `User` herda todos os atributos e métodos da classe `Authenticatable`.

Segue a classe `Authenticatable`:

```php
<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
}

```

### 2.3. Polimorfismo

O polimorfismo é um mecanismo que permite que um objeto possa ser referenciado de várias formas.

No exemplo abaixo, temos a classe `ClientController` que possui o método `store` que recebe um objeto do tipo `ClientRequest` como parâmetro.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('clients.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        DB::transaction(function() use($request) {
            $user = User::create([
                'email' => $request->get('email'),
                'name' => $request->get('name'),
                'password' => Hash::make('123456')
            ]);

            $user->client()->create([
                'address_id' => $request->get('address_id'),
            ]);
        });

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClientRequest  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        DB::transaction(function() use($request, $client) {
            $client->user->update([
                'email' => $request->get('email'),
                'name' => $request->get('name')
            ]);

            $client->update([
                'address_id' => $request->get('address_id'),
            ]);
        });

        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Client $client): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {

        DB::transaction(function () use ($client) {
            $client->user->delete();
            $client->delete();
        });

        return redirect()->route('clients.index');

    }
}

```

O método `store` recebe um objeto do tipo `StoreClientRequest` como parâmetro. Esse objeto é uma classe que herda da classe `FormRequest` do framework Laravel.

```php

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users',
            'address_id' => 'required'
        ];
    }
}


```

Que por usa vez, herda da classe `FormRequest`

### 2.4. Abstração

A abstração é o processo de esconder os detalhes de implementação e mostrar apenas a funcionalidade ao usuário.

Temos a classe `SaleController` que possui o método `index` que retorna uma view.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('sales.index');
    }
}

```

O exemplo acima é um exemplo de abstração, pois o usuário não precisa saber como a view é retornada, apenas que ela é retornada.

### 2.5. Interface

Uma interface é um contrato que especifica quais métodos uma classe deve implementar.

Temos a interface `Factory` que possui os métodos `guard` e `shouldUse`.

Essa interface é implementada pela classe `AuthManager`.

```php
<?php

namespace Illuminate\Contracts\Auth;

interface Factory
{
    /**
     * Get a guard instance by name.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard($name = null);

    /**
     * Set the default guard the factory should serve.
     *
     * @param  string  $name
     * @return void
     */
    public function shouldUse($name);
}
    
```

```php
<?php

namespace Illuminate\Auth;

use Closure;
use Illuminate\Contracts\Auth\Factory as FactoryContract;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Contracts\Auth\Guard
 * @mixin \Illuminate\Contracts\Auth\StatefulGuard
 */
class AuthManager implements FactoryContract
{
    use CreatesUserProviders;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $guards = [];

    /**
     * The user resolver shared by various services.
     *
     * Determines the default user for Gate, Request, and the Authenticatable contract.
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * Create a new Auth manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        $this->userResolver = fn ($guard = null) => $this->guard($guard)->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given guard.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException(
            "Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
        );
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $name
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator($name, array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $name, $config);
    }

    /**
     * Create a session based authentication guard.
     *
     * @param  string  $name
     * @param  array  $config
     * @return \Illuminate\Auth\SessionGuard
     */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider'] ?? null);

        $guard = new SessionGuard(
            $name,
            $provider,
            $this->app['session.store'],
        );

        // When using the remember me functionality of the authentication services we
        // will need to be set the encryption instance of the guard, which allows
        // secure, encrypted cookie values to get generated for those cookies.
        if (method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar($this->app['cookie']);
        }

        if (method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher($this->app['events']);
        }

        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
        }

        if (isset($config['remember'])) {
            $guard->setRememberDuration($config['remember']);
        }

        return $guard;
    }

    /**
     * Create a token based authentication guard.
     *
     * @param  string  $name
     * @param  array  $config
     * @return \Illuminate\Auth\TokenGuard
     */
    public function createTokenDriver($name, $config)
    {
        // The token guard implements a basic API token based guard implementation
        // that takes an API token field from the request and matches it to the
        // user in the database or another persistence layer where users are.
        $guard = new TokenGuard(
            $this->createUserProvider($config['provider'] ?? null),
            $this->app['request'],
            $config['input_key'] ?? 'api_token',
            $config['storage_key'] ?? 'api_token',
            $config['hash'] ?? false
        );

        $this->app->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    /**
     * Get the guard configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["auth.guards.{$name}"];
    }

    /**
     * Get the default authentication driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['auth.defaults.guard'];
    }

    /**
     * Set the default guard driver the factory should serve.
     *
     * @param  string  $name
     * @return void
     */
    public function shouldUse($name)
    {
        $name = $name ?: $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = fn ($name = null) => $this->guard($name)->user();
    }

    /**
     * Set the default authentication driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['auth.defaults.guard'] = $name;
    }

    /**
     * Register a new callback based request guard.
     *
     * @param  string  $driver
     * @param  callable  $callback
     * @return $this
     */
    public function viaRequest($driver, callable $callback)
    {
        return $this->extend($driver, function () use ($callback) {
            $guard = new RequestGuard($callback, $this->app['request'], $this->createUserProvider());

            $this->app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Get the user resolver callback.
     *
     * @return \Closure
     */
    public function userResolver()
    {
        return $this->userResolver;
    }

    /**
     * Set the callback to be used to resolve users.
     *
     * @param  \Closure  $userResolver
     * @return $this
     */
    public function resolveUsersUsing(Closure $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Register a custom provider creator Closure.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return $this
     */
    public function provider($name, Closure $callback)
    {
        $this->customProviderCreators[$name] = $callback;

        return $this;
    }

    /**
     * Determines if any guards have already been resolved.
     *
     * @return bool
     */
    public function hasResolvedGuards()
    {
        return count($this->guards) > 0;
    }

    /**
     * Forget all of the resolved guard instances.
     *
     * @return $this
     */
    public function forgetGuards()
    {
        $this->guards = [];

        return $this;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}

```



### 2.6. Traits

Traits são mecanismos que permitem a reutilização de código em linguagens que não suportam herança múltipla.

Temos a classe `AuthorizesRequests` que possui o método `authorize` que recebe um parâmetro `$ability` e um parâmetro `$arguments` e retorna um objeto do tipo `Response`.

```php
<?php

namespace Illuminate\Foundation\Auth\Access;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Str;

trait AuthorizesRequests
{
    /**
     * Authorize a given action for the current user.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return app(Gate::class)->authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
    }

    /**
     * Guesses the ability's name if it wasn't provided.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return array
     */
    protected function parseAbilityAndArguments($ability, $arguments)
    {
        if (is_string($ability) && ! str_contains($ability, '\\')) {
            return [$ability, $arguments];
        }

        $method = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];

        return [$this->normalizeGuessedAbilityName($method), $ability];
    }

    /**
     * Normalize the ability name that has been guessed from the method name.
     *
     * @param  string  $ability
     * @return string
     */
    protected function normalizeGuessedAbilityName($ability)
    {
        $map = $this->resourceAbilityMap();

        return $map[$ability] ?? $ability;
    }

    /**
     * Authorize a resource action based on the incoming request.
     *
     * @param  string|array  $model
     * @param  string|array|null  $parameter
     * @param  array  $options
     * @param  \Illuminate\Http\Request|null  $request
     * @return void
     */
    public function authorizeResource($model, $parameter = null, array $options = [], $request = null)
    {
        $model = is_array($model) ? implode(',', $model) : $model;

        $parameter = is_array($parameter) ? implode(',', $parameter) : $parameter;

        $parameter = $parameter ?: Str::snake(class_basename($model));

        $middleware = [];

        foreach ($this->resourceAbilityMap() as $method => $ability) {
            $modelName = in_array($method, $this->resourceMethodsWithoutModels()) ? $model : $parameter;

            $middleware["can:{$ability},{$modelName}"][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName, $options)->only($methods);
        }
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'viewAny',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['index', 'create', 'store'];
    }
}
```

### 2.7. Exceptions

Exceções são erros que ocorrem durante a execução de um programa. O Laravel possui uma classe `Handler` que é responsável por tratar as exceções que ocorrem durante a execução do programa.

No exemplo abaixo, temos o método `store` da classe `ClientController` que recebe um objeto do tipo `StoreClientRequest` e retorna um objeto do tipo `Response`.

Antes de retornar o é feito um `try/catch` para tratar as exceções que podem ocorrer durante a execução do método e validar se a requisição é válida.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('clients.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function store(StoreClientRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('clients.create')->withErrors($e->errors());
        }

        DB::transaction(function() use($request) {
            $user = User::create([
                'email' => $request->get('email'),
                'name' => $request->get('name'),
                'password' => Hash::make('123456')
            ]);

            $user->client()->create([
                'address_id' => $request->get('address_id'),
            ]);
        });

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClientRequest  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        DB::transaction(function() use($request, $client) {
            $client->user->update([
                'email' => $request->get('email'),
                'name' => $request->get('name')
            ]);

            $client->update([
                'address_id' => $request->get('address_id'),
            ]);
        });

        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Client $client): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {

        DB::transaction(function () use ($client) {
            $client->user->delete();
            $client->delete();
        });

        return redirect()->route('clients.index');

    }
}

```

---

### 3. Extra

### 3.1. Classe abstrata

Uma classe abstrata é uma classe que não pode ser instanciada, mas pode ser herdada.

Temos a classe `Controller` que é uma classe abstrata.

```php
<?php

namespace Illuminate\Routing;

use BadMethodCallException;

abstract class Controller
{
    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Register middleware on the controller.
     *
     * @param  \Closure|array|string  $middleware
     * @param  array  $options
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        return $this->{$method}(...array_values($parameters));
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
    
```

Dessa classe, temos diversas classes que herdam dela, como por exemplo a classe `Controller` que é uma classe abstrata.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
    
```

### 3.2. Enums

Um enum é um tipo de dado que consiste em um conjunto de constantes nomeadas.

Temos a classe `Status` que é um enum.

```php
<?php

namespace App\Enums;

enum Status: string
{
    CASE PENDING = 'P';
    CASE APPROVED = 'A';
    CASE CANCELED = 'C';
}
```

Dessa classe, temos a classe `Sale` que possui um atributo do tipo `Status`.

```php
<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'seller_id',
        'sold_at',
        'status',
        'total_amount'
    ];

    protected $casts = [
        'status' => Status::class
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
    
```

### 3.3. Docker

Docker é uma plataforma de código aberto que permite que você crie, teste e implante aplicativos rapidamente. O Docker pacote aplicativos em contêineres padronizados para executar em qualquer ambiente de trabalho, incluindo nuvem, virtualizados e locais.

Temos o arquivo `docker-compose.yml` que é um arquivo de configuração do Docker.

```yaml
# For more information: https://laravel.com/docs/sail
version: '3'
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.2
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.2/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - pgsql
    pgsql:
        image: 'postgres:15'
        ports:
            - '${FORWARD_DB_PORT:-5432}:5432'
        environment:
            PGPASSWORD: '${DB_PASSWORD:-secret}'
            POSTGRES_DB: '${DB_DATABASE}'
            POSTGRES_USER: '${DB_USERNAME}'
            POSTGRES_PASSWORD: '${DB_PASSWORD:-secret}'
        volumes:
            - 'sail-pgsql:/var/lib/postgresql/data'
            - './vendor/laravel/sail/database/pgsql/create-testing-database.sql:/docker-entrypoint-initdb.d/10-create-testing-database.sql'
        networks:
            - sail
        healthcheck:
            test: ["CMD", "pg_isready", "-q", "-d", "${DB_DATABASE}", "-U", "${DB_USERNAME}"]
            retries: 3
            timeout: 5s
networks:
    sail:
        driver: bridge
volumes:
    sail-pgsql:
        driver: local

```

Com o Docker, podemos subir varios containers, como por exemplo o container do `Postgres` que é um banco de dados.

### 3.4. Eloquent

O Eloquent ORM incluído com o Laravel fornece uma implementação de ActiveRecord simples e bonita para trabalhar com seu banco de dados. Cada tabela de banco de dados possui um "Model" correspondente que é usado para interagir com essa tabela. Os modelos permitem que você consulte dados em suas tabelas, bem como inserir novos registros na tabela.

### 3.5. OpenAI API

A API do OpenAI é uma API de texto que permite que os desenvolvedores usem os modelos de linguagem de última geração da OpenAI em seus produtos. Ele permite que você envie texto para o modelo e receba texto de volta como resposta.

Temos a classe `OpenAI` que é uma classe que faz a integração com a API do OpenAI.

```php
<?php

declare(strict_types=1);

namespace OpenAI\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OpenAI\Resources\Completions completions()
 * @method static \OpenAI\Resources\Embeddings embeddings()
 * @method static \OpenAI\Resources\Edits edits()
 * @method static \OpenAI\Resources\Files files()
 * @method static \OpenAI\Resources\FineTunes fineTunes()
 * @method static \OpenAI\Resources\Images images()
 * @method static \OpenAI\Resources\Models models()
 * @method static \OpenAI\Resources\Moderations moderations()
 */
final class OpenAI extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'openai';
    }
}

```
---

### 4. Arquitetura

Foi utilizado o padrão de arquitetura MVC (Model-View-Controller) que é um padrão de projeto de software, ou padrão de arquitetura de software formulado na década de 1970, focado no reuso de código e a separação de conceitos em três camadas interconectadas, onde a apresentação dos dados e interação dos usuários (front-end) são separados dos métodos que interagem com o banco de dados (back-end).


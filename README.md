
# my-php-test
my-php-test

# Router
```php
Route::prefix('call')->group(function () {
    Route::post('add', 'CallController@newCall');
    Route::post('done', 'CallController@endOfTheCall');
});

Route::prefix('employee')->group(function () {
    Route::post('want', 'CallController@newCallWant');
    Route::post('add', 'CallController@newEmployee');
});
```

# CallController
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndOfTheCallRequest;
use App\Http\Requests\NewCallRequest;
use App\Http\Requests\NewCallWantRequest;
use App\Http\Requests\NewEmployeeRequest;
use App\Repositories\Call\CallRepositoryInterface;

class CallController extends Controller
{
    public function __construct(CallRepositoryInterface $callRepository)
    {
        $this->callRepository = $callRepository;
    }

    public function newCall(NewCallRequest $request)
    {
        $response = $this->callRepository->newCall($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function endOfTheCall(EndOfTheCallRequest $request)
    {
        $response = $this->callRepository->endOfTheCall($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function newCallWant(NewCallWantRequest $request)
    {
        $response = $this->callRepository->newCallWant($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function newEmployee(NewEmployeeRequest $request)
    {
        $response = $this->callRepository->newEmployee($request);
        return response()->json($response["data"], $response["statusCode"]);
    }
}

```

# CallRepositoryInterface
```php
<?php

namespace App\Repositories\Call;

interface CallRepositoryInterface {
    public function newCall($request);
    public function endOfTheCall($request);
    public function newEmployee($request);
    public function newCallWant($request);
}
```


# CallTypesRepositoryInterface
```php
<?php

namespace App\Repositories\Call\CallTypes;

interface CallTypesRepositoryInterface {
    public function setData($hash);
    public function initOpration();
}
```

# CreateCallsTable 
```php
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string('hash');
            $table->enum('priority', ['high', 'low']);
            $table->enum('state', ['waiting', 'doing', 'done']);
            $table->timestamps();
        });
```

# CreateEmployeesTable 
```php
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->enum('priority', ['1', '2', '3']);
            $table->enum('state', ['waiting', 'busy']);
            $table->timestamps();
        });
```

If you want to use or test it you can call this tree routes
## http://127.0.0.1/api/employee/want
parametres
id -> which is employee id


## http://127.0.0.1/api/employee/add
parametres
priority -> that is priority


## http://127.0.0.1/api/call/done
parametres
hash -> that is the hash of the call


## http://127.0.0.1/api/call/add
parametres
type -> "urgent" of "normal"
hash -> that is call hash

for env you can easy use https://github.com/cmohammadc/ENV-php-nginx-mariadb-phpmyadmin-varnish

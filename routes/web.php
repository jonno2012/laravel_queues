<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
////    (new \App\Jobs\SendWelcomeEmail())->handle();
//
//    \App\Jobs\SendWelcomeEmail::dispatch()->delay(5);
////    for($i=1; $i<101; $i++) {
////        \App\Jobs\SendWelcomeEmail::dispatch();
////    }
//
////    \App\Jobs\ProcessPayment::dispatch()->onQueue('payments');
//
//
//    return view('welcome');
//});

Route::get('/workflows1', function () {
//   $chain = [
//       new \App\Jobs\PullRepo(),
//       new \App\Jobs\RunTests(),
//       new \App\Jobs\Deploy()
//   ];

   $batch = [
       new \App\Jobs\PullRepo(),
       new \App\Jobs\PullRepo(),
       new \App\Jobs\PullRepo(),
   ];

//   \Illuminate\Support\Facades\Bus::chain($chain)->dispatch();
    \Illuminate\Support\Facades\Bus::batch($batch)
        ->allowFailures()
        ->catch(function($batch, $e) {
            // to be executed when a job fails
        })
        ->then(function ($batch) {
            // to be executed if no jobs fail and once they have all completed
        })
        ->finally(function($batch) {
            // will execute if jobs complete even if one of them has failed
        })
        ->onQueue('deployments')
        ->onConnection('database')
        ->dispatch();

   return view('welcome');
});

Route::get('/workflows2', function() {
    // we can dispatch a chain within a batch:
    // * both batches will be executed in parallel.
    $batch = [
        [
            new \App\Jobs\PullRepo(),
            new \App\Jobs\RunTests(),
            new \App\Jobs\Deploy()
        ],
        [
            new \App\Jobs\PullRepo(),
            new \App\Jobs\RunTests(),
            new \App\Jobs\Deploy()
        ],
    ];

    \Illuminate\Support\Facades\Bus::batch($batch)
        ->allowFailures()
        ->dispatch();

    // we can also call a batch inside a chain
    \Illuminate\Support\Facades\Bus::chain([
        new \App\Jobs\Deploy(),
        function() {
        \Illuminate\Support\Facades\Bus::batch([
            new \App\Jobs\PullRepo(),
            new \App\Jobs\PullRepo(),
            new \App\Jobs\PullRepo(),
        ])->dispatch();
        }
    ]);

    return view('welcome');
});

Route::get('/redistest', function() {
    (new \App\Jobs\Deploy())::dispatch();

//    \Cache::store('redis')->put('Laradock', 'Awesome', 10);
    return view('welcome');
});


// uses the transaction closure to ensure job is dispatched only after a db action has completed.
// if transaction is rolled back the job will not be dispatched. this can be configured globally in the queue.php config by
// using the after_commit prop to the connection which is being used. this can prevent a lot of confusing bugs.
Route::get('/', function () {
    \Illuminate\Support\Facades\DB::transaction(function () {
        $user = \App\Models\User::create(['name' => 'sdfsdf']);

        \App\Jobs\SendWelcomeEmail::dispatch($user)->afterCommit();
    });

    return view('welcome');
});

<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\Api\GetCardController;
use App\Http\Controllers\Api\GetPackIdController;
use App\Http\Controllers\Api\GetPackInformationController;
use App\Http\Controllers\Api\GetPackRewardChanceController;
use App\Http\Controllers\Api\GetUserBalanceController;
use App\Http\Controllers\BurnCardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\EOSController;
use App\Http\Controllers\FaucetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ParseCollectionController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\Staking\ActiveStakingController;
use App\Http\Controllers\Staking\StakingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UnpackingController;
use App\Http\Controllers\UpdateNFTController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [HomeController::class, 'index']);
Route::get('/console', [ConsoleController::class, 'index']);
Route::get('/console/santa', [ConsoleController::class, 'santa']);
Route::get('/console/nansen', [ConsoleController::class, 'nansen']);

Route::get('/staking', [StakingController::class, 'index'])->middleware('waxauth');
Route::get('/staking/list', [StakingController::class, 'inventory'])->middleware('waxauth');
Route::get('/staking/claim', [EOSController::class, 'claim'])->middleware('waxauth')->name('claim');

Route::get('/token_staking', [ActiveStakingController::class, 'index'])->middleware('waxauth');
Route::post('/token_staking/stack', [ActiveStakingController::class, 'stack'])->middleware('waxauth');
Route::post('/token_staking/cancel', [ActiveStakingController::class, 'cancel'])->middleware('waxauth');
Route::post('/token_staking/claim', [ActiveStakingController::class, 'claim'])->middleware('waxauth');


Route::get('/collection', [CollectionController::class, 'index']);
Route::get('/collection/list', [CollectionController::class, 'ofList']);

Route::get('/unpacking', [UnpackingController::class, 'index'])->middleware(['waxauth']);
Route::get('/unpacking/list', [UnpackingController::class, 'ofList'])->middleware(['waxauth']);
Route::post('/unpacking/unpack', [UnpackingController::class, 'unpack'])->middleware(['waxauth']);

Route::get('/faucet', [FaucetController::class, 'index'])->middleware([]);
Route::post('/faucet/claim', [FaucetController::class, 'claim'])->middleware([]);

Route::get('/market', [MarketController::class, 'index']);
Route::get('/market/list', [MarketController::class, 'ofList']);
Route::post('/buy/card', [MarketController::class, 'buy_card']);


Route::get('/tournament', [TournamentController::class, 'index'])->middleware('technicalWork');

Route::get('/rewards', [RewardController::class, 'index']);//->middleware('auth');

Route::get('/subscription', [SubscriptionController::class, 'index'])->middleware('auth');

Route::post('/burn', BurnCardController::class)->middleware('waxauth');

Route::get('/update/pool', [PoolController::class, 'update'])->middleware('auth')->name('pool.update');

Route::get('/dashboard', [AdminPanelController::class, 'index'])->middleware(['auth']);
Route::post('/dashboard/nft', UpdateNFTController::class)->middleware(['auth'])->name('saveNFT');

Route::get('/parse/collection', ParseCollectionController::class)->name('parseCollection');


Route::prefix('api')->group(function () {
    Route::get('/get_balance/{userName}', [GetUserBalanceController::class, 'getBalance']);
    Route::get('/pack/{userName}', GetPackInformationController::class);
    Route::get('/reward_chance/{packTemplateId}', GetPackRewardChanceController::class);
    Route::get('/getCard', GetCardController::class);
});

Route::get('/api/get_balance/{userName}', [GetUserBalanceController::class, 'getBalance']);


require __DIR__.'/auth.php';

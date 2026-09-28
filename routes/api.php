<?php

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('mobile.')->group(function () {
    require __DIR__ . '/mobile.php';
});

# Testing FCM Notification
Route::get('/test-fcm-v1', function (FirebaseService $fcm) {
    // Put an actual FCM token from your React Native app here
    $token = 'dz5tp84OTNmtw3zFNqVfGS:APA91bFxCFNhcEau_Et-SHC_f12ZJAtMsMUeMiWlpRr_aSzb40dDLydL6IbD06PfTWv5avqdwdHduMNMglORBEzebBY42LrISrZaGLZN5GesyPw5DaH8FX4';

    $title = 'Hello from Laravel (v1)';
    $body = 'This is an FCM HTTP v1 notification';
    $data = [
        'type' => 'test',
        'source' => 'laravel',
    ];

    $result = $fcm->sendToDevice($token, $title, $body, $data);

    return response()->json($result);
});
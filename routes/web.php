<?php

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
//if (App::runningUnitTests() && is_development_server()) {
    /**
     * CACHE BUSTERS
     */
     //dd('oo');exit;
     Route::get('{path}', function($filename) {
     return Bust::css($filename);
            })->where('path', '.*\.css$');
     App::make('cachebuster.StripSessionCookiesFilter')->addPattern('|\.css$|');

    Route::pattern('hash', '[a-zA-Z0-9]+');
    Route::pattern('allowedExtensions', '(jpg|png|gif|js|css|woff|ttf|svg|eot){1}');
    Route::pattern('folders', '[a-zA-Z0-9_\/]*');
    Route::pattern('fileName', '.+');

    $guesser = \Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser::getInstance();

    Route::get(
        '{folders}{fileName}-{hash}.{allowedExtensions}',
        array(
            function ($folders, $fileName, $hash, $extension) use ($guesser) {
                $shortPath = $folders . $fileName . '.' . $extension;
                $path = public_path() . DIRECTORY_SEPARATOR . $shortPath;
                if (!file_exists($path)) {
                    return App::abort(404);
                }

                $headers = [
                    'Content-Type' => $guesser->guess($path),
                    'Cache-Control' => 'max-age=31536000',
                    'Pragma' => 'cache',
                    'Expires' => 'Sun, 17 Jan 2038 19:14:07 GMT'
                ];

                if (strtolower($extension) == 'css') {

                }

                return Response::make(file_get_contents($path), 200, $headers);
            }
        )
    );
//}
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

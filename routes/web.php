<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware gro up. Now create something great!
|
 */

Route::get('oweAmount', 'Api\RatingController@oweAmount');
Route::get('driver_invoice', 'DriverDashboardController@driver_invoice');
Route::get('call_again', 'Api\RiderController@call_again');
Route::match(array('GET', 'POST'),'apple_callback', 'UserController@apple_callback');
Route::get('app/{type}', 'HomeController@redirect_to_app')->name('redirect_to_app');

Route::group(['middleware' =>'canInstall'], function () {
	Route::group(['middleware' =>'locale'], function () {
		Route::get('currency_cron', 'HomeController@currency_cron');
		Route::get('/', 'HomeController@index');
	});
});

Route::group(['middleware' =>'locale'], function () {

	Route::get('help', 'HomeController@help');
	Route::get('help/topic/{id}/{category}', 'HomeController@help');
	Route::get('help/article/{id}/{question}', 'HomeController@help');
	Route::get('ajax_help_search', 'HomeController@ajax_help_search');

	Route::post('set_session', 'HomeController@set_session');
	Route::get('user_disabled', 'UserController@user_disabled');

	Route::match(array('GET', 'POST'), 'signin_driver', 'UserController@signin_driver');
	Route::match(array('GET', 'POST'),'signin_rider', 'UserController@signin_rider')->name('rider.signin');
	Route::match(array('GET', 'POST'),'signin_company', 'UserController@signin_company');
	Route::get('facebook_login', 'UserController@facebook_login');
	Route::get('forgot_password_driver', 'UserController@forgot_password');
	Route::get('forgot_password_rider', 'UserController@forgot_password');
	Route::get('forgot_password_company', 'UserController@forgot_password');
	Route::post('forgotpassword', 'UserController@forgotpassword');
	Route::match(array('GET', 'POST'), 'reset_password', 'UserController@reset_password');
	Route::match(array('GET', 'POST'), 'company/reset_password', 'UserController@company_reset_password');
	Route::get('forgot_password_link/{id}', 'EmailController@forgot_password_link');
	Route::match(array('GET', 'POST'),'signup_rider', 'UserController@signup_rider');
	Route::match(array('GET', 'POST'),'signup_driver', 'UserController@signup_driver');
	Route::match(array('GET', 'POST'),'signup_company', 'UserController@signup_company');

	Route::get('facebookAuthenticate', 'UserController@facebookAuthenticate');
	Route::get('googleAuthenticate', 'UserController@googleAuthenticate');
	
	Route::view('signin', 'user.signin');
	Route::view('signup', 'user.signup');

	Route::get('safety', 'RideController@safety');
	Route::get('ride', 'RideController@ride');
	Route::get('how_it_works', 'RideController@how_it_works');

	Route::get('drive', 'DriveController@drive');
	Route::get('requirements', 'DriveController@requirements');
	Route::get('driver_app', 'DriveController@driver_app');
	Route::get('drive_safety', 'DriveController@drive_safety');

	// signup functionality
	Route::post('rider_register', 'UserController@rider_register');
	Route::post('driver_register', 'UserController@driver_register');
	Route::post('company_register', 'UserController@company_register');
	Route::post('login', 'UserController@login');
	Route::post('login_driver', 'UserController@login_driver');
	Route::post('ajax_trips/{id}', 'DashboardController@ajax_trips');

	Route::post('change_mobile_number', 'DriverDashboardController@change_mobile_number');
	Route::post('profile_upload', 'DriverDashboardController@profile_upload');
	Route::get('download_invoice/{id}', 'DriverDashboardController@download_invoice');
	Route::get('download_rider_invoice/{id}', 'DashboardController@download_rider_invoice');
});

// Rider Routes..
Route::group(['middleware' => ['locale','rider_guest']], function () {
	Route::get('trip', 'DashboardController@trip')->name('rider.trips');
	Route::get('profile', 'DashboardController@profile');
	Route::get('payment', 'DashboardController@payment');
	Route::get('trip_detail/{id}', 'DashboardController@trip_detail');
	Route::post('rider_rating/{rating}/{trip_id}', 'DashboardController@rider_rating');
	Route::post('trip_detail/rider_rating/{rating}/{trip_id}', 'DashboardController@rider_rating');
	Route::get('trip_invoice/{id}', 'DashboardController@trip_invoice');
	Route::get('invoice_download/{id}', 'DashboardController@invoice_download');
	Route::post('rider_update_profile/{id}', 'DashboardController@update_profile');
	Route::get('referral', 'DashboardController@referral')->name('referral');
	Route::post('ajax_referral_data/{id}', 'DashboardController@ajax_referral_data');
});

// Driver Routes..
Route::group(['middleware' => ['locale','driver_guest']], function () {
	Route::get('driver_profile', 'DriverDashboardController@driver_profile');
	Route::get('documents/{id}', 'DriverDashboardController@documents');
	Route::post('document_upload/{id}', 'DriverDashboardController@document_upload');
	Route::get('add_vehicle', 'DriverDashboardController@add_vehicle');
	Route::get('driver_payment', 'DriverDashboardController@driver_payment');

	Route::get('driver_invoice/{id}', 'DriverDashboardController@driver_invoice');
	Route::get('driver_banking', 'DriverDashboardController@driver_banking');
	Route::get('driver_trip', 'DriverDashboardController@driver_trip');
	Route::get('driver_trip_detail/{id}', 'DriverDashboardController@driver_trip_detail');

	Route::post('ajax_payment', 'DriverDashboardController@ajax_payment');
	Route::get('driver_referral', 'DashboardController@driver_referral')->name('driver_referral');

	// profile update
	Route::post('driver_update_profile/{id}', 'DriverDashboardController@driver_update_profile');
	Route::get('driver_invoice', 'DriverDashboardController@show_invoice');
	Route::get('print_invoice/{id}', 'DriverDashboardController@print_invoice');

	// Payout Preferences
	Route::match(['get', 'post'],'payout_preferences','UserController@payout_preferences')->name('driver_payout_preference');
});

Route::get('sign_out', function () {
	$user_type = @Auth::user()->user_type;
	Auth::logout();
	if (@$user_type == 'Rider') {
		return redirect('signin_rider');
	} else {
		return redirect('signin_driver');
	}

});

Route::group(['prefix' => (LOGIN_USER_TYPE=='company')?'company':'admin', 'middleware' =>'admin_guest'], function () {

	if (LOGIN_USER_TYPE == 'company') {
		Route::get('logout', function () {
			Auth::guard('company')->logout();
		    return redirect('signin_company');
		});
		Route::get('profile', function () {
		    return redirect('company/edit_company/'.auth('company')->id());
		});

		Route::match(['get', 'post'],'payout_preferences','CompanyController@payout_preferences')->name('company_payout_preference');
		Route::post('set_session', 'HomeController@set_session');
	}
});

// Static page route
Route::get('clear__l-log', 'HomeController@clearLog');
Route::get('show__l-log', 'HomeController@showLog');
Route::get('export-db', 'HomeController@exportDB');

Route::get('{name}', 'HomeController@static_pages');
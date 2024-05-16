<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageCourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/media', [MediaController::class, 'index']);
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);
Route::get('/courses', [CourseController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum', 'throttle:500|2200,1']], function () {
});
Route::group(['middleware' => ['restrictRole:admin']], function () {
});

// chapter route
Route::get('/chapters', [ChapterController::class, 'index']);
Route::get('/chapters/{chapter}', [ChapterController::class, 'show']);
Route::post('/chapters', [ChapterController::class, 'create']);
Route::put('/chapters/{chapter}', [ChapterController::class, 'update']);
Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy']);
// lesson route
Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/lessons/{lesson}', [LessonController::class, 'show']);
Route::post('/lessons', [LessonController::class, 'create']);
Route::put('/lessons/{lesson}', [LessonController::class, 'update']);
Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
// media  route
Route::post('/media', [MediaController::class, 'store']);
Route::delete('/media/{media}', [MediaController::class, 'destroy']);
// mentors route
Route::get('/mentors', [MentorController::class, 'index']);
Route::get('/mentors/{mentor}', [MentorController::class, 'show']);
Route::post('/mentors', [MentorController::class, 'create']);
Route::put('/mentors/{mentor}', [MentorController::class, 'update']);
Route::delete('/mentors/{mentor}', [MentorController::class, 'destroy']);
// image course  route
Route::post('/image-course', [ImageCourseController::class, 'create']);
Route::delete('/image-course/{imageCourse}', [ImageCourseController::class, 'destroy']);

Route::post('/courses', [CourseController::class, 'create']);
Route::put('/courses/{course}', [CourseController::class, 'update']);
Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
// });

Route::post('/logout', [LogoutController::class, 'logout']);
// user route
Route::put('/users/{user}', [UserController::class, 'update']);
Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/users/{user}', [UserController::class, 'getUserById']);




// course route
Route::get('/courses/{course}', [CourseController::class, 'show']);


// my course  route
Route::get('/my-course', [MyCourseController::class, 'index']);
Route::post('/my-course', [MyCourseController::class, 'create']);
// Route::post('/my-course/premium', [MyCourseController::class, 'createPremiumAccess']);

// review route
Route::post('/review', [ReviewController::class, 'create']);
Route::put('/review/{review}', [ReviewController::class, 'update']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);

// payment order route
Route::get('/order', [OrderController::class, 'index']);
Route::post('/order', [OrderController::class, 'create']);

// webhook route
Route::post('/webhook', [WebhookController::class, 'midtransHandler']);

// reset password route
Route::post('forget-password', [ResetPasswordController::class, 'store']);
Route::put('forget-password', [ResetPasswordController::class, 'update']);
// });

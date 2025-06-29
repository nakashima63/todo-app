<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\TodoController;

Route::get('/', [TodoController::class, 'index'])->name('todos.index');

Route::resource('todos', TodoController::class);

// エラーテスト用ルート
Route::post('/test-error', function () {
    Log::error('手動エラーテスト: ' . now() . ' - ユーザーがエラーボタンをクリックしました');
    throw new Exception('CloudWatch Logs テスト用エラー - ' . now());
})->name('test.error');

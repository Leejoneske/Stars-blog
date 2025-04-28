 Route::post('/telegram-webhook', function () {
    $telegram = app()->make(App\Services\TelegramService::class);
    return $telegram->handleWebhook(request()->all());
});

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mark transactions as completed when end date has passed (run daily at 00:05)
Schedule::command('transactions:complete-overdue')->dailyAt('00:05');

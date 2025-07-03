<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:generate-morning-attendance')->dailyAt('05:00')->weekdays();; // Untuk generate absensi pagi
Schedule::command('app:update-to-alfa')->dailyAt('17.00')->weekdays(); // Untuk update absensi menjadi Alfa sore

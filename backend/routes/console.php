<?php

Schedule::command('articles:import')->hourly();
Schedule::command('newsletter:send')->dailyAt('08:00');

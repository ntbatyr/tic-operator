<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->dropIfExists('user_invest_programs');

Capsule::schema()->create('user_invest_programs', function (Blueprint $table) {
    try {
        $table->increments('id');
        $table->bigInteger('user_id');
        $table->bigInteger('invest_program_id');
        $table->decimal('amount', 12, 6);
        $table->unique(['user_id', 'invest_program_id']);
    } catch (Exception $exception) {
        echo "Error: {$exception->getMessage()} \n";
        echo "Trace: {$exception->getTraceAsString()} \n";
    }
});

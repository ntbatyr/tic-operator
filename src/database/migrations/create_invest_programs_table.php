<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->dropIfExists('invest_programs');

Capsule::schema()->create('invest_programs', function (Blueprint $table) {
    try {
        $table->increments('id');
        $table->string('name');
        $table->decimal('min_deposit', 12, 6);
        $table->float('annual_percent');
        $table->boolean('active')->default(0);
    } catch (Exception $exception) {
        echo "Error: {$exception->getMessage()} \n";
        echo "Trace: {$exception->getTraceAsString()} \n";
    }
});

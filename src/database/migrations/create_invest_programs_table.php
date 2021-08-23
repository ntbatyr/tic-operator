<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->table('invest_programs', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->decimal('min_deposit', 12, 6);
    $table->float('annual_percent');
    $table->boolean('active')->default(0);
});

<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->create('invest_programs', function (Blueprint $table) {
    $table->increments('id');
    $table->bigInteger('user_id');
    $table->bigInteger('invest_program_id');
    $table->decimal('amount', 12, 6);
    $table->unique(['user_id', 'invest_program_id']);
});

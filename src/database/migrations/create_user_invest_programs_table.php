<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('user_invest_programs')) {
    Capsule::schema()->create('user_invest_programs', function (Blueprint $table) {
        $table->increments('id');
        $table->bigInteger('user_id');
        $table->bigInteger('invest_program_id');
        $table->decimal('amount', 12, 6);
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent();
        $table->unique(['user_id', 'invest_program_id']);
    });
}

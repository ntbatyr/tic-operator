<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->create('accounts', function (Blueprint $table) {
    $table->increments('id');
    $table->string('user_id');
    $table->bigInteger('amount')->default(0);

});

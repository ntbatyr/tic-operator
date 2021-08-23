<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->table('migrations', function (Blueprint $table) {
    $table->increments('id');
    $table->string('migration');
    $table->bigInteger('batch')->default(0);
});

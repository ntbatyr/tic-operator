<?php

namespace Models;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public function __construct()
    {
        $dbConfig = app_config('database');

        $capsule = new Capsule();
        $capsule->addConnection($dbConfig['default']);

        $capsule->bootEloquent();
    }
}
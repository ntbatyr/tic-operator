<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Migration extends Model
{
    protected $table = 'migrations';
    protected $fillable = [
        'id', 'migration', 'batch',
    ];
}
<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class InvestProgram extends Model
{
    protected $table = 'invest_programs';

    protected $fillable = [
        'id',
        'name',
        'min_deposit',
        'annual_percent',
        'active',
    ];
}
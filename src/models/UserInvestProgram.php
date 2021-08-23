<?php

namespace Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property int $invest_program_id
 * @property float $amount
 * @property InvestProgram $investProgram
 */
class UserInvestProgram extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user_invest_programs';

    protected $fillable = [
        'user_id',
        'invest_program_id',
        'amount'
    ];

    public function investProgram(): BelongsTo
    {
        return $this->belongsTo(InvestProgram::class, 'invest_program_id');
    }
}
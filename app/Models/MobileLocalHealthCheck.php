<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['check_key', 'check_value', 'checked_at'])]
class MobileLocalHealthCheck extends Model
{
    protected $connection = 'mobile_local';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_at' => 'immutable_datetime',
        ];
    }
}

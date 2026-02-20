<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    use HasUlids;

    protected $guarded = ['id'];
}

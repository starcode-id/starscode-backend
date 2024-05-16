<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    use HasFactory;
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'email';
    protected $fillable = [
        'email', 'token'
    ];
    protected $casts = [
        'created_at'
        => 'datetime:Y-m-d H:m:s',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class booklendinginfo extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'BookID',
        'LendingUserID',
        'LendingDate',
        'ReturnExpectedDate',
        'ReturnDate',
        'Note',
        'CreateUserID',
        'CreateDateTime',
        'UpdateUserID',
        'UpdateDateTime',
        'LockVer'
    ];
}

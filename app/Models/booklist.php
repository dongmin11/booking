<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class booklist extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Sortable;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'BookName',
        'Author',
        'Publisher',
        'PublicationDate',
        'PurchaserID',
        'PurchaseDate',
        'CreateUserID',
        'Note',
    ];
    public $sortable = ['BookName', 'Author', 'Publisher', 'PublicationDate','PurchaseDate'];
}

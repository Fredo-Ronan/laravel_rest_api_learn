<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "contents";
    protected $primarykey = "id";

    protected $fillable = [
        'title',
        'released_year',
        'genre',
        'type',
    ];
}

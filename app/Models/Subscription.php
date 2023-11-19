<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = "subscriptions";
    protected $primarykey = "id";
    protected $fillable = [
        'id_user',
        'category',
        'price',
        'transaction_date',
    ];

    public function User(){
        return $this->belongsTo(User::class, 'id_user');
    }
}

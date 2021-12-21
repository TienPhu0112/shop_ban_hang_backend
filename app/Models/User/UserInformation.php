<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    protected $table = 'user_informations';

    protected $fillable = [
        'phone_num',
        'province',
        'district',
        'address'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}

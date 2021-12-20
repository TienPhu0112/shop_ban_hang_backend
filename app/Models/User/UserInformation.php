<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}

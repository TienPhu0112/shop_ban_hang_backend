<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'dob',
        'phone_num',
        'address'
    ];

    protected $table = 'admin_informations';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

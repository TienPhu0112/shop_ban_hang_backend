<?php

namespace App\Models\Admin;

use App\Models\Blog\Blog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    public function adminInformation()
    {
        return $this->hasOne(AdminInfomation::class, 'admin_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'admin_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_permissions', 'admin_id');
    }
}

<?php

namespace App\Models\Blog;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

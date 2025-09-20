<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $table = 'team_members';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'photo',
        'bio',
        'banned',
    ];

    protected $casts = [
        'permissions' => 'array', // để json tự động decode/encode
        'banned' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
}
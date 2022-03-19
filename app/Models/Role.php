<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

     const IS_SUPER_ADMIN = 1;
     const IS_ADMIN = 2;
     const IS_MANAGER = 3;
     const IS_USER = 4;

     public function users() {
         return $this->hasMany(User::class);
     }
}

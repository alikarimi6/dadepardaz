<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePermission extends Model
{
    protected $fillable = [
        'route_name',
        'permission_name',
    ];
}

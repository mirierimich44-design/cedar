<?php

namespace Modules\MadeToOrder\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name','unit','base_price','description'];
}

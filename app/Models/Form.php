<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $fillable = ['country_id','name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }
}

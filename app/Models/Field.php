<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;
    protected $fillable = ['form_id', 'name', 'type', 'is_required', 'category'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}

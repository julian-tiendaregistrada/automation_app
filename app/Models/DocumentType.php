<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = ['name'];

    // public function clients(): HasMany
    // {
    //    return $this->hasMany(Client::class);
    // }
}

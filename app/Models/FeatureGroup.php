<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureGroup extends Model
{
    protected $fillable = ['product_id', 'name'];

    public function features()
    {
        return $this->hasMany(Feature::class);
    }
}

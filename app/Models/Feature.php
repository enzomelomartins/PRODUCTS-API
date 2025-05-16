<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['feature_group_id', 'key', 'value'];

    public function group()
    {
        return $this->belongsTo(FeatureGroup::class, 'feature_group_id');
    }
}

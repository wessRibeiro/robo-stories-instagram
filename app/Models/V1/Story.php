<?php

namespace Louder\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table      = 'Historias';
    public    $timestamps = false;

    public function influencer(){
        return $this->belongsTo('Louder\Models\V1\Influencer', 'iduser');
    }
}

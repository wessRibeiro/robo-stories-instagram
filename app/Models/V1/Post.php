<?php

namespace Louder\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'Posts';

    public function influencer(){
        return $this->belongsTo('Louder\Models\V1\Influencer', 'iduser');
    }
}

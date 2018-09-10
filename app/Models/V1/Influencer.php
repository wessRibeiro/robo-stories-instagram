<?php

namespace Louder\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    protected $table = 'Influencers';



    public function stories()
    {
        return $this->hasMany('Louder\Models\V1\Story', 'iduser', 'id');
    }
}

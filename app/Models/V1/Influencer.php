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

    public function profile()
    {
        return $this->hasOne('Louder\Models\V1\Profile', 'idInfluencer', 'idInstagram');
    }
}

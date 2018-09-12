<?php

namespace Louder\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'Perfis';

    public function influencer(){
        return $this->belongsTo('Louder\Models\V1\Influencer', 'idInstagram','idInfluencer');
    }
}

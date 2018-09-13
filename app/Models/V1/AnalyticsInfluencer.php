<?php

namespace Louder\Models\V1;

use Illuminate\Database\Eloquent\Model;

class AnalyticsInfluencer extends Model
{
    protected $table = 'AnalyticsInfluencers';

    public function influencer()
    {
        return $this->hasOne('Louder\Models\V1\Influencer', 'idInstagram', 'idInstagram');
    }
}

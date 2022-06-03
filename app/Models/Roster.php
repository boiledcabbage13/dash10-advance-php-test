<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use HasFactory;

    protected $table = 'roster';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $appends  = [
        'age',
    ];

    public function team() {
        return $this->hasOne(Team::class, 'code', 'team_code');
    }

    public function player_total() {
        return $this->hasOne(PlayerTotal::class, 'player_id', 'id');
    }

    public function getAgeAttribute() {
        $dateOfBirth = new \DateTime($this->dob);
        $today = new \Datetime(date('m.d.y'));
        $age = $today->diff($dateOfBirth);

        return $age->y;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PlayerTotal extends Model
{
    use HasFactory;

    protected $table = 'player_totals';

    protected $appends  = [
        'total_points',
        'field_goals_pct',
        '3pt_pct',
        '2pt_pct',
        'free_throws_pct',
        'total_rebounds',
    ];

    public function getTotalPointsAttribute()
    {
        $threePointMultiplier = 3;
        $twoPointMultiplier = 2;
        $totalPoints = ($this['3pt'] * $threePointMultiplier) + ($this['2pt'] * $twoPointMultiplier) + $this['free_throws'];
        
        return $totalPoints;
    }

    public function getFieldGoalsPctAttribute() {
        return $this['field_goals_attempted'] ? (round($this['field_goals'] / $this['field_goals_attempted'], 2) * 100) . '%' : 0;
    }

    public function get3ptPctAttribute() {
        return $this['3pt_attempted'] ? (round($this['3pt'] / $this['3pt_attempted'], 2) * 100) . '%' : 0;
    }

    public function get2ptPctAttribute() {
        return $this['2pt_attempted'] ? (round($this['2pt'] / $this['2pt_attempted'], 2) * 100) . '%' : 0;
    }

    public function getFreeThrowsPctAttribute() {
        return $this['free_throws_attempted'] ? (round($this['free_throws'] / $this['free_throws_attempted'], 2) * 100) . '%' : 0;
    }

    public function getTotalReboundsAttribute() {
        return $this['offensive_rebounds'] + $this['defensive_rebounds'];
    }
}

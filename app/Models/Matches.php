<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'naam',
        'beschrijving',
        'status',
        'created_at',
        'updated_at',
    ];

    // Define relationships with correct foreign key
    public function gebruikersScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id');
    }

    public function matchUserScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'match_gebruikers_scores', 'wedstrijd_id', 'gebruiker_id')->withPivot('points');
    }
}
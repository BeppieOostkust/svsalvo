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

    // Define relationships if necessary
    public function gebruikersScores()
    {
        return $this->hasMany(MatchGebruikerScore::class);
    }
}

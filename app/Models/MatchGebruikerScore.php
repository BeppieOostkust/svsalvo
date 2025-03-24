<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchGebruikerScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedstrijd_id',
        'gebruiker_id',
        'linker_kaart_6',
        'linker_kaart_7',
        'linker_kaart_8',
        'linker_kaart_9',
        'linker_kaart_10',
        'rechter_kaart_6',
        'rechter_kaart_7',
        'rechter_kaart_8',
        'rechter_kaart_9',
        'rechter_kaart_10',
        'aantal_schoten_buiten_tijd',
        'afwaarderingen',
        'totale_punten',
    ];

    public function matches()
    {
        return $this->belongsTo(Matches::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

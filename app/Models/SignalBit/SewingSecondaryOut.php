<?php

namespace App\Models\SignalBit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingSecondaryOut extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sb';

    protected $table = 'output_secondary_out';

    protected $guarded=[];

    public function secondaryIn()
    {
        return $this->belongsTo(SewingSecondaryIn::class, 'secondary_in_id', 'id');
    }
}

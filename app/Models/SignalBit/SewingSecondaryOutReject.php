<?php

namespace App\Models\SignalBit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewingSecondaryOutReject extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sb';

    protected $table = 'output_secondary_out_reject';

    protected $guarded=[];

    public function secondaryOut()
    {
        return $this->belongsTo(SewingSecondaryOut::class, 'secondary_out_id', 'id');
    }
}

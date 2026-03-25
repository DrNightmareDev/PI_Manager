<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Models;

use Illuminate\Database\Eloquent\Model;

class StaticPlanet extends Model
{
    protected $table = 'seat_pi_manager_static_planets';

    protected $primaryKey = 'planet_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $guarded = [];
}

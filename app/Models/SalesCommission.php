<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCommission extends Model
{
    use HasFactory;

    protected $table = 'sales_commission_view';

    public $incrementing = false;

    public $timestamps = false;

    public static function getColumns(): array
    {
        return [
            'company',
            'seller',
            'client',
            'city',
            'state',
            'sold_at',
            'status',
            'total_amount',
            'commission'
        ];
    }


}

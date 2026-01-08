<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'ref_id',
        'trx_id',
        'qr_code',
        'amount',
        'status',
        'transaction_status',
        'transaction_desc',
        'brand_name',
        'buyer_ref',
        'trx_date',
        'expired_at',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PaymentGateway extends Model
{
    use HasTranslations;

    public $translatable = ['description']; // الأعمدة التي تحتاج إلى ترجمة
    protected $guarded  = [];

    public function settings()
    {
        return $this->hasMany(PaymentGatewaySetting::class, 'gateway_id');
    }

}

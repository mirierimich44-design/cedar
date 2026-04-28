<?php

namespace Modules\Pesapal\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PesapalSetting extends Model
{
    protected $table = 'pesapal_settings';

    protected $guarded = ['id'];

    protected $hidden = [
        'consumer_key',
        'consumer_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function getDecryptedConsumerKeyAttribute()
    {
        try {
            return $this->consumer_key ? Crypt::decryptString($this->consumer_key) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDecryptedConsumerSecretAttribute()
    {
        try {
            return $this->consumer_secret ? Crypt::decryptString($this->consumer_secret) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setConsumerKeyAttribute($value)
    {
        $this->attributes['consumer_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setConsumerSecretAttribute($value)
    {
        $this->attributes['consumer_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getBaseUrl()
    {
        return $this->environment === 'production'
            ? 'https://pay.pesapal.com/v3'
            : 'https://cybqa.pesapal.com/pesapalv3';
    }

    public function isProduction()
    {
        return $this->environment === 'production';
    }

    public function isConfigured()
    {
        return $this->is_active
            && $this->decrypted_consumer_key
            && $this->decrypted_consumer_secret
            && $this->ipn_id;
    }

    public static function getForBusiness($business_id)
    {
        return self::where('business_id', $business_id)->first();
    }
}

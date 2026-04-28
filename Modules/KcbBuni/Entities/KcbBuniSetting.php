<?php

namespace Modules\KcbBuni\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class KcbBuniSetting extends Model
{
    protected $table = 'kcb_buni_settings';

    protected $guarded = ['id'];

    public function setAppKeyAttribute($value)
    {
        $this->attributes['app_key'] = Crypt::encryptString($value);
    }

    public function getDecryptedAppKeyAttribute()
    {
        return $this->app_key ? Crypt::decryptString($this->app_key) : null;
    }

    public function setAppSecretAttribute($value)
    {
        $this->attributes['app_secret'] = Crypt::encryptString($value);
    }

    public function getDecryptedAppSecretAttribute()
    {
        return $this->app_secret ? Crypt::decryptString($this->app_secret) : null;
    }

    public function isConfigured()
    {
        return $this->decrypted_app_key && $this->decrypted_app_secret;
    }

    public static function getForBusiness($business_id)
    {
        return self::where('business_id', $business_id)->first();
    }
}

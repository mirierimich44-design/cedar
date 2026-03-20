<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MpesaSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpesa_settings';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'consumer_key',
        'consumer_secret',
        'passkey',
        'security_credential',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    /**
     * Get the business that owns this setting.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Get decrypted consumer key.
     */
    public function getDecryptedConsumerKeyAttribute()
    {
        try {
            return $this->consumer_key ? Crypt::decryptString($this->consumer_key) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get decrypted consumer secret.
     */
    public function getDecryptedConsumerSecretAttribute()
    {
        try {
            return $this->consumer_secret ? Crypt::decryptString($this->consumer_secret) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get decrypted passkey.
     */
    public function getDecryptedPasskeyAttribute()
    {
        try {
            return $this->passkey ? Crypt::decryptString($this->passkey) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get decrypted security credential.
     */
    public function getDecryptedSecurityCredentialAttribute()
    {
        try {
            return $this->security_credential ? Crypt::decryptString($this->security_credential) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted consumer key.
     */
    public function setConsumerKeyAttribute($value)
    {
        $this->attributes['consumer_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted consumer secret.
     */
    public function setConsumerSecretAttribute($value)
    {
        $this->attributes['consumer_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted passkey.
     */
    public function setPasskeyAttribute($value)
    {
        $this->attributes['passkey'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted security credential.
     */
    public function setSecurityCredentialAttribute($value)
    {
        $this->attributes['security_credential'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Get the base URL based on environment.
     */
    public function getBaseUrl()
    {
        return $this->environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Check if using production environment.
     */
    public function isProduction()
    {
        return $this->environment === 'production';
    }

    /**
     * Get the effective shortcode (till or paybill).
     */
    public function getEffectiveShortcode()
    {
        return $this->shortcode_type === 'till' ? $this->till_number : $this->shortcode;
    }

    /**
     * Get the transaction type for API calls.
     */
    public function getTransactionType()
    {
        return $this->shortcode_type === 'till' 
            ? 'CustomerBuyGoodsOnline' 
            : 'CustomerPayBillOnline';
    }

    /**
     * Get settings for a business.
     */
    public static function getForBusiness($business_id)
    {
        return self::where('business_id', $business_id)->first();
    }
}

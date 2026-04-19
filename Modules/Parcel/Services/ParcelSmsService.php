<?php

namespace Modules\Parcel\Services;

use App\Utils\Util;
use App\Business;
use Modules\Parcel\Entities\Parcel;

class ParcelSmsService
{
    protected $util;

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    /**
     * Send SMS notification to parcel recipient or sender.
     *
     * @param Parcel $parcel
     * @param string $template_type
     * @return bool
     */
    public function sendNotification(Parcel $parcel, string $template_type)
    {
        $business = Business::find($parcel->business_id);
        $sms_settings = $business->sms_settings;

        if (empty($sms_settings) || (empty($sms_settings['url']) && empty($sms_settings['nexmo_key']) && empty($sms_settings['twilio_sid']) && empty($sms_settings['advanta_api_key']))) {
            return false;
        }

        $message = $this->getMessageFromTemplate($parcel, $template_type);
        if (!$message) {
            return false;
        }

        $to = ($template_type === 'collected') ? $parcel->sender_phone : $parcel->recipient_phone;

        $data = [
            'sms_settings' => $sms_settings,
            'mobile_number' => $to,
            'sms_body' => $message
        ];

        try {
            return $this->util->sendSms($data);
        } catch (\Exception $e) {
            \Log::error('Parcel SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get message body based on status.
     */
    protected function getMessageFromTemplate(Parcel $parcel, string $type)
    {
        $tracking_url = url('/track/' . $parcel->waybill_number);
        
        switch ($type) {
            case 'booked':
                return "Your parcel from {$parcel->sender_name} has been booked. Waybill: {$parcel->waybill_number}. Track it: {$tracking_url}";
            
            case 'in_transit':
                return "Your parcel {$parcel->waybill_number} is now in transit to {$parcel->destination->name}.";
            
            case 'arrived':
                return "Your parcel {$parcel->waybill_number} has arrived at {$parcel->destination->name}. Please collect it during working hours.";
            
            case 'collected':
                return "Your parcel {$parcel->waybill_number} has been successfully collected by {$parcel->recipient_name}. Thank you!";
            
            case 'cod_paid':
                return "Payment of KES {$parcel->charge_amount} for parcel {$parcel->waybill_number} received. Your parcel is ready for collection.";

            default:
                return null;
        }
    }
}

<?php

namespace Modules\Parcel\Services;

use App\Utils\MpesaService;
use Modules\Parcel\Entities\Parcel;

class ParcelMpesaService
{
    protected $mpesaService;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Business ID will be set dynamically when needed
    }

    /**
     * Set the business ID for M-Pesa service.
     * 
     * @param int $businessId
     * @return $this
     */
    public function setBusiness($businessId)
    {
        $this->mpesaService = new MpesaService($businessId);
        return $this;
    }

    /**
     * Initiate STK Push payment for a parcel.
     *
     * @param Parcel $parcel
     * @return array
     */
    public function initiateBookingPayment(Parcel $parcel)
    {
        if (!$this->mpesaService) {
            $this->setBusiness($parcel->business_id);
        }

        if (!$this->mpesaService->isConfigured()) {
            return [
                'success' => false,
                'message' => 'M-Pesa is not configured for this business.'
            ];
        }

        return $this->mpesaService->initiateSTKPush(
            $parcel->sender_phone,
            $parcel->charge_amount,
            $parcel->waybill_number,
            "Parcel " . $parcel->waybill_number
        );
    }
}

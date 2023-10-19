<?php

namespace App\Lists;

use App\Entity\Traits\ListTrait;

class GasStationStatusReference
{
    use ListTrait;

    public const CREATED = 'created';

    public const UPDATED_TO_ADDRESS_FORMATED = 'updated_to_address_formated';
    public const ADDRESS_FORMATED = 'address_formated';
    public const ADDRESS_ERROR_FORMATED = 'address_error_formated';

    public const UPDATED_TO_FOUND_IN_TEXTSEARCH = 'updated_to_found_in_textSearch';
    public const FOUND_IN_TEXTSEARCH = 'found_in_textSearch';
    public const NOT_FOUND_IN_TEXTSEARCH = 'not_found_in_textSearch';

    public const UPDATED_TO_FOUND_IN_DETAILS = 'updated_to_found_in_details';
    public const FOUND_IN_DETAILS = 'found_in_details';
    public const NOT_FOUND_IN_DETAILS = 'not_found_in_details';

    public const PLACE_ID_ANOMALY = 'place_id_anomaly';

    public const WAITING_VALIDATION = 'waiting_validation';
    public const VALIDATION_REJECTED = 'validation_rejected';
    public const VALIDATED = 'validated';

    public const OPEN = 'open';
    public const CLOSED = 'closed';
}

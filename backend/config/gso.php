<?php

return [
    // Number of years allowed to backdate a Delivery Receipt. Can be overridden via .env
    'dr_backdate_years' => env('GSO_DR_BACKDATE_YEARS', 5),
];

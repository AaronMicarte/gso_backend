<?php

return [
    // Number of years allowed to backdate a Delivery Receipt. Can be overridden via .env
    'dr_backdate_years' => env('GSO_DR_BACKDATE_YEARS', 1),

    // Base URL of the frontend (Next.js). Used for QR payloads so scans land on the UI.
    'frontend_url' => env('GSO_FRONTEND_URL', 'http://localhost:3000'),
];

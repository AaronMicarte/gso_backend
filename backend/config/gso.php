<?php

return [
    // Number of years allowed to backdate a Delivery Receipt. Can be overridden via .env
    'dr_backdate_years' => env('GSO_DR_BACKDATE_YEARS', 1),

    // Base URL of the frontend (Next.js). Used for QR payloads so scans land on the UI.
    'frontend_url' => env('GSO_FRONTEND_URL', 'http://localhost:3000'),

    // Default agency name used in accountability printouts.
    'entity_name' => env('GSO_ENTITY_NAME', 'CITY GOVERNMENT OF EL SALVADOR'),

    // Optional UACS fund cluster code/name for printed forms. Kept blank by default.
    'fund_cluster' => env('GSO_FUND_CLUSTER', ''),

    // Opening entry Office/Officer label for Property Card (e.g., "Cyron Micarte - GSO").
    'gso_officer_name' => env('GSO_OFFICER_NAME', ''),
    'gso_department_name' => env('GSO_DEPARTMENT_NAME', 'GSO'),
];

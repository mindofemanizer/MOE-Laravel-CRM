<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Model Bindings
    |--------------------------------------------------------------------------
    */

    'models' => [

        'user' => App\Models\User::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */

    'tables' => [

        'contacts' => 'crm_contacts',

        'segments' => 'crm_segments',

        'contact_segment' => 'crm_contact_segment',

        'leads' => 'crm_leads',

        'activities' => 'crm_activities',

    ],

];

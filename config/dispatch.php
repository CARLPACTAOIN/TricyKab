<?php

return [

    'max_candidates' => (int) env('DISPATCH_MAX_CANDIDATES', 5),

    'offer_ttl_seconds' => (int) env('DISPATCH_OFFER_TTL', 60),

];

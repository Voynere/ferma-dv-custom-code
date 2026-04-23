<?php
/**
 * Copy this file to ferma_fasovka_sync_once.config.php in the same directory (WordPress root).
 * ferma_fasovka_sync_once.config.php is gitignored — do not commit it.
 */
return array(
    // 'basic' or 'bearer'
    'FERMA_MS_AUTH_MODE' => 'basic',

    'FERMA_MS_BASIC_LOGIN' => '',
    'FERMA_MS_BASIC_PASSWORD' => '',

    // only if FERMA_MS_AUTH_MODE === 'bearer'
    'FERMA_MS_BEARER_TOKEN' => '',

    'FERMA_MS_LIMIT' => 100,
    'FERMA_ACF_RAZBIVKA_FIELD_KEY' => 'field_627cbc0e2d6f3',
    'FERMA_MS_MAX_ROWS' => 0,
    'FERMA_MS_ONLY' => true,
);

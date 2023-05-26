<?php

return [
    /*
     * The host to use when listening for debug server connections.
     */
    'host' => \Hyperf\Support\env('DUMP_SERVER_HOST', 'tcp://127.0.0.1:9912'),
];
<?php

return array(
    '{{url.category}}' => $config->url . '/' . $config->category->slug . '/',
    '{{url.category:%s}}' => $config->url . '/' . $config->category->slug . '/$1'
);
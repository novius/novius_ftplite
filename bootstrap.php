<?php
\Event::register('404.end', function($params) {
    \Novius\Ftplite\Ftplite::sendFile($params['url']);
});
\Event::register('front.404NotFound', function($params) {
    \Novius\Ftplite\Ftplite::sendFile($params['url'].'.html');
});

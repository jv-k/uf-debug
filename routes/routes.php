<?php

$app->group('/debug', function() {

    // debug home
    $this->get('', 'UserFrosting\Sprinkle\UfDebug\DebugController:pageDebug');

})->add('authGuard');

// middleware for checking if the site is set to offline for development/updates:
// ->add('UserFrosting\Sprinkle\Controlpanel\PageController:checkOffline');

?>
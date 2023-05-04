<?php

if (!defined('ABSPATH')) exit;

$backgroundColor = daftplugInstantify::getSetting('pwaWebShareButtonBgColor');
$iconColor = daftplugInstantify::getSetting('pwaWebShareButtonIconColor');
$buttonPosition = explode('-', daftplugInstantify::getSetting('pwaWebShareButtonPosition'));

?>

<div class="daftplugPublicWebShareButton" style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $iconColor; ?>; <?php echo $buttonPosition[0]; ?>: 20px; <?php echo $buttonPosition[1]; ?>: 20px;">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" version="1.1" class="daftplugPublicWebShareButton_icon -iconShare" style="fill: <?php echo $iconColor; ?>;">
        <g>
            <path d="M 18.398438 2.398438 C 16.632812 2.398438 15.199219 3.832031 15.199219 5.601562 C 15.203125 5.824219 15.226562 6.050781 15.273438 6.269531 L 8.007812 9.902344 C 7.402344 9.203125 6.523438 8.800781 5.601562 8.800781 C 3.832031 8.800781 2.398438 10.234375 2.398438 12 C 2.398438 13.765625 3.832031 15.199219 5.601562 15.199219 C 6.523438 15.199219 7.402344 14.796875 8.011719 14.101562 L 15.273438 17.730469 C 15.222656 17.949219 15.199219 18.175781 15.199219 18.398438 C 15.199219 20.167969 16.632812 21.601562 18.398438 21.601562 C 20.167969 21.601562 21.601562 20.167969 21.601562 18.398438 C 21.601562 16.632812 20.167969 15.199219 18.398438 15.199219 C 17.476562 15.199219 16.597656 15.601562 15.988281 16.300781 L 8.726562 12.667969 C 8.777344 12.449219 8.800781 12.226562 8.800781 12 C 8.796875 11.777344 8.773438 11.550781 8.726562 11.332031 L 15.992188 7.699219 C 16.597656 8.398438 17.476562 8.796875 18.398438 8.800781 C 20.167969 8.800781 21.601562 7.367188 21.601562 5.601562 C 21.601562 3.832031 20.167969 2.398438 18.398438 2.398438 Z M 18.398438 2.398438"/>
        </g>
    </svg>
</div>
<?php

if (!defined('ABSPATH')) exit;

$backgroundColor = daftplugInstantify::getSetting('pwaPushButtonBgColor');
$bellIconColor = daftplugInstantify::getSetting('pwaPushButtonIconColor');
$buttonPosition = explode('-', daftplugInstantify::getSetting('pwaPushButtonPosition'));
$tooltipFlow = ($buttonPosition[1] == 'left') ? 'right' : 'left';

?>

<div class="daftplugPublicPushButton" data-tooltip="<?php esc_html_e('Subscribe to notifications', self::$textDomain); ?>" data-tooltip-flow="<?php echo $tooltipFlow; ?>" style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $bellIconColor; ?>; <?php echo $buttonPosition[0]; ?>: 20px; <?php echo $buttonPosition[1]; ?>: 20px;">
    <svg class="daftplugPublicPushButton_icon -iconBell" style="fill: <?php echo $bellIconColor; ?>; stroke: <?php echo $bellIconColor; ?>;">
        <use href="#iconBell"></use>
    </svg>
    <svg class="daftplugPublicPushButton_icon -iconLoading" style="fill: <?php echo $backgroundColor; ?>; stroke: <?php echo $bellIconColor; ?>;">
        <use href="#iconLoading"></use>
    </svg>
    <svg class="daftplugPublicPushButton_icon -iconBellOff" style="fill: <?php echo $bellIconColor; ?>; stroke: <?php echo $bellIconColor; ?>;">
        <use href="#iconBellOff"></use>
    </svg>
</div>

<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconBell">
        <g stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </g>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconBellOff">
        <g stroke-linecap="round" stroke-linejoin="round">
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            <path d="M18.63 13A17.89 17.89 0 0 1 18 8"></path>
            <path d="M6.26 6.26A5.86 5.86 0 0 0 6 8c0 7-3 9-3 9h14"></path>
            <path d="M18 8a6 6 0 0 0-9.33-5"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        </g>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconLoading">
        <g stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="2" x2="12" y2="6"></line>
            <line x1="12" y1="18" x2="12" y2="22"></line>
            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
            <line x1="2" y1="12" x2="6" y2="12"></line>
            <line x1="18" y1="12" x2="22" y2="12"></line>
            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
        </g>
    </symbol>
</svg>
<?php

if (!defined('ABSPATH')) exit;

$appIcon = wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
$bgColor = daftplugInstantify::getSetting('pwaBackgroundColor');
$style = daftplugInstantify::getSetting('pwaPreloaderStyle');

echo '<div class="daftplugPublic" data-daftplug-plugin="'.$this->optionName.'">';
	switch ($style) {
		case 'default':
			echo '<div class="daftplugPublicPreloader -default" style="background-color: '.$bgColor.';">
					<div class="daftplugPublicPreloader_icon" style="background-image: url('.$appIcon.');"></div>
				</div>';
			break;
		case 'spinner':
			echo '<div class="daftplugPublicPreloader -spinner" style="background-color: #f9f9f9;">
					<div class="daftplugPublicPreloader_spins">
						<div>
							<div>
								<div>
								<div>
									<div>
									<div>
										<div>
										<div>
											<div>
											<div></div>
											</div>
										</div>
										</div>
									</div>
									</div>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
			break;
		case 'redirect':
			echo '<div class="daftplugPublicPreloader -redirect" style="background-color: #f1c40f;">
					<div class="daftplugPublicPreloader_body">
						<span>
							<span></span>
							<span></span>
							<span></span>
							<span></span>
						</span>
						<div class="daftplugPublicPreloader_base">
							<span></span>
							<div class="daftplugPublicPreloader_face"></div>
						</div>
					</div>
					<div class="daftplugPublicPreloader_fazers">
						<span></span>
						<span></span>
						<span></span>
						<span></span>
					</div>
				</div>';
		break;
		case 'percent':
			echo '<div class="daftplugPublicPreloader -percent" style="background-color: #111;">
					<span class="daftplugPublicPreloader_counter"></span>
					<div class="daftplugPublicPreloader_progress">
						<span class="daftplugPublicPreloader_fill"></span>
					</div>
				</div>';
		break;
		case 'skeleton':
			echo '<div class="daftplugPublicPreloader -skeleton" style="background-color: '.$bgColor.';"></div>';
			break;
		default:
	}
echo '</div>';

?>
<?php

if (!defined('ABSPATH')) exit;

?>
<footer class="daftplugAdminFooter -disabled">
    <script src="https://www.paypal.com/sdk/js?client-id=AWR70BHOOULzL6XYCo8Q8ejdU0k9pctqEFD2kuwLDre0R8MMfGtNKTDBrdUats4niomH1TibSft-YVch&enable-funding=venmo&currency=USD" data-sdk-integration-source="button-factory"></script>
    <div class="daftplugAdminPopup" data-popup="reviewModal">
        <div class="daftplugAdminPopup_container">
            <div class="daftplugAdminRating">
                <h4 class="daftplugAdminRating_title"><?php esc_html_e('Rate Instantify on CodeCanyon', $this->textDomain); ?></h4>
                <p class="daftplugAdminRating_description"><?php esc_html_e('Enjoying Instantify plugin by DaftPlug? Please star rate us on CodeCanyon. Your rating is important for us and it helps us to improve the plugin and our service.', $this->textDomain); ?></p>
                <img class="daftplugAdminRating_image" src="<?php echo plugins_url('admin/assets/img/image-review.png', $this->pluginFile)?>"/>
                <a class="daftplugAdminButton daftplugAdminRating_button" target="_blank" href="https://codecanyon.net/downloads#item-25757693"><?php esc_html_e('Rate Instantify', $this->textDomain); ?></a>
            </div>
        </div>
    </div>
    <div class="daftplugAdminPopup" data-popup="getAndroidAppModal">
        <div class="daftplugAdminPopup_container">
            <div class="daftplugAdminRating">
                <img src="<?php echo plugins_url('admin/assets/img/icon-android.png', $this->pluginFile); ?>" style="margin-bottom: 10px;"/>
                <h4 class="daftplugAdminRating_title">
                    <?php esc_html_e('Get Android app for only ', $this->textDomain); ?>
                    <div class="daftplugAdminPricingTable_price" style="margin: 0;">
                        <sup class="daftplugAdminPricingTable_currency">$</sup>
                        <span class="daftplugAdminPricingTable_amount">19</span>
                    </div>
                </h4>
                <p class="daftplugAdminRating_description"><?php _e('After the successful payment, we will create your Android app package for your website and send it to you along with the next few steps to submit and make it live on Google Play Store. If you are unable to proceed with the payment, please send us an email at <a class="daftplugAdminLink" href="mailto:support@daftplug.com">support@daftplug.com</a> or use the <a class="daftplugAdminLink" href="https://codecanyon.net/user/daftplug#contact" target="_blank">contact form</a> found on our CodeCanyon profile page.', $this->textDomain); ?></p>
                <div class="daftplugAdminField">
                    <p class="daftplugAdminField_description"><?php esc_html_e('Enter email address where we\'ll send your Android app package.', $this->textDomain); ?></p>
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="<?php esc_html_e('Invalid email address', $this->textDomain); ?>" name="deliveryEmail" id="deliveryEmail" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Delivery Email', $this->textDomain); ?>" value="" autocomplete="off" required>
                    </div>
                </div>
                <div class="daftplugAdminPaypalButtonContainer" data-tooltip="<?php esc_html_e('Please fill the delivery email field to proceed with the payment.', $this->textDomain); ?>" data-tooltip-flow="top">
                    <div class="daftplugAdminPaypalButton -android -disabled" data-target="android" data-price="19"></div>
                </div>
                <div class="daftplugAdminPaypalButtonResponse"></div>
            </div>
        </div>
	</div>
    <div class="daftplugAdminPopup" data-popup="getAndroiosAppModal">
        <div class="daftplugAdminPopup_container">
            <div class="daftplugAdminRating">
                <img src="<?php echo plugins_url('admin/assets/img/icon-androios.png', $this->pluginFile); ?>" style="margin-bottom: 10px;"/>
                <h4 class="daftplugAdminRating_title">
                    <?php esc_html_e('Get Android & iOS apps for only ', $this->textDomain); ?>
                    <div class="daftplugAdminPricingTable_price" style="margin: 0;">
                        <sup class="daftplugAdminPricingTable_currency">$</sup>
                        <span class="daftplugAdminPricingTable_amount">37</span>
                    </div>
                </h4>
                <p class="daftplugAdminRating_description"><?php _e('After the successful payment, we will create your Android and iOS app packages for your website and send them to you along with the next few steps to submit and make them live on Google Play Store and Apple App Store. If you are unable to proceed with the payment, please send us an email at <a class="daftplugAdminLink" href="mailto:support@daftplug.com">support@daftplug.com</a> or use the <a class="daftplugAdminLink" href="https://codecanyon.net/user/daftplug#contact" target="_blank">contact form</a> found on our CodeCanyon profile page.', $this->textDomain); ?></p>
                <div class="daftplugAdminField">
                    <p class="daftplugAdminField_description"><?php esc_html_e('Enter email address where we\'ll send your Android and iOS app packages.', $this->textDomain); ?></p>
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="<?php esc_html_e('Invalid email address', $this->textDomain); ?>" name="deliveryEmail" id="deliveryEmail" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Delivery Email', $this->textDomain); ?>" value="" autocomplete="off" required>
                    </div>
                </div>
                <div class="daftplugAdminPaypalButtonContainer" data-tooltip="<?php esc_html_e('Please fill the delivery email field to proceed with the payment.', $this->textDomain); ?>" data-tooltip-flow="top">
                    <div class="daftplugAdminPaypalButton -androios -disabled" data-target="androios" data-price="37"></div>
                </div>
                <div class="daftplugAdminPaypalButtonResponse"></div>
            </div>
        </div>
	</div>
    <div class="daftplugAdminPopup" data-popup="getIosAppModal">
        <div class="daftplugAdminPopup_container">
            <div class="daftplugAdminRating">
                <img src="<?php echo plugins_url('admin/assets/img/icon-ios.png', $this->pluginFile); ?>" style="margin-bottom: 10px;"/>
                <h4 class="daftplugAdminRating_title">
                    <?php esc_html_e('Get iOS app for only ', $this->textDomain); ?>
                    <div class="daftplugAdminPricingTable_price" style="margin: 0;">
                        <sup class="daftplugAdminPricingTable_currency">$</sup>
                        <span class="daftplugAdminPricingTable_amount">29</span>
                    </div>
                </h4>
                <p class="daftplugAdminRating_description"><?php _e('After the successful payment, we will create your iOS app package for your website and send it to you along with the next few steps to submit and make it live on App Store. If you are unable to proceed with the payment, please send us an email at <a class="daftplugAdminLink" href="mailto:support@daftplug.com">support@daftplug.com</a> or use the <a class="daftplugAdminLink" href="https://codecanyon.net/user/daftplug#contact" target="_blank">contact form</a> found on our CodeCanyon profile page.', $this->textDomain); ?></p>
                <div class="daftplugAdminField">
                    <p class="daftplugAdminField_description"><?php esc_html_e('Enter email address where we\'ll send your iOS app package.', $this->textDomain); ?></p>
                    <div class="daftplugAdminInputText -flexAuto">
                        <input type="text" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="<?php esc_html_e('Invalid email address', $this->textDomain); ?>" name="deliveryEmail" id="deliveryEmail" class="daftplugAdminInputText_field" data-placeholder="<?php esc_html_e('Delivery Email', $this->textDomain); ?>" value="" autocomplete="off" required>
                    </div>
                </div>
                <div class="daftplugAdminPaypalButtonContainer" data-tooltip="<?php esc_html_e('Please fill the delivery email field to proceed with the payment.', $this->textDomain); ?>" data-tooltip-flow="top">
                    <div class="daftplugAdminPaypalButton -ios -disabled" data-target="ios" data-price="29"></div>
                </div>
                <div class="daftplugAdminPaypalButtonResponse"></div>
            </div>
        </div>
	</div>
	<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
		<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconOverview">
        	<g stroke-linecap="round" stroke-linejoin="round">
        		<circle fill="none" stroke-miterlimit="10" cx="12" cy="12" r="2"></circle>
            	<circle fill="none" stroke-miterlimit="10" cx="12" cy="12" r="11"></circle>
            	<path fill="none" stroke-miterlimit="10" d="M6.3,10C6.1,10.6,6,11.3,6,12" stroke-linecap="butt"></path>
            	<path fill="none" stroke-miterlimit="10" d="M18,12c0-3.3-2.7-6-6-6 c-0.7,0-1.4,0.1-2,0.3" stroke-linecap="butt"></path>
            	<line fill="none" stroke-miterlimit="10" x1="10.6" y1="10.6" x2="7" y2="7"></line>
        	</g>
    	</symbol>
    	<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconSettings">
    		<g stroke-linecap="round" stroke-linejoin="round">
    			<circle fill="none" stroke-miterlimit="10" cx="12" cy="12" r="3"></circle>
    			<path fill="none" stroke-miterlimit="10" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke-linecap="butt"></path>
    		</g>
    	</symbol>
		<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconSupport">
			<g stroke-linecap="round" stroke-linejoin="round">
		    	<circle fill="none" stroke-miterlimit="10" cx="12" cy="12" r="11"></circle>
		    	<path fill="none" stroke-miterlimit="10" d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" stroke-linecap="butt"></path>
		    	<line fill="none" stroke-miterlimit="10" x1="12" y1="17" x2="12" y2="17"></line>
		    </g>
		</symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="iconSubmit">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.7008908,5.37931459 L2.7008908,5.37931459 C2.9224607,5.60207651 3.2826628,5.60304283 3.50542472,5.38147293 C3.52232305,5.36466502 3.53814843,5.34681177 3.55280728,5.32801875 L5.34805194,3.02646954 L5.34805194,10.3480519 C5.34805194,10.7081129 5.63993903,11 6,11 L6,11 C6.36006097,11 6.65194806,10.7081129 6.65194806,10.3480519 L6.65194806,3.02646954 L8.44719272,5.32801875 C8.6404327,5.57575732 8.99791646,5.61993715 9.24565503,5.42669716 C9.26444805,5.41203831 9.28230129,5.39621293 9.2991092,5.37931459 L9.2991092,5.37931459 C9.55605877,5.12098268 9.57132199,4.70855346 9.33416991,4.43193577 L6.75918715,1.42843795 C6.39972025,1.00915046 5.76841509,0.960656296 5.34912761,1.32012319 C5.31030645,1.35340566 5.27409532,1.38961679 5.24081285,1.42843795 L2.66583009,4.43193577 C2.42867801,4.70855346 2.44394123,5.12098268 2.7008908,5.37931459 Z"></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="iconLoading">
            <g stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="8" r="7.5"></circle>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="iconSuccess">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M4.76499011,6.7673683 L8.2641848,3.26100386 C8.61147835,2.91299871 9.15190114,2.91299871 9.49919469,3.26100386 C9.51164115,3.27347582 9.52370806,3.28637357 9.53537662,3.29967699 C9.83511755,3.64141434 9.81891834,4.17816549 9.49919469,4.49854425 L5.18121271,8.82537365 C4.94885368,9.05820878 4.58112654,9.05820878 4.34876751,8.82537365 L2.50080531,6.97362503 C2.48835885,6.96115307 2.47629194,6.94825532 2.46462338,6.93495189 C2.16488245,6.59321455 2.18108166,6.0564634 2.50080531,5.73608464 C2.84809886,5.3880795 3.38852165,5.3880795 3.7358152,5.73608464 L4.76499011,6.7673683 Z"></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 14" id="iconFail">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.216,12.529 L8.882,0.654 C8.506,-0.052 7.494,-0.052 7.117,0.654 L0.784,12.529 C0.429,13.195 0.912,14 1.667,14 L14.334,14 C15.088,14 15.571,13.195 15.216,12.529 Z M8,12 C7.448,12 7,11.552 7,11 C7,10.448 7.448,10 8,10 C8.552,10 9,10.448 9,11 C9,11.552 8.552,12 8,12 Z M9,9 L7,9 L7,5 L9,5 L9,9 Z"></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconReload">  
            <g stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconCheck">
            <g stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconX">
            <g stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconMinus">
            <g stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconPlus">
            <g stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 28" id="iconPwa">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M 11.191406 0.109375 C 10.597656 0.324219 0.308594 5.671875 0.164062 5.84375 C -0.046875 6.085938 -0.046875 6.570312 0.164062 6.800781 C 0.320312 6.992188 10.644531 12.347656 11.21875 12.546875 C 11.621094 12.679688 12.9375 12.679688 13.335938 12.546875 C 13.921875 12.347656 24.238281 6.992188 24.402344 6.800781 C 24.601562 6.570312 24.601562 6.085938 24.402344 5.851562 C 24.238281 5.664062 13.914062 0.304688 13.335938 0.109375 C 12.953125 -0.0273438 11.558594 -0.0195312 11.191406 0.109375 Z M 11.785156 2.601562 C 12.1875 2.808594 12.507812 3.007812 12.488281 3.050781 C 12.460938 3.132812 11.382812 4.046875 10.132812 5.042969 C 9.757812 5.347656 9.523438 5.5625 9.605469 5.539062 C 9.695312 5.511719 10.371094 5.292969 11.109375 5.0625 C 11.859375 4.835938 12.882812 4.503906 13.390625 4.34375 C 13.902344 4.171875 14.398438 4.039062 14.480469 4.039062 C 14.660156 4.039062 15.792969 4.648438 15.792969 4.738281 C 15.792969 4.773438 15.675781 4.882812 15.539062 4.980469 C 15.328125 5.125 14.707031 5.644531 13.109375 7.019531 C 12.863281 7.222656 13.117188 7.160156 15.273438 6.453125 L 17.71875 5.652344 L 18.578125 6.085938 C 19.050781 6.316406 19.445312 6.535156 19.445312 6.570312 C 19.445312 6.597656 19.023438 6.757812 18.515625 6.910156 C 17.4375 7.234375 14.425781 8.167969 13.421875 8.488281 C 13.046875 8.617188 12.25 8.859375 11.667969 9.027344 L 10.589844 9.34375 L 9.859375 8.957031 C 9.457031 8.742188 9.128906 8.542969 9.128906 8.515625 C 9.128906 8.480469 9.722656 7.941406 10.453125 7.3125 C 11.183594 6.6875 11.765625 6.15625 11.757812 6.136719 C 11.710938 6.09375 10.199219 6.542969 7.640625 7.359375 L 7.113281 7.53125 L 6.425781 7.199219 C 6.050781 7.019531 5.75 6.847656 5.75 6.8125 C 5.75 6.757812 8.378906 4.414062 9.121094 3.8125 C 9.300781 3.671875 9.777344 3.257812 10.171875 2.898438 C 10.570312 2.539062 10.925781 2.242188 10.972656 2.242188 C 11.019531 2.242188 11.382812 2.40625 11.785156 2.601562 Z M 11.785156 2.601562 "></path>
                <path d="M 19.308594 10.6875 C 13.519531 13.578125 13.375 13.667969 12.917969 14.574219 L 12.691406 15.023438 L 12.691406 27.613281 L 12.898438 27.800781 C 13.054688 27.945312 13.226562 28 13.558594 28 C 13.949219 28 14.515625 27.738281 19.34375 25.335938 C 25.105469 22.453125 25.332031 22.320312 25.789062 21.421875 L 26.019531 20.964844 L 26.019531 8.375 L 25.808594 8.183594 C 25.644531 8.03125 25.480469 7.988281 25.140625 7.988281 C 24.738281 7.988281 24.203125 8.238281 19.308594 10.6875 Z M 22.074219 16.539062 C 22.886719 18.03125 23.625 19.359375 23.707031 19.492188 C 23.800781 19.625 23.84375 19.753906 23.828125 19.769531 C 23.734375 19.851562 22.09375 20.640625 22.011719 20.640625 C 21.972656 20.640625 21.78125 20.355469 21.589844 20.011719 L 21.261719 19.375 L 19.710938 20.164062 L 18.167969 20.957031 L 17.828125 21.941406 L 17.5 22.9375 L 16.605469 23.40625 C 16.023438 23.710938 15.703125 23.835938 15.703125 23.753906 C 15.703125 23.691406 16.339844 21.824219 17.125 19.601562 C 17.910156 17.382812 18.632812 15.335938 18.734375 15.058594 C 18.90625 14.566406 18.933594 14.539062 19.609375 14.1875 C 19.992188 13.992188 20.375 13.828125 20.449219 13.828125 C 20.53125 13.820312 21.132812 14.824219 22.074219 16.539062 Z M 22.074219 16.539062 "></path>
                <path d="M 19.683594 16.511719 C 19.65625 16.585938 19.445312 17.1875 19.226562 17.832031 L 18.816406 19.027344 L 19.699219 18.558594 C 20.183594 18.308594 20.59375 18.09375 20.605469 18.09375 C 20.613281 18.082031 20.414062 17.699219 20.175781 17.230469 C 19.902344 16.71875 19.710938 16.433594 19.683594 16.511719 Z M 19.683594 16.511719 "></path>
                <path d="M 1.734375 16.253906 L 1.734375 21.132812 L 2.691406 21.609375 C 3.222656 21.863281 3.671875 22.078125 3.699219 22.078125 C 3.726562 22.078125 3.742188 21.359375 3.742188 20.480469 L 3.742188 18.882812 L 5.011719 19.511719 C 6.199219 20.085938 6.335938 20.136719 7.003906 20.175781 C 7.558594 20.203125 7.796875 20.183594 8.015625 20.066406 C 8.699219 19.714844 8.945312 19.214844 8.945312 18.171875 C 8.945312 17.078125 8.582031 16.144531 7.742188 15.09375 C 6.964844 14.144531 6.683594 13.957031 3.515625 12.339844 C 2.664062 11.910156 1.90625 11.515625 1.851562 11.460938 C 1.761719 11.398438 1.734375 12.367188 1.734375 16.253906 Z M 6.050781 15.6875 C 6.644531 16.242188 6.929688 17.300781 6.609375 17.75 C 6.371094 18.09375 5.886719 18.019531 4.746094 17.457031 L 3.742188 16.960938 L 3.742188 14.375 L 4.773438 14.914062 C 5.332031 15.203125 5.90625 15.550781 6.050781 15.6875 Z M 6.050781 15.6875 "></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26" id="iconAmp">
            <g stroke-linecap="round" stroke-linejoin="round">
				<path d="M 17.457031 11.808594 L 12.003906 20.886719 L 11.015625 20.886719 L 11.992188 14.972656 L 8.925781 14.976562 C 8.652344 14.976562 8.433594 14.753906 8.433594 14.484375 C 8.433594 14.367188 8.539062 14.167969 8.539062 14.167969 L 13.976562 5.101562 L 14.980469 5.109375 L 13.980469 11.03125 L 17.070312 11.027344 C 17.34375 11.027344 17.5625 11.246094 17.5625 11.519531 C 17.5625 11.628906 17.519531 11.726562 17.457031 11.808594 Z M 13 0 C 5.820312 0 0 5.820312 0 13 C 0 20.179688 5.820312 26 13 26 C 20.179688 26 26 20.179688 26 13 C 26 5.820312 20.179688 0 13 0 Z M 13 0 "></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26" id="iconFbia">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M 19.964844 1.855469 C 21.117188 1.855469 22.097656 2.265625 22.917969 3.082031 C 23.734375 3.902344 24.144531 4.882812 24.144531 6.035156 L 24.144531 19.964844 C 24.144531 21.117188 23.734375 22.097656 22.917969 22.917969 C 22.097656 23.734375 21.117188 24.144531 19.964844 24.144531 L 17.238281 24.144531 L 17.238281 15.511719 L 20.125 15.511719 L 20.558594 12.144531 L 17.238281 12.144531 L 17.238281 9.996094 C 17.238281 9.453125 17.351562 9.046875 17.578125 8.777344 C 17.804688 8.507812 18.246094 8.371094 18.90625 8.371094 L 20.675781 8.355469 L 20.675781 5.355469 C 20.066406 5.265625 19.203125 5.222656 18.09375 5.222656 C 16.777344 5.222656 15.726562 5.609375 14.9375 6.382812 C 14.148438 7.15625 13.753906 8.25 13.753906 9.664062 L 13.753906 12.144531 L 10.851562 12.144531 L 10.851562 15.511719 L 13.753906 15.511719 L 13.753906 24.144531 L 6.035156 24.144531 C 4.882812 24.144531 3.902344 23.734375 3.082031 22.917969 C 2.265625 22.097656 1.855469 21.117188 1.855469 19.964844 L 1.855469 6.035156 C 1.855469 4.882812 2.265625 3.902344 3.082031 3.082031 C 3.902344 2.265625 4.882812 1.855469 6.035156 1.855469 Z M 19.964844 1.855469 "/>
            </g>
        </symbol>
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
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconCopy">
            <g stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </g>
	    </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconEdit">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </g>
	    </symbol>
        <symbol  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconRemove">
            <g stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconAttachment">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconFile">
            <g stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                <polyline points="13 2 13 9 20 9"></polyline>
            </g>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="iconSearch">
            <g stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </g>
        </symbol>
    </svg>
</footer>
<?php

if (!defined('ABSPATH')) exit;

$bgColor = daftplugInstantify::getSetting('pwaNavigationTabBarBgColor');
$iconColor = daftplugInstantify::getSetting('pwaNavigationTabBarIconColor');
$iconActiveColor = daftplugInstantify::getSetting('pwaNavigationTabBarIconActiveColor');
$iconActiveBgColor = daftplugInstantify::getSetting('pwaNavigationTabBarIconActiveBgColor');
$currentUrl = daftplugInstantify::getCurrentUrl(false);

?>

<nav class="daftplugPublicNavigationTabBar" style="background-color: <?php echo $bgColor; ?>;">
	<ul class="daftplugPublicNavigationTabBar_list">
	<?php
	for ($ntb = 1; $ntb <= 7; $ntb++) {
        $icon = daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sIcon', $ntb));
        $label = esc_html__(daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sLabel', $ntb)), $this->textDomain);
        $page = (daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sCustomUrl', $ntb)) == 'on') ? daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sUrl', $ntb)) : daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%sPage', $ntb));
		if (daftplugInstantify::getSetting(sprintf('pwaNavigationTabBarItem%s', $ntb)) == 'on') {
			if ($page == '*directSearch*') { ?>
                <li class="daftplugPublicNavigationTabBar_item -directSearch <?php if (is_search()) {echo '-active';} ?>">
                    <div class="daftplugPublicNavigationTabBar_search">
                        <form role="search" method="get" action="<?php echo esc_url(home_url('/', 'https')); ?>" class="daftplugPublicNavigationTabBar_searchForm">
                            <input type="search" name="s" class="daftplugPublicNavigationTabBar_searchField" placeholder="<?php esc_html_e('Search for something...'); ?>" value="<?php echo get_search_query(); ?>" required>
                        </form>
                    </div>
                    <a class="daftplugPublicNavigationTabBar_link" href="javascript:void(0)" <?php if (is_search()) { echo 'style="background:'.$iconActiveBgColor.';"'; } ?>>
                        <i class="daftplugPublicNavigationTabBar_icon <?php echo $icon; ?>" style="color: <?php echo (is_search() ? $iconActiveColor : $iconColor); ?>"></i>
                    </a>
                    <?php if ($label !== '') { ?>
                        <span class="daftplugPublicNavigationTabBar_label" style="color: <?php echo $iconColor; ?>"><?php echo $label; ?></span>
                    <?php } ?>
                </li>
            <?php } else { ?>
                <li class="daftplugPublicNavigationTabBar_item <?php if ($currentUrl == $page) {echo '-active';} ?>">
                    <a class="daftplugPublicNavigationTabBar_link" href="<?php echo $page; ?>" <?php if ($currentUrl == $page) { echo 'style="background:'.$iconActiveBgColor.';"'; } ?>>
                        <?php if (function_exists('wc_get_cart_url') && ($page == wc_get_cart_url())) { echo '<span class="daftplugPublicNavigationTabBar_cartcount"></span>'; } ?>
                        <i class="daftplugPublicNavigationTabBar_icon <?php echo $icon; ?>" style="color: <?php echo ($currentUrl == $page ? $iconActiveColor : $iconColor); ?>"></i>
                    </a>
                    <?php if ($label !== '') { ?>
                        <span class="daftplugPublicNavigationTabBar_label" style="color: <?php echo $iconColor; ?>"><?php echo $label; ?></span>
                    <?php } ?>
                </li>
			<?php }
		}
	}
	?>
	</ul>
</nav>
<?php

if (!defined('ABSPATH')) exit;

header('Content-Type:'.esc_attr(feed_content_type('rss-http')).'; charset='.esc_attr(get_option('blog_charset')), true);
echo '<?xml version="1.0" encoding="'.esc_attr(get_option('blog_charset')).'"?'.'>';

?>

<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<title><?php wp_title_rss().' - '.esc_html_e('Instant Articles', $this->textDomain); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php bloginfo_rss('url'); ?></link>
		<description><?php bloginfo_rss("description"); ?></description>
		<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
		<language><?php bloginfo_rss('language'); ?></language>
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency><?php echo daftplugInstantify::getSetting('fbiaUpdateFrequency'); ?></sy:updateFrequency>
		<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
		<item>
			<title><?php the_title_rss(); ?></title>
			<link><?php the_permalink_rss(); ?></link>
			<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
			<guid isPermaLink="false"><?php the_guid(); ?></guid>
			<dc:creator><?php the_author(); ?></dc:creator>
			<pubDate><?php echo get_post_time('c', true); ?></pubDate>
			<modDate><?php echo get_post_modified_time('c', true); ?></modDate>
			<content:encoded>
				<![CDATA[
					<!DOCTYPE html>
					<html lang="<?php echo get_bloginfo('language'); ?>" <?php if (daftplugInstantify::getSetting('fbiaRtlPublishing') == 'on') {echo 'dir="rtl"';} ?> prefix="op: http://media.facebook.com/op#">
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_bloginfo('charset'); ?>" />
							<meta property="op:markup_version" content="v1.0">
							<meta property="op:generator:application" content="<?php echo $this->slug; ?>">
							<meta property="op:generator:application:version" content="<?php echo $this->version; ?>">
							<meta property="fb:article_style" content="<?php echo daftplugInstantify::getSetting('fbiaArticleStyle'); ?>">
							<?php if (!empty(wp_get_canonical_url())) { echo '<link rel="canonical" href="'.esc_url(wp_get_canonical_url()).'" />'; } ?>
							<?php echo apply_filters("{$this->optionName}_articles_head", '', get_the_id()); ?>
							<title><?php the_title_rss(); ?></title>
						</head>
						<body>
							<article>
								<header>
									<h1><?php the_title(); ?></h1>
									<time class="op-published" dateTime="<?php echo get_post_time('c', true); ?>"><?php the_date(); ?></time>
									<time class="op-modified" dateTime="<?php echo get_post_modified_time('c', true); ?>"><?php the_modified_date(); ?></time>
									<address><?php the_author(); ?></address>
									<?php echo apply_filters("{$this->optionName}_articles_header", '', get_the_id()); ?>
								</header>
								<?php echo apply_filters("{$this->optionName}_articles_content", $this->handleContentTransformation(get_post(get_the_id())), get_the_id()); ?>
								<footer>
									<?php echo apply_filters("{$this->optionName}_articles_footer", '', get_the_id()); ?>
								</footer>
							</article>
						</body>
					</html>
				]]>
			</content:encoded>
		</item>
		<?php endwhile; endif; wp_reset_postdata(); ?>
	</channel>
</rss>
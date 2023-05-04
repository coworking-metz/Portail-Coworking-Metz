<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('daftplugInstantifyFbiaPublicFeed')) {
    class daftplugInstantifyFbiaPublicFeed {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $settings;

        public $daftplugInstantifyFbiaPublic;

    	public function __construct($config, $daftplugInstantifyFbiaPublic) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->settings = $config['settings'];

            $this->daftplugInstantifyFbiaPublic = $daftplugInstantifyFbiaPublic;

            add_action('pre_get_posts', array($this, 'getPostTypeArticles'));
            add_filter("{$this->optionName}_articles_header", array($this, 'injectFeaturedImage'), 10, 2, 1);
            add_filter("{$this->optionName}_articles_header", array($this, 'injectPostCategories'), 10, 2, 10);

            if (daftplugInstantify::getSetting('fbiaArticleInteraction') == 'on') {
                add_filter("{$this->optionName}_articles_head", array($this, 'injectInteractionMetaTag'), 10, 2);
            }

            if (!empty(daftplugInstantify::getSetting('fbiaCopyright'))) {
                add_filter("{$this->optionName}_articles_footer", array($this, 'injectCopyright'), 20, 2);
            }

            if (daftplugInstantify::getSetting('fbiaRelatedArticles') == 'on') {
                add_filter("{$this->optionName}_articles_footer", array($this, 'injectRelatedArticles'), 10, 2);
            }
    	}

        public function getPostTypeArticles($wpQuery) {
            if (($wpQuery->query_vars['feed'] == 'instant-articles') && $wpQuery->is_main_query()) {
                $metaQuery = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'fbia',
                        'value' => 'exclude',
                        'compare' => 'NOT IN',
                    ),
                    array(
                        'key' => 'fbia',
                        'compare' => 'NOT EXISTS'
                    )
                );

                $wpQuery->set('meta_query', $metaQuery);
                $wpQuery->set('orderby', 'modified');
                $wpQuery->set('posts_per_rss', daftplugInstantify::getSetting('fbiaArticleQuantity'));

                if (!isset($wpQuery->query_vars['post_type'])) {
                    $wpQuery->set('post_type', (array)daftplugInstantify::getSetting('fbiaOnPostTypes'));
                }
            }

            return $wpQuery;
        }

        public function injectFeaturedImage($header, $postId) {
            $featuredImageId = get_post_thumbnail_id($postId);
            $featuredImageAlt = get_post_meta($featuredImageId, '_wp_attachment_image_alt', true);
            $featuredImageCaption = wp_get_attachment_caption($featuredImageId);

            if (has_post_thumbnail($postId)) {
                $header .= '<figure>';
                $header .= '<img src="'.get_the_post_thumbnail_url($postId, 'full').'"/>';

                if (!empty($featuredImageCaption)) {
                    $header .= '<figcaption>'.$featuredImageCaption.'</figcaption>';
                } elseif (!empty($featuredImageAlt)) {
                    $header .= '<figcaption>'.$featuredImageAlt.'</figcaption>';
                }

                $header .= '</figure>';
            }

            return $header;
        }

        public function injectPostCategories($header, $postId) {
            if (has_category()) {
                $categoryNames = array();
                foreach (get_the_category() as $category) {
                    $categoryNames[] = $category->name;
                }
                $header .= '<h3 class="op-kicker">'.implode(', ', $categoryNames).'</h3>';
            }

            return $header;
        }

        public function injectInteractionMetaTag($head, $postId) {
            $head .= '<meta property="fb:likes_and_comments" content="enable">';

            return $head;
        }
        
        public function injectCopyright($footer, $postId) {
            $footer .= '<small>'.wp_kses_post(daftplugInstantify::getSetting('fbiaCopyright')).'</small>';

            return $footer;
        }

        public function injectRelatedArticles($footer, $postId) {
            $queryArgs = array(
                'post__not_in' => array($postId),
                'posts_per_page' => 4,
                'ignore_sticky_posts'  => true,
                'order'  => 'DESC',
                'orderby' => 'date',
                'no_found_rows' => true,
                'post_type' => get_post_type(),
                'post_status' => 'publish',
            );

            $relatedArticlesLoop = new WP_Query($queryArgs);
            $relatedArticlePosts = $relatedArticlesLoop->get_posts();
            if ($relatedArticlesLoop->have_posts()) {
                $footer .= '<ul class="op-related-articles">';
                foreach ($relatedArticlePosts as $relatedArticlePost) {
                    $footer .= '<li><a href="';
                    $footer .= esc_url(get_permalink($relatedArticlePost));
                    $footer .= '"></a></li>';
                }
                $footer .= '</ul>';
            }

            return $footer;
        }
    }
}
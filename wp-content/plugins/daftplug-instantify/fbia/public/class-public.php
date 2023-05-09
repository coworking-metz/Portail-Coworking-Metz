<?php

if (!defined('ABSPATH')) exit;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Validators\Type;

if (!class_exists('daftplugInstantifyFbiaPublic')) {
    class daftplugInstantifyFbiaPublic {
    	public $name;
        public $description;
        public $slug;
        public $version;
        public $textDomain;
        public $optionName;

        public $pluginFile;
        public $pluginBasename;

        public $dependencies;

        public $settings;

        public $partials;

    	public function __construct($config) {
    		$this->name = $config['name'];
            $this->description = $config['description'];
            $this->slug = $config['slug'];
            $this->version = $config['version'];
            $this->textDomain = $config['text_domain'];
            $this->optionName = $config['option_name'];

            $this->pluginFile = $config['plugin_file'];
            $this->pluginBasename = $config['plugin_basename'];

            $this->dependencies = array();

            $this->settings = $config['settings'];

            $this->partials = $this->generatePartials();

            add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
            add_action('init', array($this, 'addFeed'));
    	}

        public function loadAssets() {            
            $this->dependencies[] = 'jquery';
            $this->dependencies[] = "{$this->slug}-public";

            wp_enqueue_style("{$this->slug}-fbia-public", plugins_url('fbia/public/assets/css/style-fbia.min.css', $this->pluginFile), array(), $this->version);
            wp_enqueue_script("{$this->slug}-fbia-public", plugins_url('fbia/public/assets/js/script-fbia.min.js', $this->pluginFile), $this->dependencies, $this->version, true);
        }

    	public function generatePartials() {
            $partials = array(
                'instantArticles' => plugin_dir_path(__FILE__) . implode(DIRECTORY_SEPARATOR, array('partials', 'display-instantarticles.php')),
            );

            return $partials;
    	}

        public function addFeed() {
            $added = false;
            $rules = (array)get_option('rewrite_rules');
            $feeds = array_keys($rules, 'index.php?&feed=$matches[1]');
            add_feed('instant-articles', array($this, 'renderInstantArticles'));
            foreach ($feeds as $feed) {
                if (strpos($feed, 'instant-articles') !== false) {
                    $added = true;
                }
            }

            if (!$added) {
                flush_rewrite_rules(false);
            }
        }

        public function getPostContent($postId) {
            global $post, $more;
            $origMore = $more;
            $more = 1;
            $resetPostdata = false;
            if (empty($post) || $postId !== $post->ID) {
                $post = get_post($postId);
                setup_postdata($post);
                $resetPostdata = true;
            }
            $content = $postId->post_content;    
            if (!has_filter('the_content', 'wpautop')) {
                add_filter('the_content', 'wpautop');
            }
            $content = apply_filters('the_content', $content);
            $more = $origMore;
            if ($resetPostdata) {
                wp_reset_postdata();
            }
            preg_match_all('!<a[^>]*? href=[\'"]#[^<]+</a>!i', $content, $matches);
            foreach ($matches[0] as $link) {
                $content = str_replace($link, strip_tags($link), $content);
            }

            return $content;
        }

        public function handleContentTransformation($postId) {
            $content = $this->getPostContent($postId);
            $transformerRules = plugin_dir_path(dirname(__FILE__)) . 'includes/libs/rules-configuration.json';
            $configuration = file_get_contents($transformerRules);
            $instantArticle = InstantArticle::create();
            $transformer = new Transformer();
            $transformer->loadRules($configuration);
            if (!empty(daftplugInstantify::getSetting('fbiaCustomRules'))) {
                $transformer->loadRules(htmlspecialchars(wp_unslash(daftplugInstantify::getSetting('fbiaCustomRules'))));
            }
            if (!Type::isTextEmpty($content)) {
                $transformer->transformString($instantArticle, $content, get_option('blog_charset'));
            }
            $fullContent = $instantArticle->render();
            $libxmlPreviousState = libxml_use_internal_errors(true);
            $dom = new DOMDocument('1.0', get_option('blog_charset'));
            if (function_exists('mb_convert_encoding')) {
                $fullContent = mb_convert_encoding($fullContent, 'HTML-ENTITIES', get_option('blog_charset'));
            }
            @$dom->loadHTML($fullContent);
            libxml_clear_errors();
            libxml_use_internal_errors($libxmlPreviousState);
            $dom->normalizeDocument();
            $body = $dom->documentElement->lastChild;
            preg_match("/<article[^>]*>(.*?)<\/article>/is", $dom->saveHTML($body), $matches);
            $content = $matches[1];

            return $content;
        }

        public function renderInstantArticles() {
            include_once($this->partials['instantArticles']);
        }
    }
}
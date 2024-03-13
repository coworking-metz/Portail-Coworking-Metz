<?php

/* 
 * vérification de la configuration wordpress (en cas de migration) 
 * appeler /wp-admin/?config=force&auto pour mettre à jour l'URL du site en base de données
 * appeler /wp-admin/?config=force pour vérifier les valeurs au préalable
 */
function check_current_config()
{
  wp_cache_init();

  if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
  }
  $config = isset($_GET['config']) ? $_GET['config'] : false;
  $auto = isset($_GET['auto']);
  $new = rtrim(current_site_url() . current(explode('wp-admin', $_SERVER['REQUEST_URI'])), '/');

  if ($config) {
    if ($auto) {
      $_GET['old'] = get_option('siteurl');
      $_GET['siteurl'] = $new;
    }
    if ($config == 'force' && !isset($_GET['old']) && !isset($_GET['siteurl'])) {
?>
      <form method="get">
        <input type="hidden" name=config value=force>
        <p>Ancienne Url<br><input type="text" name="old" size="100" placeholder="Ancienne url" value="<?php echo get_option('siteurl'); ?>"></p>

        <p>Nouvelle Url<br><input type="text" name="siteurl" size="100" placeholder="Nouvelle url" value="<?php echo $new; ?>"></p>
        <input type="submit">
      </form>
      <?php
      exit;
    }

    $siteurl = addslashes($_GET['siteurl']);
    $old = addslashes($_GET['old']);
    if ($config == 'force' && $old && $siteurl) {
      // Update the site URL in options
      update_option('home', $siteurl);
      update_option('siteurl', $siteurl);

      // Fetch all options and update serialized data
      $options = $GLOBALS['wpdb']->get_results('SELECT option_name, option_value FROM ' . $GLOBALS['wpdb']->prefix . 'options');
      foreach ($options as $option) {
        if (is_serialized($option->option_value)) {
          $data = unserialize($option->option_value);
          $data = recursive_replace($data, $old, $siteurl);
          $GLOBALS['wpdb']->update(
            $GLOBALS['wpdb']->prefix . 'options',
            ['option_value' => serialize($data)],
            ['option_name' => $option->option_name]
          );
        }
      }

      // Update URLs in posts
      $GLOBALS['wpdb']->query('UPDATE ' . $GLOBALS['wpdb']->prefix . 'posts SET post_content = REPLACE(post_content,"' . $old . '","' . $siteurl . '")');
      $GLOBALS['wpdb']->query('UPDATE ' . $GLOBALS['wpdb']->prefix . 'posts SET guid = REPLACE(guid,"' . $old . '","' . $siteurl . '")');

      if ($auto) {
        echo 'update terminated: ' . $old . ' -> ' . $siteurl;
      } else {
      ?>
        <p>update terminated: <?php echo $old; ?> -> <?php echo $siteurl; ?></p><a href="<?php echo $siteurl; ?>/wp-admin/">Continue</a>
<?php
      }
      exit;
    }
  }

}

  // Recursive function to replace URLs
  function recursive_replace($value, $old, $siteurl)
  {
    if (is_array($value)) {
      foreach ($value as $key => $subvalue) {
        $value[$key] = recursive_replace($subvalue, $old, $siteurl);
      }
    } elseif (is_string($value)) {
      return str_replace($old, $siteurl, $value);
    }
    return $value;
  }

add_action('init', 'check_current_config');

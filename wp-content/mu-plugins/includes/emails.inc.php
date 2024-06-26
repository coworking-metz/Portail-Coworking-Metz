<?php

/**
 * Ajoute des shortcodes personnalisés à un modèle de mail
 *
 * @param array $shortcodes Liste des shortcodes à ajouter
 */
function add_custom_shortcodes_to_template($shortcodes)
{
    foreach (['viwec_register_preview_shortcode', 'viwec_register_replace_shortcode'] as $filter) {
        add_filter($filter, function ($codes, $object, $args) use ($shortcodes) {
            return $shortcodes;
        }, 10, 3);
    }
}

/**
 * Charger un modèle de mail et retourne son contenu
 *
 * @param int $template_id ID du modèle de mail
 * @param array $codes Codes de remplacement pour le contenu du mail
 * @return array Retourne un tableau contenant le message et le sujet
 */
function charger_template_mail($template_id, $codes = [])
{

    if(strstr($template_id, 'brevo')) {
        $template_id = str_replace('brevo-','',$template_id);
        $template = brevo_getTemplate($template_id);

        $ret = ['message' => $template['htmlContent'], 'subject' => $template['subject']];

        foreach($ret as &$item) {
            $item = str_replace('http://https://','https://', $item);
            foreach($codes as $code) {
                foreach($code as $index=>$value) {
                    $item = str_replace($index, (string) $value, $item);
                }
            }
        }
        $ret = str_replace('http://https://','https://', $ret);
        $ret = str_replace('https://https://','https://', $ret);
        $ret = str_replace('https://http://','http://', $ret);

        return $ret;

    } else {
    add_custom_shortcodes_to_template($codes);

        $template = new VIWEC_Render_Email_Template(['template_id' => $template_id]);
        ob_start();
        $template->get_content();
        $message = ob_get_contents();
        ob_end_clean();
        return ['message' => $message, 'subject' => $template->get_subject()];
    }
}

function envoyer_email_creation_compte($user)
{

    $success = retrieve_password($user->user_email);
    return $success ? true : false;
}

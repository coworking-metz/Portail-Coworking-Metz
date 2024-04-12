<?php

use function Crontrol\Event\pause;

function brevo_getTemplates()
{
    if (!class_exists('SendinblueApiClient')) {
        return;
    }

    $templates = get_transient('brevo-templates');
    if (!$templates) {
        $apiClient = new SendinblueApiClient();

        $templates = $apiClient->getAllEmailTemplates()['templates'] ?? [];
        set_transient('brevo-templates', $templates, HOUR_IN_SECONDS);
    }

    return $templates;
}

function brevo_getTemplate($id)
{
    $templates = brevo_getTemplates();

    foreach ($templates as $template) {
        if ($template['id'] == $id) {
            return $template;
        }
    }
}
function brevo_start_unsubscribed()
{
    if (!class_exists('SendinblueApiClient')) return;
    $apiClient = new SendinblueApiClient();
    $lists = $apiClient->getAllLists();

    $processes = [];
    foreach ($lists['lists'] as $list) {
        $data = [
            "customContactFilter" => [
                "actionForContacts" => "unsubscribed",
                "listId" => $list['id'],
            ],
            "notifyUrl" => site_url('?brevo-notify&brevo-action=unsubscribed'),
        ];

        $ret = $apiClient->post('/contacts/export', $data);
        $processes[] = $ret['processId'];
    }
    return $processes;
    // foreach ($processes as $pid) {
    //     sleep(5);

    //     if ($p = brevo_get_process($pid)) {
    //         m($pid,$p);
    //         if ($p['export_url']) {
    //             mailchimp_unsubscribe_from_csv($p['export_url']);
    //         }
    //     }
    // }
}

function brevo_get_process($pid)
{
    if (!class_exists('SendinblueApiClient')) return;
    $apiClient = new SendinblueApiClient();
    return $apiClient->get('/processes/' . $pid);
}

function brevo_sync_to_wordpress_list()
{
    if (!class_exists('SendinblueApiClient')) return;
    $users = get_users([
        'role__in' => ['administrator', 'customer'],
    ]);

    $apiClient = new SendinblueApiClient();

    $lists = $apiClient->getAllLists();

    $chunks = slice_array_to_chunks($users);
    foreach ($chunks as $chunk) {
        $json = [];
        foreach ($chunk as $user) {
            $json[] = ['EMAIL' => $user->user_email, 'attributes' => ['FIRSTNAME' => $user->first_name, 'LASTNAME' => $user->last_name]];
        }
        $data = array(
            "disableNotification" => true,
            "updateExistingContacts" => true,
            "emptyContactsAttributes" => false,
            "jsonBody" => $json,
            "listIds" => [10],
        );
        $ret = $apiClient->importContacts($data);
    }

    return $ret;
}

function brevo_unsubscribe($emails)
{
    if (!class_exists('SendinblueApiClient')) return;

    $apiClient = new SendinblueApiClient();

    $lists = $apiClient->getAllLists();

    $json = [];
    foreach ($emails as $email) {
        $json[] = ['email' => $email];
    }
    $data = array(
        "emailBlacklist" => true,
        "disableNotification" => false,
        "smsBlacklist" => false,
        "updateExistingContacts" => true,
        "emptyContactsAttributes" => false,
        "jsonBody" => $json,
        "listIds" => array_column($lists['lists'], 'id'),
    );
    return $apiClient->importContacts($data);
}
/**
 * Importe des contacts dans Brevo via leur API.
 *
 * @param string $apiKey Clé API de Brevo.
 * @param array $data Données à envoyer.
 * @return array Réponse de l'API.
 */

function importContactsToBrevo($data)
{
    $url = "https://api.brevo.com/v3/contacts/import";

    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
        'api-key: ' . BREVO_KEY,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

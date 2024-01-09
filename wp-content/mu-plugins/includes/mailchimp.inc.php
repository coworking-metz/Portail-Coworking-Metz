<?php

function mailchimp_unsubscribe_from_csv($url)
{

    $url = $_POST['url'] ?? $_GET['url'] ?? false;
    $emails = [];

    if (($handle = fopen($url, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                $emails[] = $data[0]; // Add the email to the array
            }
        }
        fclose($handle);
    }


    return mailchimp_unsubscribe($emails);
}


function mailchimp_unsubscribed()
{
    $lists = mailchimp_fetchAllListsFromMailchimp();
    $unsubscribed = [];
    foreach ($lists as $list) {
        $page = 1;
        do {
            $tmp = mailchimp_fetchUnsubscribedAddresses($list['id'], $page);
            $page++;
            $unsubscribed = array_merge($unsubscribed, $tmp);
        } while (count($tmp));
    }
    return $unsubscribed;
}

function mailchimp_unsubscribe($emails)
{


    $members = [];
    foreach ($emails as $email) {
        $members[] = [
            'email_address' => $email,
            'status' => 'unsubscribed',
            'status_if_new' => 'unsubscribed'
        ];
    }
    $apiKey = MAILCHIMP_KEY;

    $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);

    $lists = mailchimp_fetchAllListsFromMailchimp();
    $unsubscribed = [];
    foreach ($lists as $list) {

        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $list['id'] . '/?skip_merge_validation=false';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'anystring:' . $apiKey);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: apikey ' . $apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            "members" => $members
        )));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);
        curl_close($ch);
    }

    return $emails;
}


function mailchimp_fetchUnsubscribedAddresses($listId, $page)
{
    $count = 100;
    $offset = ($page - 1) * $count;
    $apiKey = MAILCHIMP_KEY;
    $dc = substr($apiKey, strpos($apiKey, '-') + 1); // Extract the datacenter from the API key.
    $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$listId}/members?status=unsubscribed&fields=members.email_address&count=" . $count . "&offset=" . $offset;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: apikey ' . $apiKey));
    $result = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($result, true);
    $unsubscribedAddresses = array_column($data['members'], 'email_address');
    return $unsubscribedAddresses;
}

function pingMailchimp()
{
    $apiKey = MAILCHIMP_KEY;
    $dc = substr($apiKey, strpos($apiKey, '-') + 1); // Extract the datacenter from the API key.

    $url = "https://{$dc}.api.mailchimp.com/3.0/ping";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "anystring:{$apiKey}");
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function mailchimp_fetchAllListsFromMailchimp()
{
    $apiKey = MAILCHIMP_KEY;

    $dc = substr($apiKey, strpos($apiKey, '-') + 1); // Extract the datacenter from the API key.
    $url = "https://{$dc}.api.mailchimp.com/3.0/lists";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: apikey ' . $apiKey));
    $result = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($result, true);
    return $data['lists'];
}

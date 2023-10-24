<?php
include __DIR__ . '/supabase.class.php';

function supabase()
{
    if (empty($GLOBALS['supabase'])) {
        $GLOBALS['supabase'] = new SupabaseClient(
            SUPABASE_API_URL,
            SUPABASE_API_KEY
        );
    }
    return $GLOBALS['supabase'];
}

function getParticipations($data)
{
    $hash = hashEvenement($data);
    $criteria = ['hash' => $hash];
    $liste =  supabase()->read('participations', $criteria);
    $out = ['ok' => 0, 'ko' => 0, 'maybe' => 0];
    foreach ($liste as $item) {
        $out[$item['participe']]++;
    }
    return $out;
}

function getEvenement($id)
{
    if(!$id) return;
    $criteria = ['id' => $id];
    return supabase()->read('evenements', $criteria)[0] ?? false;
}
function getEvenements()
{
    return supabase()->read('evenements');
}
function  upsertEvenement($data)
{
    $criteria = ['id' => $data['id'] ?? false];
    $data['ok'] = $data['ok']??0;
    $data['ko'] = $data['ko']??0;
    $data['maybe'] = $data['maybe']??0;
    unset($data['id']);
    $response = supabase()->upsert('evenements', $data, $criteria);
    if (!empty($response['id'])) {
        return $response;
    }

    $ret = getEvenement($criteria['id']);
    if ($ret) {
        return $ret;
    } else {
        me($ret, $response);
    }
}
function getParticipation($email, $id_evenement)
{
    $criteria = ['id_evenement' => $id_evenement, 'email' => $email];
    return supabase()->read('participations', $criteria)[0] ?? false;
}
function  upsertParticipation($id_evenement, $data)
{
    $criteria = ['email' => $data['email'], 'id_evenement' => $id_evenement];
    $response = supabase()->upsert('participations', $data, $criteria);
    if(!empty($response['id'])) {
        return $response;
    }
    return getParticipation($criteria['email'], $id_evenement);
}
function hashEvenement($data)

{
    return md5($data['evenement'] . $data['date'] . $data['heure']);
}

function texteParticipation($participation)
{
    if (empty($participation['participe'])) return 'Vous n\'avez pas encore répondu';
    if ($participation['participe'] == 'ok') return 'Vous avez confirmé votre participation';
    if ($participation['participe'] == 'ko') return 'Vous ne participatez pas';
    if ($participation['participe'] == 'maybe') return 'Vous allez peut-être participer';
}
/*
function getAbonnementByStripeId($stripe_id)
{
    if (!$stripe_id) return;
    return supabase()->read('abonnements', ['stripe_id' => $stripe_id])[0] ?? null;
}
function getClientByStripeId($stripe_id)
{
    if (!$stripe_id) return;
    return supabase()->read('clients', ['stripe_id' => $stripe_id])[0] ?? null;
}

function getClientById($id)
{
    if (!$id) return;
    return supabase()->read('clients', ['id' => $id])[0] ?? null;
}

function updateAbonnement($data, $id)
{

    $criteria = ['id' => $id];
    $response = supabase()->update('abonnements', $data, $criteria);
    print_r($response);
    exit;
    $abonnement = supabase()->read('abonnements', $criteria)[0]??false;
    return $abonnement;
}

function insertAbonnement($subscription)
{
    $client = getClientByStripeId($subscription['customer']);
    if (!$client) return;

    $data = [
        'commande' => $subscription,
        'client_id' => $client['id'],
        'stripe_id' => $subscription->id,
    ];
    $criteria = ['stripe_id' => $data['stripe_id']];

    supabase()->upsert('abonnements', $data, $criteria);
    $abonnement = supabase()->read('abonnements', $criteria)[0]??false;
    return $abonnement;
}

function  upsertSite($site)
{
    $site['slug'] = slugify($site['nom']);
    if (empty($site['slug'])) return;

    $criteria = ['slug' => $site['slug']];
    $response = supabase()->upsert('sites', $site, $criteria);
    $site = supabase()->read('sites', $criteria)[0]??false;
    return $site;
}
function upsertClient($customer)
{
    if (!$customer->email ?? false) return;
    // Construct the data payload for Supabase
    $data = [
        'nom' => $customer->name,
        'email' => $customer->email,
        'stripe_id' => $customer->id,
        'adresse' => $customer->address,
    ];
    $criteria = ['stripe_id' => $data['stripe_id']];
    $response = supabase()->upsert('clients', $data, $criteria);
    $client = supabase()->read('clients', $criteria)[0]??false;
    return $client;
}
*/
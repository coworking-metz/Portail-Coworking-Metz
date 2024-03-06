<?php


function ag_candidats()
{
    $users = get_users_candidat_au_ca();
    shuffle($users);
    ?>
    <meta name="robots" content="noindex, nofollow">
    <div class="candidats">
        <?php
        foreach ($users as $user) { ?>
            <div>
                <strong>
                    <?= $user->display_name; ?>
                </strong>
                <figure>
                    <img src="/polaroid/<?= $user->ID; ?>.jpg">
                </figure>
            </div>
        <?php } ?>
    </div>
    <br><br>
    <style>
        .candidats {
            display: flex;
            gap: 2rem;
            flex-direction: column;
        }

        @media screen and (min-width: 800px) {
            .candidats {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }

        .candidats div {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .candidats div img {
            max-width: 200px;
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
    <?php
}


/**
 * Récupère tous les utilisateurs dont le meta 'candidat_au_ca' n'est pas false.
 *
 * @return WP_User[] Liste des utilisateurs.
 */
function get_users_candidat_au_ca()
{
    $users = get_users([
        'meta_key' => 'candidat_au_ca',
        'meta_value' => '',
        'meta_compare' => '!=',
        'fields' => 'all',
    ]);
    
    return array_filter($users, function($user) {
        return $user->candidat_au_ca;
    });
}

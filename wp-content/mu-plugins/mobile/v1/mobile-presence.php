<?php

add_action('rest_api_init', function () {
    // TODO: should the max number of coworkers present during the day instead of the total?
    /**
     * Should return the number of coworkers present
     * for the current and past week
     */
    register_rest_route('mobile/v1', 'presence/week', array(
        'methods'  => 'GET',
        'callback' => function ($request) {
            // get the coworker count for the past days from monday last week
            $pastDaysCountQueryParams = http_build_query([
                'from' => date('Y-m-d', strtotime('monday last week')),
                'to' => date('Y-m-d', strtotime('sunday this week')),
            ]);
            $pastDaysCountUrl = "https://tickets.coworking-metz.fr/stats/day?$pastDaysCountQueryParams";
            $pastDaysCountData = json_decode(wp_remote_get($pastDaysCountUrl)['body']); // TODO: make an HTTP client to handle errors properly

            $dayFromPreviousWeekTimeline = array_values(array_filter($pastDaysCountData, function ($dayStats) {
                return strtotime($dayStats->date) < strtotime('monday this week');
            }));
            $dayFromPreviousWeekTimeline = array_map(function ($dayStats) {
                return array(
                    'date' => $dayStats->date,
                    'value' => $dayStats->data->coworkersCount + $dayStats->data->newCoworkersCount,
                );
            }, $dayFromPreviousWeekTimeline);
            $dayFromPreviousWeek = array(
                'from' => date('Y-m-d', strtotime('monday last week')),
                'to' => date('Y-m-d', strtotime('sunday last week')),
                'timeline' => $dayFromPreviousWeekTimeline,
            );

            // also get the coworker count for today
            // TODO: should retrieve the max coworkers count for today instead of the current one
            $todayCountUrl = "https://tickets.coworking-metz.fr/api/coworkers-now";
            $todayCountData = json_decode(wp_remote_get($todayCountUrl)['body']);

            $currentWeekTimeline = array_values(array_filter($pastDaysCountData, function ($dayStats) {
                return strtotime($dayStats->date) >= strtotime('monday this week');
            }));
            $currentWeekTimeline = array_map(function ($dayStats) {
                return array(
                    'date' => $dayStats->date,
                    'value' => $dayStats->data->coworkersCount + $dayStats->data->newCoworkersCount,
                );
            }, $currentWeekTimeline);
            $currentWeekTimeline = array_merge($currentWeekTimeline, array(array(
                'date' => date('Y-m-d'),
                'value' => $todayCountData,
            )));
            $currentWeek = array(
                'from' => date('Y-m-d', strtotime('monday this week')),
                'to' => date('Y-m-d', strtotime('sunday this week')),
                'timeline' => $currentWeekTimeline,
            );

            $weekPresence = array(
                'previous' => $dayFromPreviousWeek,
                'current' => $currentWeek,
            );

            return rest_ensure_response($weekPresence);
        },
    ));

    // TODO: wait for a new endpoint /stats/hour to be created on the tickets API
    /**
     * Should return the number of coworkers present
     * for today and the same day of the week last week
     */
    register_rest_route('mobile/v1', 'presence/day', array(
        'methods'  => 'GET',
        'callback' => function ($request) {
            // get the coworker count for the same weekday as today
            // but in the previous week
            $dayFromPreviousWeekQueryParams = http_build_query([
                'from' => date('Y-m-d', strtotime('-7 day')),
                'to' => date('Y-m-d', strtotime('-6 day')),
            ]);
            $dayFromPreviousWeekUrl = "https://tickets.coworking-metz.fr/stats/hour?$dayFromPreviousWeekQueryParams";
            $dayFromPreviousWeekData = json_decode(wp_remote_get($dayFromPreviousWeekUrl)['body']);

            $dayFromPreviousWeekTimeline = array_map(function ($dayStats) {
                return array(
                    'date' => $dayStats->date,
                    'value' => $dayStats->data->coworkersCount + $dayStats->data->newCoworkersCount,
                );
            }, $dayFromPreviousWeekData);
            $dayFromPreviousWeek = array(
                'from' => date('Y-m-d', strtotime('-7 day')),
                'to' => date('Y-m-d', strtotime('-6 day')),
                'timeline' => $dayFromPreviousWeekTimeline,
            );

            // get the coworker count for the past days from monday last week
            $todayQueryParams = http_build_query([
                'from' => date('Y-m-d', strtotime('today')),
                'to' => date('Y-m-d'),
            ]);
            $todayUrl = "https://tickets.coworking-metz.fr/stats/hour?$todayQueryParams";
            $todayData = json_decode(wp_remote_get($todayUrl)['body']);

            $todayTimeline = array_map(function ($dayStats) {
                return array(
                    'date' => $dayStats->date,
                    'value' => $dayStats->data->coworkersCount + $dayStats->data->newCoworkersCount,
                );
            }, $todayData);
            $today = array(
                'from' => date('c', strtotime('today')),
                'to' => date('c'),
                'timeline' => $todayTimeline,
            );

            $dayPresence = array(
                'previous' => $dayFromPreviousWeek,
                'current' => $today,
            );

            return rest_ensure_response($dayPresence);
        },
    ));

    /**
     * Should return the number of coworkers currently present
     */
    register_rest_route('mobile/v1', 'presence/now', array(
        'methods'  => 'GET',
        'callback' => function ($request) {
            $currentCoworkersCountUrl = "https://tickets.coworking-metz.fr/api/coworkers-now";
            $currentCoworkersCountRequest = wp_remote_get($currentCoworkersCountUrl);

            $responseDate = $currentCoworkersCountRequest['http_response']->get_headers()['date'];
            $responseDateWithTimezone = (new DateTime($responseDate))->setTimezone(new DateTimeZone('Europe/Paris'));

            $currentCoworkersCountData = json_decode($currentCoworkersCountRequest['body']);

            $currentCoworkersCount = array(
                'fetchedAt' => $responseDateWithTimezone->format('c'),
                'count' => $currentCoworkersCountData,
                'total' => 28,
            );

            return rest_ensure_response($currentCoworkersCount);
        },
    ));
});

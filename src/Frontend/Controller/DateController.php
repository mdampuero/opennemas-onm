<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use DateInterval;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DateController extends Controller
{
    /**
     * Renders the date basing on the request parameters.
     *
     * @param Request The request object.
     *
     * @return Response The response with the date requested.
     */
    public function renderAction(Request $request)
    {
        $params = $request->query->all();

        $date     = $params['date'] ?? null;
        $defaults = [
            'format'   => null,
            'locale'   => $this->get('core.locale')->getLocale('frontend'),
            'timezone' => $this->get('core.locale')->getTimeZone(),
            'type'     => 'long|short'
        ];

        $params = array_merge($defaults, $params);

        try {
            if (!$date instanceof \DateTime) {
                $date = new \DateTime($date);
            }

            $endDate = new \DateTime($date->format('Y-m-d 23:59:59'));

            $expire = $endDate->getTimestamp() - $date->getTimestamp();

            $date = $this->get('data.manager.filter')
                ->set($date)
                ->filter('date', $params)
                ->get();

            $headers = [
                'x-cacheable' => true,
                'x-cache-for' => $expire . 's',
                'x-tags'      => 'header-date'
            ];

            return new Response($date, 200, $headers);
        } catch (\Throwable $e) {
            $this->get('logger')->error(sprintf('Error rendering date: %s', $e->getMessage()));

            return new Response('', 500);
        }
    }
}

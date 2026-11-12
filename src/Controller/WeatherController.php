<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Service\WeatherUtil;

final class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country}', name: 'app_weather', defaults: ['country' => null])]
    #[Route('/weather/{city}', name: 'app_weather_city')]

    public function city(string $city, LocationRepository $locationRepository, WeatherUtil $util, ?string $country = null
    ): Response {
        if ($country !== null) {
            $location = $locationRepository->findOneBy([
                'country' => $country,
                'city' => $city
            ]);

            $measurements = $util->getWeatherForCountryAndCity(
                $country,
                $city
            );
        } else {
            $location = $locationRepository->findOneBy([
                'city' => $city
            ]);

            if (!$location) {
                throw $this->createNotFoundException('Nie znaleziono lokacji!');
            }

            $measurements = $util->getWeatherForLocation($location);
        }

        if (!$location) {
            throw $this->createNotFoundException('Nie znaleziono lokacji!');
        }

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}

<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\LocationRepository;

use App\Repository\MeasurementRepository;

class WeatherUtil
{
    public function __construct(
        private MeasurementRepository $measurementRepository,
        private LocationRepository $locationRepository
    ) {}
    /**
     * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepository->findBy([
            'location' => $location
        ]);
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $cityName): array {
        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $cityName
        ]);

        if (!$location) {
            return [];
        }

        return $this->getWeatherForLocation($location);
    }
}

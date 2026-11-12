<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city-country',
    description: 'Add a short description for your command',
)]
class WeatherCityCountryCommand{

    public function __construct(private readonly WeatherUtil $weatherUtil){
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('City Name')] string $name,
        #[Argument('County Code')] string $countryCode,
    ): int {

        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $name);
        $io->writeln(sprintf('Location: %s', $name));
        $io->table(['Date', 'Temperature', 'Humidity', 'WindSpeed', 'Rain Probability'], array_map(fn($m) => [
            $m->getDate()->format('Y-m-d'),
            $m->getTemperature(),
            $m->getHumidity(),
            $m->getWind(),
            $m->getRainProbability()
        ], $measurements));

        return Command::SUCCESS;
    }
}

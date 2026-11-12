<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand{

    public function __construct(private readonly LocationRepository $locationRepository, private readonly WeatherUtil $weatherUtil){
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);

        if($location === null){
            $io->error('Lokacji nie ma');

            return Command::FAILURE;
        }
        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location: %s', $location->getCity()));
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

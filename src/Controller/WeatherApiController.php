<?php
 
namespace App\Controller;
 
use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
 
final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        WeatherUtil $util,
        #[MapQueryParameter] ?string $city = null,
        #[MapQueryParameter] ?string $country = null,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);
 
        if (!in_array($format, ['json', 'csv'], true)) {
            return $this->json(['error' => 'Nieznany format! '], 400);
        }
 
        if ($twig) {
            $content = $this->renderView(sprintf('weather_api/index.%s.twig', $format), [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
 
            return new Response(
                $content,
                200,
                ['Content-Type' => $format === 'json' ? 'application/json' : 'text/plain; charset=UTF-8']
            );
        }
 
        if ($format === 'json') {
            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getTemperature(),
                    'fahrenheit' => $m->getFahrenheit(),
                    'Humidity' => $m->getHumidity(),
                    'Wind' => $m->getWind(),
                    'RainProbability' => $m->getRainProbability(),
                ], $measurements),
            ]);
        }
 
        // csv
        $csv = "city,country,date,celsius,fahrenheit,Humidity,Wind,RainProbability\n";
        foreach ($measurements as $m) {
            $csv .= implode(',', [
                $city,
                $country,
                $m->getDate()->format('Y-m-d'),
                $m->getTemperature(),
                $m->getFahrenheit(),
                $m->getHumidity(),
                $m->getWind(),
                $m->getRainProbability(),
            ]) . "\n";
        }
 
        return new Response($csv, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
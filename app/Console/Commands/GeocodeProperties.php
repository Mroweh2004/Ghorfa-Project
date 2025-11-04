<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:geocode {--limit=10 : Number of properties to process at once} {--force : Force geocoding even if coordinates exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode properties that don\'t have latitude/longitude coordinates';

    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');

        $query = Property::query();

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude');
            });
        }

        $properties = $query->limit($limit)->get();

        if ($properties->isEmpty()) {
            $this->info('No properties found to geocode.');
            return;
        }

        $this->info("Processing {$properties->count()} properties...");

        $bar = $this->output->createProgressBar($properties->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($properties as $property) {
            $address = $this->buildAddress($property);
            
            if (empty($address)) {
                $this->warn("Skipping property {$property->id}: No address available");
                $bar->advance();
                continue;
            }

            $coordinates = $this->geocodingService->geocode($address);

            if ($coordinates) {
                $property->update([
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude']
                ]);
                $successCount++;
            } else {
                $this->warn("Failed to geocode property {$property->id}: {$address}");
                $errorCount++;
            }

            $bar->advance();

            // Add a small delay to avoid hitting rate limits
            usleep(100000); // 0.1 second
        }

        $bar->finish();
        $this->newLine();

        $this->info("Geocoding completed!");
        $this->info("Successfully geocoded: {$successCount} properties");
        $this->info("Failed to geocode: {$errorCount} properties");

        if ($errorCount > 0) {
            $this->warn("Some properties failed to geocode. You can run the command again to retry failed properties.");
        }
    }

    /**
     * Build a complete address string from property data
     *
     * @param Property $property
     * @return string
     */
    private function buildAddress(Property $property): string
    {
        $addressParts = array_filter([
            $property->address,
            $property->city,
            $property->country
        ]);

        return implode(', ', $addressParts);
    }
}

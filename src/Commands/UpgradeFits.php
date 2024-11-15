<?php

namespace CryptaTech\Seat\Fitting\Commands;

use CryptaTech\Seat\Fitting\Models\Doctrine;
use CryptaTech\Seat\Fitting\Models\Fitting;
use CryptaTech\Seat\Fitting\Models\OldDoctrine;
use CryptaTech\Seat\Fitting\Models\OldFitting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class UpgradeFits.
 */
class UpgradeFits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptatech:fittings:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'upgrade previous seat-fitting fittings to the new modelling';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info('Finding fits to upgrade!');

        $oldFits = OldFitting::all();
        $c = count($oldFits);

        $this->info('Found '.$c.' fits to process');

        $bar = $this->getProgressBar($c);
        $failedUpgrades = 0;

        $mapping = [];

        foreach ($oldFits as $oldFit) {
            try { // If a fit fails then we just add it to a list of errors.
                $f = Fitting::createFromEve($oldFit->eftfitting);
                $mapping[$oldFit->id] = $f->fitting_id;
            } catch (Exception $e) {
                Log::error(['fit' => $oldFit->eftfitting, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                $failedUpgrades += 1;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        $this->info('Fitting Migration Complete!');
        $this->info('Success: '.$c - $failedUpgrades);
        $this->warn('Failure : '.$failedUpgrades);

        $this->line('');

        $this->info('Updating Doctrine Fitting Mapping!');
        $oldDocs = OldDoctrine::all();
        $bar = $this->getProgressBar(count($oldDocs));

        foreach ($oldDocs as $oldDoc) {
            $newDoc = Doctrine::create([
                'name' => $oldDoc->name,
            ]);
            foreach ($oldDoc->fittings()->get() as $oldFit) {
                $newDoc->fittings()->attach($mapping[$oldFit->id]);
            }
            $bar->advance();

        }

        $bar->finish();
        $this->line('');

        $this->info('Doctrine Migration Complete!');

    }

    /**
     * Get a new progress bar to display based on the
     * amount of iterations we expect to use.
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function getProgressBar($iterations)
    {

        $bar = $this->output->createProgressBar($iterations);

        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');

        return $bar;
    }
}

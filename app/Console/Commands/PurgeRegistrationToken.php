<?php

namespace App\Console\Commands;

use App\Models\FcmRegistrationToken;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PurgeRegistrationToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:registration-token 
                            {--revoked : Only purge revoked registration tokens}
                            {--expired : Only purge expired registration tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge revoked and / or expired push notification registration tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expired = Carbon::now()->subDays(7);

        if (($this->option('revoked') && $this->option('expired')) ||
            (!$this->option('revoked') && !$this->option('expired'))
        ) {
            FcmRegistrationToken::where('revoked', 1)->orWhereDate('expires_at', '<', $expired)->delete();

            $this->info('Purged revoked tokens and tokens expired for more than seven days.');
        } elseif ($this->option('revoked')) {
            FcmRegistrationToken::where('revoked', 1)->delete();

            $this->info('Purged revoked tokens.');
        } elseif ($this->option('expired')) {
            FcmRegistrationToken::whereDate('expires_at', '<', $expired)->delete();

            $this->info('Purged tokens expired for more than seven days.');
        }
    }
}

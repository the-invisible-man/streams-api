<?php

namespace App\Lib\StandardLib\Tools;

use Illuminate\Console\Command;
use App\Lib\StandardLib\Contracts\CacheWarmer as CacheWarmerContract;

/**
 * Class CacheWarmer
 *
 * @package App\Lib\StandardLib\Tools
 * @author  Carlos Granados <granados.carlos91@gmail.com>
 */
class CacheWarmer extends Command
{
    /**
     * @var string
     */
    protected $signature = 'cache:warmup';

    /**
     * @var string
     */
    protected $description = 'Executes loaded cache warmers';

    /**
     * @var CacheWarmerContract[]
     */
    private $warmers;

    /**
     * CacheWarmer constructor.
     *
     * @param CacheWarmerContract[] $warmers
     */
    public function __construct(array $warmers)
    {
        parent::__construct();

        $this->warmers = $warmers;
    }

    public function handle()
    {
        foreach ($this->warmers as $warmer)
        {
            $this->info("Loading into cache with {$warmer->name()}");

            $total = $warmer->warmUp();

            $this->info("Loaded {$total} items into the cache.");
        }
    }
}
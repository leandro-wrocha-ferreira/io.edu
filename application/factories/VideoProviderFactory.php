<?php
namespace app\factories;

use app\domain\education\video\VideoProviderInterface;

/**
 * Factory for instantiating video provider drivers.
 */
class VideoProviderFactory
{
    /**
     * Create a driver instance based on the driver name and credentials.
     *
     * @param string $driver e.g. 'youtube', 'vimeo'
     * @param array|null $credentials API keys, etc.
     * @return VideoProviderInterface
     * @throws \InvalidArgumentException if driver is unknown
     */
    public static function make(string $driver, ?array $credentials = null): VideoProviderInterface
    {
        switch ($driver) {
            case 'youtube':
                // In a real implementation, require the YoutubeDriver class
                // return new \app\domain\education\video\drivers\YoutubeDriver($credentials);
                throw new \RuntimeException("YoutubeDriver not yet implemented.");
            case 'vimeo':
                throw new \RuntimeException("VimeoDriver not yet implemented.");
            default:
                throw new \InvalidArgumentException("Unknown video provider driver: {$driver}");
        }
    }
}

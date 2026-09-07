<?php
namespace app\domain\education\video;

/**
 * Interface that all video providers must implement.
 */
interface VideoProviderInterface
{
    /**
     * Check if the provider can handle the given URL.
     *
     * @param string $url
     * @return bool
     */
    public function supports(string $url): bool;

    /**
     * Extract the native video ID from a URL.
     *
     * @param string $url
     * @return string
     * @throws \InvalidArgumentException if URL is invalid
     */
    public function extract_id(string $url): string;

    /**
     * Fetch metadata from the provider's API.
     *
     * @param string $video_id
     * @return VideoMetadata
     * @throws \RuntimeException on API failure
     */
    public function fetch_metadata(string $video_id): VideoMetadata;
}

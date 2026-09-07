<?php

namespace app\domain\education\video;

/**
 * Standardized video metadata returned by providers.
 */
class VideoMetadata
{
	public readonly string $provider_name;
	public readonly string $video_id;
	public readonly int $duration_seconds;

	/**
	 * Constructor.
	 *
	 * @param string $provider_name
	 * @param string $video_id
	 * @param int $duration_seconds
	 */
	public function __construct(string $provider_name, string $video_id, int $duration_seconds)
	{
		$this->provider_name = $provider_name;
		$this->video_id = $video_id;
		$this->duration_seconds = $duration_seconds;
	}
}

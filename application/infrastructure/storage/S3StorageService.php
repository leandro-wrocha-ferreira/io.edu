<?php

namespace app\infrastructure\storage;

use app\domain\storage\StorageServiceInterface;
use Aws\S3\S3Client;
use RuntimeException;

/**
 * S3 implementation of StorageServiceInterface using AWS SDK PHP.
 *
 * Supports AWS S3, LocalStack and S3-compatible providers with multipart upload.
 */
class S3StorageService implements StorageServiceInterface
{
	/**
	 * S3 client instance.
	 *
	 * @var S3Client
	 */
	private S3Client $s3_client;

	/**
	 * Bucket name.
	 *
	 * @var string
	 */
	private string $bucket;

	/**
	 * Browser accessible endpoint override (for Docker/LocalStack).
	 *
	 * @var string|null
	 */
	private ?string $browser_endpoint;

	/**
	 * Internal endpoint.
	 *
	 * @var string|null
	 */
	private ?string $internal_endpoint;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $config Optional custom configuration
	 */
	public function __construct(array $config = [])
	{
		$endpoint = $config['endpoint'] ?? getenv('AWS_S3_ENDPOINT') ?: ($_ENV['AWS_S3_ENDPOINT'] ?? 'http://localstack:4566');
		$region = $config['region'] ?? getenv('AWS_S3_REGION') ?: ($_ENV['AWS_S3_REGION'] ?? 'us-east-1');
		$key = $config['key'] ?? getenv('AWS_ACCESS_KEY_ID') ?: ($_ENV['AWS_ACCESS_KEY_ID'] ?? 'test');
		$secret = $config['secret'] ?? getenv('AWS_SECRET_ACCESS_KEY') ?: ($_ENV['AWS_SECRET_ACCESS_KEY'] ?? 'test');

		$this->bucket = $config['bucket'] ?? getenv('AWS_S3_BUCKET') ?: ($_ENV['AWS_S3_BUCKET'] ?? 'ioedu-media');
		$this->internal_endpoint = !empty($endpoint) ? (string) $endpoint : null;
		$this->browser_endpoint = $config['browser_endpoint'] ?? getenv('AWS_S3_BROWSER_ENDPOINT') ?: ($_ENV['AWS_S3_BROWSER_ENDPOINT'] ?? 'http://localhost:4566');


		$client_args = [
			'version' => 'latest',
			'region' => $region,
			'use_path_style_endpoint' => true,
			'credentials' => [
				'key' => $key,
				'secret' => $secret,
			],
		];

		if (!empty($endpoint)) {
			$client_args['endpoint'] = $endpoint;
		}

		$this->s3_client = new S3Client($client_args);
	}

	/**
	 * Initiate multipart upload session.
	 *
	 * @param string $filename Original file name
	 * @param string $content_type MIME type
	 * @param string $folder Destination folder
	 * @return array{upload_id: string, key: string, bucket: string}
	 */
	public function initiate_multipart(string $filename, string $content_type, string $folder = 'uploads'): array
	{
		$clean_name = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($filename));
		$key = sprintf('%s/%s/%s_%s', trim($folder, '/'), date('Y/m'), bin2hex(random_bytes(8)), $clean_name);

		try {
			$result = $this->s3_client->createMultipartUpload([
				'Bucket' => $this->bucket,
				'Key' => $key,
				'ContentType' => $content_type ?: 'application/octet-stream',
			]);

			return [
				'upload_id' => (string) $result['UploadId'],
				'key' => $key,
				'bucket' => $this->bucket,
			];
		} catch (\Exception $exception) {
			throw new RuntimeException('Failed to initiate multipart upload: ' . $exception->getMessage(), 0, $exception);
		}
	}

	/**
	 * Generate presigned URL for an upload part.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param int $part_number Part number (1-based)
	 * @return string Presigned upload URL
	 */
	public function sign_part(string $upload_id, string $key, int $part_number): string
	{
		try {
			$command = $this->s3_client->getCommand('UploadPart', [
				'Bucket' => $this->bucket,
				'Key' => $key,
				'UploadId' => $upload_id,
				'PartNumber' => $part_number,
			]);

			$request = $this->s3_client->createPresignedRequest($command, '+60 minutes');
			$uri = (string) $request->getUri();

			if ($this->browser_endpoint !== null && $this->internal_endpoint !== null) {
				$internal_host = parse_url($this->internal_endpoint, PHP_URL_HOST);
				$internal_port = parse_url($this->internal_endpoint, PHP_URL_PORT);
				$browser_host = parse_url($this->browser_endpoint, PHP_URL_HOST);
				$browser_port = parse_url($this->browser_endpoint, PHP_URL_PORT);

				$search = $internal_host . ($internal_port ? ':' . $internal_port : '');
				$replace = $browser_host . ($browser_port ? ':' . $browser_port : '');
				$uri = str_replace($search, $replace, $uri);
			}

			return $uri;
		} catch (\Exception $exception) {
			throw new RuntimeException('Failed to generate presigned part URL: ' . $exception->getMessage(), 0, $exception);
		}
	}

	/**
	 * List already uploaded parts for an ongoing multipart upload session.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @return array<array{PartNumber: int, ETag: string}>
	 */
	public function list_parts(string $upload_id, string $key): array
	{
		try {
			$result = $this->s3_client->listParts([
				'Bucket' => $this->bucket,
				'Key' => $key,
				'UploadId' => $upload_id,
			]);

			$parts = [];
			if (!empty($result['Parts'])) {
				foreach ($result['Parts'] as $part) {
					$parts[] = [
						'PartNumber' => (int) $part['PartNumber'],
						'ETag' => trim((string) $part['ETag'], '"'),
					];
				}
			}

			return $parts;
		} catch (\Exception $exception) {
			return [];
		}
	}

	/**
	 * Complete a multipart upload session.

	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param array<array{PartNumber: int, ETag: string}> $parts Uploaded parts list
	 * @return array{location: string, key: string, bucket: string}
	 */
	public function complete_multipart(string $upload_id, string $key, array $parts): array
	{
		try {
			$formatted_parts = [];
			foreach ($parts as $part) {
				$formatted_parts[] = [
					'PartNumber' => (int) $part['PartNumber'],
					'ETag' => trim((string) $part['ETag'], '"'),
				];
			}

			usort($formatted_parts, static function (array $part_a, array $part_b): int {
				return $part_a['PartNumber'] <=> $part_b['PartNumber'];
			});

			$result = $this->s3_client->completeMultipartUpload([
				'Bucket' => $this->bucket,
				'Key' => $key,
				'UploadId' => $upload_id,
				'MultipartUpload' => [
					'Parts' => $formatted_parts,
				],
			]);

			$location = (string) ($result['Location'] ?? '');
			if ($this->browser_endpoint !== null && $this->internal_endpoint !== null) {
				$internal_host = parse_url($this->internal_endpoint, PHP_URL_HOST);
				$internal_port = parse_url($this->internal_endpoint, PHP_URL_PORT);
				$browser_host = parse_url($this->browser_endpoint, PHP_URL_HOST);
				$browser_port = parse_url($this->browser_endpoint, PHP_URL_PORT);

				$search = $internal_host . ($internal_port ? ':' . $internal_port : '');
				$replace = $browser_host . ($browser_port ? ':' . $browser_port : '');
				$location = str_replace($search, $replace, $location);
			}

			return [
				'location' => $location,
				'key' => $key,
				'bucket' => $this->bucket,
			];
		} catch (\Exception $exception) {
			throw new RuntimeException('Failed to complete multipart upload: ' . $exception->getMessage(), 0, $exception);
		}
	}
}

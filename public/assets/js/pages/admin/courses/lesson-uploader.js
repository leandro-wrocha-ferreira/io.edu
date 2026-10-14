/**
 * Resumable S3 Multipart Upload Client & Video Metadata Reader — io.edu LMS
 * File: public/assets/js/pages/admin/courses/lesson-uploader.js
 */
(function (global) {
	'use strict';

	const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB minimum part size for AWS S3 multipart upload

	/**
	 * Active uploads counter and registry.
	 */
	const UploadTracker = {
		activeCount: 0,

		increment: function () {
			this.activeCount++;
			this.updateBadge();
		},

		decrement: function () {
			this.activeCount = Math.max(0, this.activeCount - 1);
			this.updateBadge();
		},

		hasActive: function () {
			return this.activeCount > 0;
		},

		updateBadge: function () {
			const badge = document.getElementById('active-uploads-badge');
			const countLabel = document.getElementById('active-uploads-count');
			if (!badge) {
				return;
			}

			if (this.activeCount > 0) {
				badge.classList.remove('d-none');
				if (countLabel) {
					countLabel.textContent = this.activeCount === 1 ? '1 arquivo' : this.activeCount + ' arquivos';
				}
			} else {
				badge.classList.add('d-none');
			}
		}
	};

	// Prevent accidental navigation during active uploads
	window.addEventListener('beforeunload', function (beforeUnloadEvent) {
		if (UploadTracker.hasActive()) {
			beforeUnloadEvent.preventDefault();
			beforeUnloadEvent.returnValue = 'Existem uploads de vídeo ou arquivo em andamento. Se você sair agora, os envios serão interrompidos.';
			return beforeUnloadEvent.returnValue;
		}
	});

	/**
	 * Build unique local cache key for resumable upload session.
	 *
	 * @param {File} file
	 * @param {string} folder
	 * @returns {string}
	 */
	function getResumeSessionKey(file, folder) {
		return 'ioedu_s3_' + folder + '_' + file.name + '_' + file.size + '_' + file.lastModified;
	}

	/**
	 * Perform resumable multipart upload directly to S3/LocalStack.
	 *
	 * @param {File} file Target file
	 * @param {string} folder Destination folder ('videos'|'attachments')
	 * @param {Function} onProgress Progress callback (percent, statusText)
	 * @returns {Promise<{location: string, key: string}>}
	 */
	async function uploadMultipartToS3(file, folder, onProgress) {
		UploadTracker.increment();
		const sessionKey = getResumeSessionKey(file, folder);

		try {
			let uploadId = null;
			let objectKey = null;
			const existingPartsMap = new Map();

			// Check for existing session in localStorage
			const cachedSession = localStorage.getItem(sessionKey);
			if (cachedSession) {
				try {
					const sessionData = JSON.parse(cachedSession);
					// Query S3 for already uploaded parts
					const listResponse = await Http.post('/admin/storage/multipart/listar-partes', {
						upload_id: sessionData.uploadId,
						key: sessionData.objectKey
					});

					if (listResponse && Array.isArray(listResponse.parts) && listResponse.parts.length > 0) {
						uploadId = sessionData.uploadId;
						objectKey = sessionData.objectKey;
						listResponse.parts.forEach(function (partItem) {
							existingPartsMap.set(partItem.PartNumber, partItem.ETag);
						});
					}
				} catch (resumeCheckError) {
					localStorage.removeItem(sessionKey);
				}
			}

			// If no valid session, initiate a new one
			if (!uploadId || !objectKey) {
				onProgress(5, 'Iniciando upload seguro...');
				const initResponse = await Http.post('/admin/storage/multipart/iniciar', {
					filename: file.name,
					content_type: file.type || 'application/octet-stream',
					folder: folder
				});

				uploadId = initResponse.upload_id;
				objectKey = initResponse.key;

				localStorage.setItem(sessionKey, JSON.stringify({
					uploadId: uploadId,
					objectKey: objectKey
				}));
			}

			const totalBytes = file.size;
			const totalParts = Math.max(1, Math.ceil(totalBytes / CHUNK_SIZE));
			const finalUploadedParts = [];

			let uploadedBytesTotal = 0;

			// Upload parts, skipping those already in S3
			for (let partNumber = 1; partNumber <= totalParts; partNumber++) {
				const startByte = (partNumber - 1) * CHUNK_SIZE;
				const endByte = Math.min(totalBytes, startByte + CHUNK_SIZE);
				const partBytes = endByte - startByte;

				if (existingPartsMap.has(partNumber)) {
					// Already on S3! Resume from where it left off
					finalUploadedParts.push({
						PartNumber: partNumber,
						ETag: existingPartsMap.get(partNumber)
					});
					uploadedBytesTotal += partBytes;
					continue;
				}

				const chunkBlob = file.slice(startByte, endByte);
				const calculatedPercent = Math.round((uploadedBytesTotal / totalBytes) * 85) + 5;
				onProgress(calculatedPercent, 'Enviando arquivo...');

				// Generate fresh Presigned Part URL on demand
				const signResponse = await Http.post('/admin/storage/multipart/assinar-parte', {
					upload_id: uploadId,
					key: objectKey,
					part_number: partNumber
				});

				const presignedUrl = signResponse.url;

				const partEtag = await new Promise(function (resolve, reject) {
					const xhrRequest = new XMLHttpRequest();
					xhrRequest.open('PUT', presignedUrl, true);

					xhrRequest.onload = function () {
						if (xhrRequest.status >= 200 && xhrRequest.status < 300) {
							const etagHeader = xhrRequest.getResponseHeader('ETag') || xhrRequest.getResponseHeader('etag') || '';
							resolve(etagHeader.replace(/"/g, ''));
						} else {
							reject(new Error('Falha no upload da parte ' + partNumber));
						}
					};

					xhrRequest.onerror = function () {
						reject(new Error('Erro de conexão ao enviar dados para a nuvem.'));
					};

					xhrRequest.send(chunkBlob);
				});

				finalUploadedParts.push({
					PartNumber: partNumber,
					ETag: partEtag
				});

				uploadedBytesTotal += partBytes;
			}

			onProgress(95, 'Finalizando montagem do arquivo...');
			const completeResponse = await Http.post('/admin/storage/multipart/concluir', {
				upload_id: uploadId,
				key: objectKey,
				parts: finalUploadedParts
			});

			localStorage.removeItem(sessionKey);
			onProgress(100, 'Upload concluído com sucesso!');
			return completeResponse;
		} finally {
			UploadTracker.decrement();
		}
	}

	/**
	 * Extract duration in seconds from local video file in browser via HTML5.
	 *
	 * @param {File} file
	 * @returns {Promise<number>}
	 */
	function extractVideoDuration(file) {
		return new Promise(function (resolve) {
			const temporaryVideo = document.createElement('video');
			temporaryVideo.preload = 'metadata';
			const objectUrl = URL.createObjectURL(file);
			temporaryVideo.src = objectUrl;

			temporaryVideo.onloadedmetadata = function () {
				URL.revokeObjectURL(objectUrl);
				const durationSeconds = Math.round(temporaryVideo.duration) || 0;
				resolve(durationSeconds);
			};

			temporaryVideo.onerror = function () {
				URL.revokeObjectURL(objectUrl);
				resolve(0);
			};
		});
	}

	/**
	 * Extract YouTube 11-char ID from full URL or raw ID string.
	 *
	 * @param {string} inputString
	 * @returns {string}
	 */
	function extractYoutubeId(inputString) {
		const trimmed = (inputString || '').trim();
		if (!trimmed) {
			return '';
		}

		const match = trimmed.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
		if (match && match[1]) {
			return match[1];
		}

		if (/^[\w-]{11}$/.test(trimmed)) {
			return trimmed;
		}

		return trimmed;
	}

	global.LessonUploader = {
		UploadTracker: UploadTracker,
		uploadMultipartToS3: uploadMultipartToS3,
		extractVideoDuration: extractVideoDuration,
		extractYoutubeId: extractYoutubeId
	};
})(window);

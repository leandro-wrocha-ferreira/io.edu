/**
 * Global HTTP Client utility using the native Fetch API.
 * File: public/assets/js/http.js
 *
 * Automatically attaches standard AJAX and JSON identification headers:
 * - X-App-Json: application/json
 * - X-Requested-With: XMLHttpRequest
 * - Accept: application/json
 *
 * Handles JSON serialization, FormData payloads, HTTP status >= 400 error rejection,
 * CSRF token attachment, and button loading/disabled states.
 */
(function (global) {
	'use strict';

	/**
	 * Retrieve CSRF token from document cookies or meta tag if configured.
	 *
	 * @returns {string|null}
	 */
	function getCsrfToken() {
		const meta = document.querySelector('meta[name="csrf-token"]');
		if (meta && meta.content) {
			return meta.content;
		}

		const match = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]*)/);
		if (match) {
			return decodeURIComponent(match[1]);
		}

		return null;
	}

	/**
	 * Manage button loading and disabled state during in-flight network requests.
	 *
	 * @param {HTMLElement|string|null} button
	 * @param {boolean} isLoading
	 */
	function toggleButtonLoading(button, isLoading) {
		const element = typeof button === 'string' ? document.querySelector(button) : button;
		if (!element || !(element instanceof HTMLElement)) {
			return;
		}

		if (isLoading) {
			element.dataset.originalHtml = element.innerHTML;
			element.disabled = true;
			element.classList.add('disabled');
			element.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processando...';
		} else {
			element.disabled = false;
			element.classList.remove('disabled');
			if (element.dataset.originalHtml !== undefined) {
				element.innerHTML = element.dataset.originalHtml;
				delete element.dataset.originalHtml;
			}
		}
	}

	/**
	 * Core request executor.
	 *
	 * @param {string} url
	 * @param {Object} [options={}]
	 * @returns {Promise<any>}
	 */
	async function request(url, options = {}) {
		const method = (options.method || 'GET').toUpperCase();
		const headers = Object.assign({
			'X-App-Json': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'application/json',
		}, options.headers || {});

		const csrfToken = getCsrfToken();
		if (csrfToken && !headers['X-CSRF-TOKEN']) {
			headers['X-CSRF-TOKEN'] = csrfToken;
		}

		let body = options.body;
		if (options.data !== undefined) {
			if (options.data instanceof FormData) {
				body = options.data;
				if (csrfToken && !body.has('csrf_test_name')) {
					body.append('csrf_test_name', csrfToken);
				}
			} else if (typeof options.data === 'object' && options.data !== null) {
				headers['Content-Type'] = 'application/json; charset=utf-8';
				body = JSON.stringify(options.data);
			} else {
				body = options.data;
			}
		}

		const button = options.button || null;
		if (button) {
			toggleButtonLoading(button, true);
		}

		try {
			const fetchOptions = {
				method: method,
				headers: headers,
				credentials: options.credentials || 'same-origin',
			};

			if (body !== undefined && method !== 'GET' && method !== 'HEAD') {
				fetchOptions.body = body;
			}

			const response = await fetch(url, fetchOptions);
			const contentType = response.headers.get('content-type') || '';
			let responseData;

			if (contentType.includes('application/json')) {
				responseData = await response.json();
			} else {
				responseData = await response.text();
			}

			if (!response.ok) {
				const errorMessage = (typeof responseData === 'object' && responseData !== null)
					? (responseData.message || responseData.error || `Erro na requisição (${response.status})`)
					: `Erro na requisição (${response.status}): ${response.statusText}`;

				const error = new Error(errorMessage);
				error.status = response.status;
				error.statusText = response.statusText;
				error.data = responseData;
				error.response = response;
				throw error;
			}

			return responseData;
		} finally {
			if (button) {
				toggleButtonLoading(button, false);
			}
		}
	}

	const HttpClient = {
		/**
		 * Perform a GET request.
		 *
		 * @param {string} url
		 * @param {Object} [options={}]
		 * @returns {Promise<any>}
		 */
		get(url, options = {}) {
			return request(url, Object.assign({}, options, { method: 'GET' }));
		},

		/**
		 * Perform a POST request.
		 *
		 * @param {string} url
		 * @param {any} [data]
		 * @param {Object|HTMLElement|string} [optionsOrButton={}]
		 * @returns {Promise<any>}
		 */
		post(url, data, optionsOrButton = {}) {
			const options = (optionsOrButton instanceof HTMLElement || typeof optionsOrButton === 'string')
				? { button: optionsOrButton }
				: optionsOrButton;

			return request(url, Object.assign({}, options, { method: 'POST', data: data }));
		},

		/**
		 * Perform a PUT request.
		 *
		 * @param {string} url
		 * @param {any} [data]
		 * @param {Object|HTMLElement|string} [optionsOrButton={}]
		 * @returns {Promise<any>}
		 */
		put(url, data, optionsOrButton = {}) {
			const options = (optionsOrButton instanceof HTMLElement || typeof optionsOrButton === 'string')
				? { button: optionsOrButton }
				: optionsOrButton;

			return request(url, Object.assign({}, options, { method: 'PUT', data: data }));
		},

		/**
		 * Perform a PATCH request.
		 *
		 * @param {string} url
		 * @param {any} [data]
		 * @param {Object|HTMLElement|string} [optionsOrButton={}]
		 * @returns {Promise<any>}
		 */
		patch(url, data, optionsOrButton = {}) {
			const options = (optionsOrButton instanceof HTMLElement || typeof optionsOrButton === 'string')
				? { button: optionsOrButton }
				: optionsOrButton;

			return request(url, Object.assign({}, options, { method: 'PATCH', data: data }));
		},

		/**
		 * Perform a DELETE request.
		 *
		 * @param {string} url
		 * @param {Object} [options={}]
		 * @returns {Promise<any>}
		 */
		delete(url, options = {}) {
			return request(url, Object.assign({}, options, { method: 'DELETE' }));
		},

		/**
		 * General request method.
		 */
		request: request,
	};

	global.Http = HttpClient;
	global.HttpClient = HttpClient;

	if (typeof module !== 'undefined' && module.exports) {
		module.exports = HttpClient;
	}
})(typeof window !== 'undefined' ? window : this);

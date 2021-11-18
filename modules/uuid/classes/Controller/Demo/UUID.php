<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Demo_UUID extends Controller_Demo {

	public function demo_v4_random()
	{
		// Generate a random UUID
		$response = UUID::v4();

		$this->content = Debug::vars($response);
	}

	public function demo_v3_namespace()
	{
		if ($this->request->method() === 'POST')
		{
			$url = $this->request->post('url');

			if ( ! filter_var($url, FILTER_VALIDATE_URL))
			{
				// Not a valid URL
				$response = 'Invalid URL.';
			}
			else
			{
				// Generate the standard v3 UUID for this URL
				$uuid = UUID::v3(UUID::URL, $url);

				// Set the response
				$response = Debug::vars($uuid);
			}
		}
		else
		{
			$url = 'http://';
		}

		$this->content = View::factory('demo/form')
			->set('message', 'Enter a URL to generate a v3 (md5) UUID.')
			->set('inputs', array(
				'URL' => Form::input('url', $url),
			))
			->bind('footer', $response)
			;
	}

	public function demo_v5_namespace()
	{
		if ($this->request->method() === 'POST')
		{
			$url = $this->request->post('url');

			if ( ! filter_var($url, FILTER_VALIDATE_URL))
			{
				// Not a valid URL
				$response = 'Invalid URL.';
			}
			else
			{
				// Generate the standard v5 UUID for this URL
				$uuid = UUID::v5(UUID::URL, $url);

				// Set the response
				$response = Debug::vars($uuid);
			}
		}
		else
		{
			$url = 'http://';
		}

		$this->content = View::factory('demo/form')
			->set('message', 'Enter a URL to generate a v5 (sha1) UUID.')
			->set('inputs', array(
				'URL' => Form::input('url', $url),
			))
			->bind('footer', $response)
			;
	}

}

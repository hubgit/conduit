<?php

namespace Conduit;

require_once __DIR__ . '/../lib/Handler.php';

//define('DEBUG', true);

class DOI extends Handler {
	protected $formats = array(
		'application/json' => 'crossrefMetadata',
		'application/citeproc+json' => 'crossrefMetadata',
		'text/html' => 'crossRefRedirect',
		'application/pdf' => 'pdfLookup',
	);

	/**
	 * Output JSON metadata for a DOI from CrossRef
	 */
	function crossrefMetadata() {
		$http = new HTTP;
		$http->get('http://dx.doi.org/' . $this->id, array(), array('Accept: ' . $this->format), array(CURLOPT_RETURNTRANSFER => false));
	}

	/**
	 * Output HTML for a DOI from CrossRef
	 */
	function crossRefRedirect() {
		$http = new HTTP;
		$http->get('http://dx.doi.org/' . $this->id, array(), array('Accept: text/html'), array(CURLOPT_RETURNTRANSFER => false));
	}

	/**
	 * Fetch HTML for a DOI, and look for a PDF
	 */
	function pdfLookup() {
		$http = new HTTP;
		$result = $http->get('http://dx.doi.org/' . $this->id, array(), array('Accept: text/html'));

		$dom = new \DOMDocument;
		@$dom->loadHTML($result);

		$xpath = new \DOMXPath($dom);

		$nodes = $xpath->query("//link[@rel='alternate'][@type='application/pdf'][@href]");
		if ($nodes->length) return $this->redirect($nodes->item(0)->getAttribute('href'));

		$nodes = $xpath->query("//meta[@name='citation_pdf_url'][@content]");
		if ($nodes->length) return $this->redirect($nodes->item(0)->getAttribute('content'));
	}
}

//$_GET['id'] = '10.1126/science.169.3946.635';
//$_SERVER['HTTP_ACCEPT'] = 'application/pdf';

$handler = new DOI($_GET['id']);
$handler->handle();
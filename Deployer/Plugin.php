<?php

namespace Deployer;

use Deployer\Git\Repository;

class Plugin implements Package {

	protected $file;
	protected $name;
	protected $pluginURI;
	protected $version;
	protected $description;
	protected $author;
	protected $authorURI;
	protected $textDomain;
	protected $domainPath;
	protected $network;
	protected $title;
	protected $authorName;
	protected $repository;
	protected $deployerStatus;
	protected $pushToDeploy;
	protected $host;
	protected $subdirectory;

	public static function fromWpArray( $file, array $array ) {
		$plugin = new static();

		$plugin->file        = $file;
		$plugin->name        = $array['Name'];
		$plugin->pluginURI   = $array['PluginURI'];
		$plugin->version     = $array['Version'];
		$plugin->description = $array['Description'];
		$plugin->author      = $array['Author'];
		$plugin->authorURI   = $array['AuthorURI'];
		$plugin->textDomain  = $array['TextDomain'];
		$plugin->domainPath  = $array['DomainPath'];
		$plugin->network     = $array['Network'];
		$plugin->title       = $array['Title'];
		$plugin->authorName  = $array['AuthorName'];

		return $plugin;
	}

	public function getSlug() {
		if ( ! $this->hasSubdirectory() ) {
			return $this->repository->getSlug();
		}

		return end( explode( '/', $this->getSubdirectory() ) );
	}

	public function getSubdirectory() {
		return $this->subdirectory;
	}

	public function hasSubdirectory() {
		return ! ( is_null( $this->getSubdirectory() ) or $this->getSubdirectory() === '' );
	}

	public function setSubdirectory( $subdirectory ) {
		$this->subdirectory = $subdirectory;
	}

	public function setDeployerStatus( $deployerStatus ) {
		$this->deployerStatus = $deployerStatus;
	}

	public function setPushToDeploy( $pushToDeploy ) {
		$this->pushToDeploy = $pushToDeploy;
	}

	public function getPushToDeployUrl() {
		$repo = base64_encode( $this->repository );

		$url = sprintf(
			'%s?wpdeployer-hook&token=%s&repo=%s',
			get_site_url(),
			get_option( 'wpdeployer_token' ),
			$repo
		);

		return esc_attr( $url );
	}

	public function setRepository( Repository $repository ) {
		$this->repository = $repository;
	}

	public function setHost( $host ) {
		$this->host = $host;
	}

	public function __get( $name ) {
		$method = 'get' . ucfirst( $name );

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}

		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
	}

	public function __toString() {
		return $this->file;
	}

	public function getIdentifier() {
		return $this->file;
	}
}

<?php

namespace Deployer\License;

interface LicenseApi {

	public function getLicenseKey( $key );
	public function registerKeyForSite( $key );
	public function removeLicenseFomSite( $key );
}

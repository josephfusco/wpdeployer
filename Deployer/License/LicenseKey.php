<?php

namespace Deployer\License;

class LicenseKey
{
    private $id;
    private $email;
    private $licenses;
    private $token;
    private $usedLicenses;
    private $validUntil;
    private $autoRenew;

    public static function fromShipperResponseArray(array $array)
    {
        $key = new LicenseKey;

        $key->id = $array['id'];
        $key->email = $array['email'];
        $key->licenses = $array['allowed_licenses'];
        $key->token = $array['token'];
        $key->usedLicenses = $array['used_licenses'];
        $key->validUntil = $array['valid_until'];
        $key->autoRenew = $array['auto_renew'];

        return $key;
    }

    public static function fromEddResponseArray(array $array)
    {
        $key = new LicenseKey;

        $key->id = null;
        $key->email = $array['customer_email'];
        $key->licenses = $array['license_limit'];
        $key->token = $array['token'];
        $key->usedLicenses = $array['site_count'];
        $key->validUntil = $array['expires'];
        $key->autoRenew = null;

        return $key;
    }

    public function id($id = null)
    {
        if ( ! is_null($id)) {
            return $this->id = $id;
        }

        return $this->id;
    }

    public function email($email = null)
    {
        if ( ! is_null($email)) {
            return $this->email = $email;
        }

        return $this->email;
    }

    public function licenses($licenses = null)
    {
        if ( ! is_null($licenses)) {
            return $this->licenses = $licenses;
        }

        return $this->licenses;
    }

    public function token($token = null)
    {
        if ( ! is_null($token)) {
            return $this->token = $token;
        }

        return $this->token;
    }

    public function usedLicenses($usedLicenses = null)
    {
        if ( ! is_null($usedLicenses)) {
            return $this->usedLicenses = $usedLicenses;
        }

        return $this->usedLicenses;
    }

    public function validUntil($validUntil = null)
    {
        if ( ! is_null($validUntil)) {
            return $this->validUntil = $validUntil;
        }

        return $this->validUntil;
    }

    public function hasExpired()
    {
        $now = time();
        $validUntil = strtotime($this->validUntil);

        return $now > $validUntil;
    }

    public function autoRenew($autoRenew = null)
    {
        if ( ! is_null($autoRenew)) {
            return $this->autoRenew = $autoRenew;
        }

        return $this->autoRenew;
    }
}

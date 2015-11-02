<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Exceptions\CPanelErrorException;
use Chernoff\WHM\Exceptions\CPanelNotFoundException;
use Chernoff\WHM\Interfaces\ManageAddonDomainInterface;
use Chernoff\WHM\Interfaces\ManageParkedDomainInterface;
use Chernoff\WHM\Interfaces\ManageUploadInterface;
use Chernoff\WHM\ValidationRules\AccountNotFound;
use Chernoff\WHM\ValidationRules\DomainRequestError;

use Exception;

/**
 * Class CPanel
 * @package Chernoff\WHM
 */
class CPanel extends WHMBase implements ManageAddonDomainInterface, ManageParkedDomainInterface, ManageUploadInterface
{
    /** Default port for secured cPanel connection */
    protected $defaultPort = 2083;

    /**
     * @param null $username
     * @return array
     */
    public function domainsGetParked($username = null)
    {
        $domains = array();
        $request = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "Park",
            "cpanel_jsonapi_func"   => "listparkeddomains",
        ]);

        if (!empty($request->cpanelresult->data)) {
            foreach ($request->cpanelresult->data as $domain) {
                $domains[] = $domain->domain;
            }
        }

        return $domains;
    }

    /**
     * @param string $domain
     * @param null $username
     * @throws CPanelErrorException
     * @return bool
     */
    public function domainPark($domain, $username = null) {
        $response = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "Park",
            "cpanel_jsonapi_func"   => "park",
            "domain"                => $domain,
        ], $this->addRule(new DomainRequestError));

        return ($response->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param string $domain
     * @param null $username
     * @return bool
     */
    public function domainParked($domain, $username = null) {
        return in_array($domain, $this->domainsGetParked($username));
    }

    /**
     * @param string $domain
     * @param null $username
     * @throws CPanelErrorException
     * @return bool
     */
    public function domainUnpark($domain, $username = null) {
        $response = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "Park",
            "cpanel_jsonapi_func"   => "unpark",
            "domain"                => $domain,
        ], $this->addRule(new DomainRequestError));

        return ($response->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param null $username
     * @return array
     */
    public function domainsGetAddon($username = null)
    {
        $domains = array();
        $request = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "Park",
            "cpanel_jsonapi_func"   => "listaddondomains",
        ]);

        if (!empty($request->cpanelresult->data)) {
            foreach ($request->cpanelresult->data as $domain) {
                $domains[] = $domain->domain;
            }
        }

        return $domains;
    }

    /**
     * @param string $domain
     * @param null $username
     * @throws CPanelErrorException
     * @return bool
     */
    public function domainAdd($domain, $username = null) {
        $response = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "addaddondomain",
            "newdomain"             => $domain,
            "subdomain"             => str_replace(".", "-", $domain),
            "dir"                   => "/public_html/{$domain}"
        ], $this->addRule(new DomainRequestError));

        return ($response->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param string $domain
     * @param null $username
     * @return bool
     */
    public function domainAdded($domain, $username = null) {
        return in_array($domain, $this->domainsGetAddon($username));
    }

    /**
     * @param string $domain
     * @param null $username
     * @throws CPanelErrorException
     * @throws Exception
     * @return bool
     */
    public function domainRemove($domain, $username) {
        $subdomain = str_replace(".", "-", $domain);
        $request   = $this->send("json-api/accountsummary", ["user" => strtolower($username)], $this->addRule(new AccountNotFound));
        $user      = $request->acct[0];
        $response  = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "deladdondomain",
            "domain"                => $domain,
            "subdomain"             => "{$subdomain}.{$user->domain}",
        ], $this->addRule(new DomainRequestError));

        return ($response->cpanelresult->data[0]->result == 1);
    }

    /**
     * @return mixed
     * @throws CPanelNotFoundException
     * @throws Exception
     */
    public function getStats() {
        $stats = array(
            'bandwidthusage',
            'diskusage',
            'phpversion',
            'apacheversion',
            'hostingpackage',
            'parkeddomains',
            'addondomains',
        );

        $request = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_module" => "StatsBar",
            "cpanel_jsonapi_func"   => "stat",
            "display"               => implode("|", $stats),
        ]);

        return $request->cpanelresult->data;
    }

    /**
     * @TODO WTF is wrong with you, cPanel?
     *
     * @param array $files
     * @return bool
     */
    public function upload(array $files)
    {
        $processed = $results = array();

        // Preprocess files list to optimize upload of several files to single
        // target dir
        foreach ($files as $from => $to) {
            $parts = explode("/", trim($to, "/"));
            $file  = array_pop($parts);
            $path  = implode("/", $parts) ?: "";

            $processed[$path][$from] = $file;
        }

        // Upload the files
        foreach ($processed as $target => $files) {
            $payload = array("dir" => urlencode($target));
            $index   = 1;

            foreach ($files as $from => $to) {
                $payload["file-{$index}"] = array("@" . $from, $to);
                $index++;
            }

            $response = $this->deployer->send(
                'execute/Fileman/upload_files',
                array(),
                $payload,
                'POST'
            );

            $this->validator->validate($response, $this->defaultRules);

            $results[] = $response;
        }

        return $results;
    }
}

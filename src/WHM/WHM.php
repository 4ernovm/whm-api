<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Interfaces\ManageAccountInterface;
use Chernoff\WHM\Interfaces\ManageAddonDomainInterface;
use Chernoff\WHM\ValidationRules\AccountNotFound;
use Chernoff\WHM\ValidationRules\AccountRequestError;
use Chernoff\WHM\ValidationRules\DomainRequestError;
use Chernoff\WHM\ValidationRules\IsEmpty;
use Exception;

/**
 * Class WHM
 * @package Chernoff\WHM
 */
class WHM extends WHMBase implements ManageAddonDomainInterface, ManageAccountInterface
{
    /** Default port for secured WHM connection */
    protected $defaultPort = 2087;

    /**
     * @param $whmUser
     * @return mixed
     */
    public function getInfo($whmUser)
    {
        $request = $this->send(
            "json-api/accountsummary",
            ["user" => strtolower($whmUser)],
            $this->addRules([new IsEmpty, new AccountNotFound]));

        return $request->acct[0];
    }

    /**
     * @param string $whmUser
     * @param string $amount
     * @return bool|mixed
     */
    public function addonDomainLimitSet($whmUser, $amount)
    {
        $request = $this->send(
            "json-api/modifyacct",
            ["user" => strtolower($whmUser), "MAXADDON" => $amount],
            $this->addRules([new IsEmpty, new AccountRequestError]));

        return ($request->result[0]->status == 1);
    }

    /**
     * @param string $domain
     * @param null $whmUser
     * @return bool|mixed
     * @throws Exception
     */
    public function domainAdd($domain, $whmUser)
    {
        $request = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_user"   => strtolower($whmUser),
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "addaddondomain",
            "newdomain"             => $domain,
            "subdomain"             => str_replace(".", "-", $domain),
            "dir"                   => "/domains/{$domain}"
        ], $this->addRules([new IsEmpty, new DomainRequestError]));

        return ($request->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param string $domain
     * @param string $whmUser
     * @return bool|mixed
     */
    public function domainRemove($domain, $whmUser)
    {
        $subdomain = str_replace(".", "-", $domain);
        $creds     = $this->deployer->getCredentials();
        $user      = $this->getInfo($creds["username"]);
        $request   = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_user"   => strtolower($whmUser),
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "deladdondomain",
            "domain"                => $domain,
            "subdomain"             => "{$subdomain}.{$user->domain}"
        ], $this->addRules([new IsEmpty, new DomainRequestError]));

        return ($request->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param string $domain
     * @param null $whmUser
     * @return bool|mixed
     */
    public function domainAdded($domain, $whmUser)
    {
        return in_array($domain, $this->domainsGetAddon($whmUser));
    }

    /**
     * @param $whmUser
     * @return array
     */
    public function domainsGetAddon($whmUser)
    {
        $request = $this->deployer->send("json-api/cpanel", [
            "cpanel_jsonapi_user"   => strtolower($whmUser),
            "cpanel_jsonapi_module" => "Park",
            "cpanel_jsonapi_func"   => "listaddondomains"
        ]);

        $domains = [];

        if (!empty($request->cpanelresult->data)) {
            foreach ($request->cpanelresult->data as $domain) {
                $domains[] = $domain->domain;
            }
        }

        return $domains;
    }
}

<?php

namespace Chernoff\WHM;

use Chernoff\WHM\Interfaces\ManageAccountInterface;
use Chernoff\WHM\Interfaces\ManageAddonDomainInterface;
use Chernoff\WHM\ValidationRules\AccountNotFound;
use Chernoff\WHM\ValidationRules\AccountRequestError;
use Chernoff\WHM\ValidationRules\DomainRequestError;
use Chernoff\WHM\ValidationRules\IsEmpty;
use Chernoff\WHM\ValidationRules\IsNull;
use Chernoff\WHM\ValidationRules\PackageError;
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
        if (!($domainsInfo = $this->domainsGetAddonAll($whmUser, $domain))) {
            return false;
        }

        if (count($domainsInfo) != 1) {
            $domainsInfo = array_filter($domainsInfo, function ($item) use ($domain) { return ($item->domain == $domain); });
        }

        $domainInfo = reset($domainsInfo);
        $subdomain = $domainInfo->fullsubdomain;
        $request   = $this->send("json-api/cpanel", [
            "cpanel_jsonapi_user"   => strtolower($whmUser),
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "deladdondomain",
            "domain"                => $domain,
            "subdomain"             => $subdomain,
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
        return in_array($domain, $this->domainsGetAddon($whmUser, $domain));
    }

    /**
     * @param string $whmUser
     * @param null $domain
     * @return array
     */
    public function domainsGetAddon($whmUser, $domain = null)
    {
        $domains = [];

        foreach ($this->domainsGetAddonAll($whmUser, $domain) as $domain) {
            $domains[] = $domain->domain;
        }

        return $domains;
    }

    /**
     * @param string $whmUser
     * @param null $domain
     * @return array
     */
    public function domainsGetAddonAll($whmUser, $domain = null)
    {
        $request = [
            "cpanel_jsonapi_user"   => strtolower($whmUser),
            "cpanel_jsonapi_module" => "AddonDomain",
            "cpanel_jsonapi_func"   => "listaddondomains"
        ];

        if ($domain) {
            $request['regex'] = "^{$domain}$";
        }

        $response = $this->deployer->send("json-api/cpanel", $request);
        $domains = [];

        if (!empty($response->cpanelresult->data)) {
            foreach ($response->cpanelresult->data as $domain) {
                $domains[] = $domain;
            }
        }

        return $domains;
    }

    /**
     * @param $username
     * @param $password
     * @param $domain
     * @param $plan
     * @param $contactEmail
     * @param $maxAddon
     * @param $ip
     * @return object
     */
    public function accountCreate($username, $password, $domain, $plan, $contactEmail, $maxAddon, $ip)
    {
        $request = $this->send("json-api/createacct", [
            "username"     => $username,
            "password"     => $password,
            "contactemail" => $contactEmail,
            "domain"       => $domain,
            "plan"         => $plan,
            "ip"           => $ip,
            "maxaddon"     => $maxAddon,
        ],
        [
            new IsEmpty,
            new IsNull,
            new AccountRequestError
        ]);

        return $request->result[0]->options;
    }

    /**
     * @param $username
     * @param $plan
     * @return mixed
     */
    public function changePackage($username, $plan)
    {
        $request = $this->send(
            "json-api/changepackage",
            ["user" => $username, "pkg" => $plan],
            [new IsEmpty, new IsNull, new AccountRequestError]
        );

        return ($request->result[0]->status == 1);
    }

    /**
     * @param $plan
     * @return mixed
     */
    public function getPackage($plan)
    {
        $request = $this->send(
            "json-api/getpkginfo",
            ["pkg" => $plan, "api.version" => 1],
            [new IsNull, new PackageError]
        );

        return $request->data->pkg;
    }

    /**
     * @param $plan
     * @return mixed
     */
    public function addPackage($plan, $maxDomains)
    {
        $request = $this->send(
            "json-api/addpkg",
            ["name" => $plan, "maxaddon" => $maxDomains, "api.version" => 1],
            [new IsNull, new PackageError]
        );

        return $request->data->pkg;
    }

    /**
     * @param $username
     * @return bool
     */
    public function unsuspendAccount($username)
    {
        $request = $this->send(
            "json-api/unsuspendacct",
            ["user" => $username]
        );

        return ($request->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param $username
     * @param null $reason
     * @return bool
     */
    public function suspendAccount($username, $reason = null)
    {
        $request = $this->send(
            "json-api/suspendacct",
            ["user" => $username, "reason" => $reason]
        );

        return ($request->cpanelresult->data[0]->result == 1);
    }

    /**
     * @param $username
     * @return bool
     */
    public function terminateAccount($username) {
        $request = $this->send(
            "json-api/removeacct",
            ["user" => $username],
            [new IsEmpty, new IsNull, new AccountRequestError]
        );

        return ($request->result[0]->status == 1);
    }
}

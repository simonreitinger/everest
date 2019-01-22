<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebsiteRepository")
 */
class Website implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * Contao version
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * API version
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $api;

    /*
     * @ORM\Column(type="boolean", options={"default":"0"}, nullable=true)
     */
    private $supported = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Website
     */
    public function setUrl($url): Website
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Website
     */
    public function setToken($token): Website
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     * @return Website
     */
    public function setVersion($version): Website
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param mixed $api
     * @return Website
     */
    public function setApi($api): Website
    {
        $this->api = $api;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSupported()
    {
        return $this->supported;
    }

    /**
     * @param mixed $supported
     * @return Website
     */
    public function setSupported($supported): Website
    {
        $this->supported = $supported;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'token' => $this->token,
            'version' => $this->version,
            'api' => $this->api,
            'supported' => $this->supported
        ];
    }

}

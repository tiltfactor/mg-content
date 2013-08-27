<?php
/**
 *
 */

class MGGameService
{
    public $soapClient;

    private static $classmap = array(
        'RegisterResult'=>'RegisterResult',
        'Status'=>'Status',
        'StatusCode'=>'StatusCode',
        'CollectionDTO'=>'CollectionDTO',
        'LicenceDTO'=>'LicenceDTO',
        'MediaDTO'=>'MediaDTO',
        'AssignMediaDTO'=>'AssignMediaDTO',

    );

    function __construct($url='http://localhost/mggameserver/index.php/ws/content/wsdl/')
    {
        $this->soapClient = new SoapClient($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
    }


    function register($string)
    {
        $RegisterResult = $this->soapClient->register($string);
        return $RegisterResult;
    }

    function createCollection($string)
    {
        $Status = $this->soapClient->createCollection($string);
        return $Status;
    }

    function updateCollection($string)
    {
        $Status = $this->soapClient->updateCollection($string);
        return $Status;
    }

    function deleteCollection($string)
    {
        $Status = $this->soapClient->deleteCollection($string);
        return $Status;
    }

    function createLicence($string)
    {
        $Status = $this->soapClient->createLicence($string);
        return $Status;
    }

    function updateLicence($string)
    {
        $Status = $this->soapClient->updateLicence($string);
        return $Status;
    }

    function deleteLicence($string)
    {
        $Status = $this->soapClient->deleteLicence($string);
        return $Status;
    }

    function createMedia($string)
    {
        $Status = $this->soapClient->createMedia($string);
        return $Status;
    }

    function deleteMedia($string)
    {
        $Status = $this->soapClient->deleteMedia($string);
        return $Status;
    }

    function assignMediaToCollections($string)
    {
        $Status = $this->soapClient->assignMediaToCollections($string);
        return $Status;
    }

    function assignMediasToCollections($string)
    {
        $Status = $this->soapClient->assignMediasToCollections($string);
        return $Status;
    }
}
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

    function __construct()
    {
        $url = 'http://localhost/mggameserver/index.php/ws/content/wsdl/';//Yii::app()->fbvStorage->get("mg-api-url");
        $this->soapClient = new SoapClient($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
    }


    /**
     * @param InstitutionDTO $institution
     * @return RegisterResult
     */
    function register($institution)
    {
        $RegisterResult = $this->soapClient->register($institution);
        return $RegisterResult;
    }

    /**
     * @param string InstitutionDTO $institution
     * @return RegisterResult
     */
    function registerInstitution($institution)
    {
        $RegisterResult = $this->soapClient->register($institution);
        return $RegisterResult;
    }

    /**
     * @param string $token
     * @param CollectionDTO $collection
     * @return Status
     */
    function createCollection($token, $collection)
    {
        $Status = $this->soapClient->createCollection($token, $collection);
        return $Status;
    }

    /**
     * @param string $token
     * @param CollectionDTO $collection
     * @return Status
     */
    function updateCollection($token, $collection)
    {
        $Status = $this->soapClient->updateCollection($token, $collection);
        return $Status;
    }

    /**
     * @param string $token
     * @param integer $id
     * @return Status
     */
    function deleteCollection($token, $id)
    {
        $Status = $this->soapClient->deleteCollection($token, $id);
        return $Status;
    }

    /**
     * @param string $token
     * @param LicenceDTO $licence
     * @return Status
     */
    function createLicence($token, $licence)
    {
        $Status = $this->soapClient->createLicence($token, $licence);
        return $Status;
    }

    /**
     * @param string $token
     * @param LicenceDTO $licence
     * @return Status
     */
    function updateLicence($token, $licence)
    {
        $Status = $this->soapClient->updateLicence($token, $licence);
        return $Status;
    }

    /**
     * @param string $token
     * @param integer $id
     * @return Status
     */
    function deleteLicence($token, $id)
    {
        $Status = $this->soapClient->deleteLicence($token, $id);
        return $Status;
    }

    /**
     * @param string $token
     * @param MediaDTO $media
     * @return Status
     */
    function createMedia($token, $media)
    {
        $Status = $this->soapClient->createMedia($token, $media);
        return $Status;
    }

    /**
     * @param string $token
     * @param integer $id
     * @return Status
     */
    function deleteMedia($token, $id)
    {
        $Status = $this->soapClient->deleteMedia($token, $id);
        return $Status;
    }

    /**
     * @param string $token
     * @param AssignMediaDTO $assign
     * @return Status
     */
    function assignMediaToCollections($token, $assign)
    {
        $Status = $this->soapClient->assignMediaToCollections($token, $assign);
        return $Status;
    }

    /**
     * @param string $token
     * @param AssignMediaDTO[] $assigns
     * @return Status
     */
    function assignMediasToCollections($token, $assigns)
    {
        $Status = $this->soapClient->assignMediasToCollections($token, $assigns);
        return $Status;
    }
}
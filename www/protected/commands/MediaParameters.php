<?php
/**
 *
 * @package
 * @author
 */
class MediaParameters
{
    /**
     * @var string
     */
    public $filename;

    /**
     * @var bool
     */
    public $chunk = true;

    /**
     * @var int
     */
    public $chunkOffset = 20;


    /**
     * @static
     * @param string $json
     * @return MediaParameters
     */
    static public function createFromJson($json)
    {
        $json = json_decode($json);
        if (is_object($json)) {
            $object = new self();
            foreach ($json as $key => $value) {
                $object->{$key} = $value;
            }
        }
        return $object;
    }
}

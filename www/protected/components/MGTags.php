<?php
/**
 * This class is a collection of helper methods for various tasks
 *
 * Some of the code has been taken from http://code.google.com/p/yiiext/ many thanks to Alexander Makarov
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @copyright Copyright &copy; 2008-2012 Tiltfactor
 * @license http://www.metadatagames.com/license/
 * @package MG
 */

class MGTags
{

    /**
     * This method gets the tags that have been used for the medias identified by $medias_ids.
     * Only tag uses with a weight >=1 will be regarded.
     * <pre>
     * It will return an array of arrays
     *
     * array(
     *  media_id = array(
     *    tag_id => array(
     *      "tag" => "tag.tag" // the value of the tag column in the database
     *    )
     *    ...
     *  )
     *  ...
     * )
     * </pre>
     *
     * @param array $media_ids array of the media(s) which tags shall be retrieved
     * @param int $user_id if set only tag that have been used by the user will be shown
     * @return array the found tags for the media(s)
     */
    public static function getTags($media_ids, $user_id = null)
    {
        $tags = array();
        $used_tags = array();

        $builder = Yii::app()->db->getCommandBuilder();

        if ($user_id) {

            $used_tags = Yii::app()->db->createCommand()
                ->select('tu.media_id, t.id as tag_id, t.tag')
                ->from('{{tag_use}} tu')
                ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                ->leftJoin('{{game_submission}} gs', 'gs.id = tu.game_submission_id')
                ->leftJoin('{{session}} s', 's.id = gs.session_id')
                ->where(array('and', 's.user_id=:userID', 'tu.weight > 0', array('in', 'tu.media_id', array_values($media_ids))),
                    array(':userID' => $user_id))
                ->queryAll();

        } else {

            $used_tags = Yii::app()->db->createCommand()
                ->select('tu.media_id, t.id as tag_id, t.tag')
                ->from('{{tag_use}} tu')
                ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                ->where(array('and', 'tu.weight > 0', array('in', 'tu.media_id', array_values($media_ids))))
                ->queryAll();

        }
        foreach ($used_tags as $tag) {
            // PASSING: In most cases, we don't want to see any of the
            // so-called PASS tags, so we will filter them out here.
            if (strcasecmp($tag["tag"], "PASSONTHISTURN") == 0) {
                // DEBUG
                //Yii::log("FOUND a PASS tag.", "Error");
                continue;
            }

            if (!isset($tags[$tag["media_id"]]))
                $tags[$tag["media_id"]] = array();

            $tags[$tag["media_id"]][$tag["tag_id"]] = array("tag" => $tag["tag"]);
        }

        return $tags;
    }

    /**
     * This method gets the tags that have been used for the medias identified by $medias_ids.
     * Only tag uses with a weight >=1 will be regarded.
     * <pre>
     * It will return an array of arrays
     *
     * array(
     *  media_id = array(
     *    tag_id => array(
     *      "tag" => "tag.tag" // the value of the tag column in the database
     *    )
     *    ...
     *  )
     *  ...
     * )
     * </pre>
     *
     * @param array $media_ids array of the media(s) which tags shall be retrieved
     * @param int $user_id if set only tag that have been used by the user will be shown
     * @return array the found tags for the media(s)
     */
    public static function getUsersTags($media_ids, $user_id)
    {
        return self::getTags($media_ids, $user_id);
    }

    /**
     * This method gets the tags with a certain compound weight that have been used for the medias identified by $medias_ids.
     * <pre>
     * THE used SQL is:
     *
     * SELECT tu.media_id, tu.tag_id, t.tag, SUM(tu.weight) as total
     * FROM tag_use tu
     * LEFT JOIN tag t ON t.id=tu.tag_id
     * WHERE tu.weight > 0 AND tu.media_id IN ($media_ids)
     * GROUP BY tu.media_id, tu.tag_id, t.tag
     * HAVING total >= $weight
     * ORDER BY tu.media_id, total
     *
     * It will return an array of arrays
     *
     * array(
     *  media_id = array(
     *    tag_id => array(
     *      "tag" => "tag.tag" // the value of the tag column in the database
     *      "total" => "SUM(tu.weight)" // the total weight of tag uses for that tag and media
     *    )
     *    ...
     *  )
     *  ...
     * )
     * </pre>
     *
     * @param array $media_ids array of the media(s) which tags shall be retrieved
     * @param int $weight return only tags that have a compound weight equal or great than this value
     * @param int $user_id if set only tag that have been used by the user will be shown
     * @return array the found tags for the media(s)
     */
    public static function getTagsByWeightThreshold($media_ids, $weight, $user_id = null)
    {
        $tags = array();
        $used_tags = array();

        $builder = Yii::app()->db->getCommandBuilder();

        if ($user_id) {
            $used_tags = Yii::app()->db->createCommand()
                ->select('tu.media_id, tu.tag_id, t.tag, SUM(tu.weight) as total')
                ->from('{{tag_use}} tu')
                ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                ->leftJoin('{{game_submission}} gs', 'gs.id = tu.game_submission_id')
                ->leftJoin('{{session}} s', 's.id = gs.session_id')
                ->where(array('and', 's.user_id=:userID', 'tu.weight > 0', array('in', 'tu.media_id', array_values($media_ids))),
                    array(':userID' => $user_id))
                ->group('tu.media_id, tu.tag_id, t.tag')
                ->having('total >= :weight', array(":weight" => $weight))
                ->order('tu.media_id, total DESC')
                ->queryAll();

        } else {
            $used_tags = Yii::app()->db->createCommand()
                ->select('tu.media_id, tu.tag_id, t.tag, SUM(tu.weight) as total')
                ->from('{{tag_use}} tu')
                ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                ->where(array('and', 'tu.weight > 0', array('in', 'tu.media_id', array_values($media_ids))))
                ->group('tu.media_id, tu.tag_id, t.tag')
                ->having('total >= :weight', array(":weight" => $weight))
                ->order('tu.media_id, total DESC')
                ->queryAll();

        }
        foreach ($used_tags as $tag) {
            if (!isset($tags[$tag["media_id"]]))
                $tags[$tag["media_id"]] = array();

            $tags[$tag["media_id"]][$tag["tag_id"]] = array("tag" => $tag["tag"], "total" => $tag["total"]);
        }

        return $tags;
    }

    /**
     * Saves new tags and tag uses for one or more media(s)
     *
     * <pre>
     * Please use the follwing structure for the $tags array:
     *
     * $tags = array(
     *   "media_id" = array(
     *     "tag" => array(
     *        "weight" => 3,
     *        "original_tag" => "" //optional if set an original version will be created/updated
     *        "original_comment" => "" //optional if set an original version entry will be created/updated
     *        "original_by_user_id" => null | user_id //optional if set an original version entry will be created/updated
     *        "tag_id" => -1|tag_id // the tag id mark it as -1 to make sure that the tag is registered as a new tag.
     *        "type" => "new" // the type the tag use will be registered with. It can be new, matched, wordstoavoid, or set by an plugin
     *     )
     *     ...
     *   )
     *   ...
     * )
     * </pre>
     *
     * @param array $tags All needed information to save tag uses in the system
     * @param int $game_submission_id The ID of the game submission_id in the database
     * @param array $tags Tags to save (these are the final tags that will be saved in the database. they should already be cleaned up and processed)
     */
    public static function saveTags($tags, $game_submission_id)
    {
        foreach ($tags as $media_id => $media_tags) {
            $arr_tags = array();

            $all_tags_with_id = true;
            foreach ($media_tags as $tag => $tag_info) {

                if (!is_array($tags[$media_id][$tag]))
                    throw new CHttpException(500, Yii::t('app', "The array passed must have arrays as it's leafs."));

                if (!array_key_exists("tag_id", $tags[$media_id][$tag]) || (int)$tags[$media_id][$tag]["tag_id"] == 0) {
                    $all_tags_with_id = false;
                    $arr_tags[] = $tag;
                }
            }

            if (count($arr_tags) > 0 || $all_tags_with_id) {

                if (!$all_tags_with_id) {

                    $known_tags = Yii::app()->db->createCommand()
                        ->select('t.id, t.tag')
                        ->from('{{tag}} t')
                        ->where(array('in', 't.tag', array_values($arr_tags)))
                        ->queryAll();

                    if ($known_tags) {
                        foreach ($known_tags as $known_tag) {
                            $tags[$media_id][strtolower($known_tag["tag"])]["tag_id"] = $known_tag["id"];
                        }
                    }
                }

                foreach ($tags[$media_id] as $tag => $tag_info) {
                    if (!array_key_exists("tag_id", $tags[$media_id][$tag]) || (int)$tags[$media_id][$tag]["tag_id"] == 0) { // tag does not exist we have to create it
                        $tag_model = new Tag;
                        $tag_model->tag = $tags[$media_id][$tag]["tag"];
                        $tag_model->created = date('Y-m-d H:i:s');
                        $tag_model->modified = date('Y-m-d H:i:s');

                        if ($tag_model->validate()) {
                            try {
                                $tag_model->save();
                            } catch (CDbException $e) {
                                $tag_searched = Tag::model()->findByAttributes(array("tag" => $tags[$media_id][$tag]["tag"]));
                                if (is_null($tag_searched)) {
                                    throw new CHttpException(500, Yii::t('app', 'Internal Server Error: - TAG SAVE: ' . json_encode($tag_model->errors)));
                                } else {
                                    $tag_model = $tag_searched;
                                }
                            }
                        } else {
                            throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
                        }
                        $tags[$media_id][$tag]["tag_id"] = $tag_model->id;
                    }

                    // now we know all tags are registered and now all the id's let's add the tag_uses
                    $tag_use_model = new TagUse;
                    $tag_use_model->tag_id = (int)$tags[$media_id][$tag]["tag_id"];
                    $tag_use_model->media_id = (int)$media_id;
                    $tag_use_model->weight = (int)$tags[$media_id][$tag]["weight"];
                    $tag_use_model->type = (string)$tags[$media_id][$tag]["type"];
                    $tag_use_model->game_submission_id = (int)$game_submission_id;
                    $tag_use_model->created = date('Y-m-d H:i:s');

                    if ($tag_use_model->validate()) {
                        $tag_use_model->save();
                    } else {
                        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
                    }
                }
            }
        }
        return true;
    }

    /**
     * Get tags array from comma separated tags string.
     *
     * @access private
     * @param string|array $tags
     * @return array
     */
    public static function parseTags($tags)
    {
        if (!is_array($tags)) {
            $tags = explode(',', trim(strip_tags($tags), ' ,'));
        }

        array_walk($tags, array("MGTags", "trim"));

        foreach ($tags as $key => $value) {
            if ($value == "")
                unset($tags[$key]);
        }
        return array_unique(array_values($tags));
    }

    /**
     * Used as a callback to trim tags.
     *
     * @access private
     * @param string $item
     * @param string $key
     * @return string
     */
    public static function trim(&$item, $key)
    {
        $item = preg_replace("/\s/", " ", $item);
        while (strpos($item, "  ") !== FALSE) {
            $item = str_replace("  ", " ", $item);
        }
        $item = preg_replace("/[^\pL\pN\p{Zs}'-]/u", "", $item);
        $item = substr(trim($item), 0, 64); //we enforce the tags to have a maximum length of 64 characters after we've trimmed white spaces
    }

    /**
     * This method gets the tags that have been used for the media identified by $mediaId and the correct length.
     * <pre>
     * It will return an array of array
     *
     * array(
     *   array(
     *      "tag" => "tag.tag" // the value of the tag column in the database
     *      "tag_id"=> "id";
     *    )
     *    ...
     *  )
     * </pre>
     *
     * @param int $mediaId tag id
     * @param int $lenght tag length
     * @return array the found tags for the media
     */
    public static function getTagsByLength($mediaId, $lenght = 3)
    {
        $tags = array();
        $used_tags = array();
        $used_tags = Yii::app()->db->createCommand()
            ->select('t.id as tag_id, t.tag')
            ->from('{{tag_use}} tu')
            ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
            ->where('tu.weight > 0 and tu.media_id=:id and length(t.tag)=:l', array(':id' => $mediaId, ':l' => $lenght))
            ->group('tag_id')
            ->queryAll();


        foreach ($used_tags as $tag) {
            // PASSING: In most cases, we don't want to see any of the
            // so-called PASS tags, so we will filter them out here.
            if (strcasecmp($tag["tag"], "PASSONTHISTURN") == 0) {
                // DEBUG
                //Yii::log("FOUND a PASS tag.", "Error");
                continue;
            }

            $tags[] = array("tag" => $tag["tag"],
                "tag_id" => $tag["tag_id"]);
        }
        return $tags;
    }
}

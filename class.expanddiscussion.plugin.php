<?php


use Vanilla\Models\VanillaMediaSchema;

class ExpanddiscussionPlugin extends Gdn_Plugin {

	public function discussionsApiController_getOutput(
        array $previousResult,
        DiscussionsApiController $sender,
        $inSchema,
        array $query,
        array $row,
        $switch
    ): array {

    $discussions = $previousResult;

    $media = Gdn::getContainer()->get(MediaModel::class);
    $categories = array_column(CategoryModel::categories(), 'Name', 'CategoryID');
    //check if array of discussions
    if(is_array($discussions[0])){
        foreach ($discussions as $key=>&$discussion) {
            //$discussions[$key]['media'] = $media->findByForeignIdType($discussions['discussionID'],'discussion');//append media
            $discussions[$key]['media'] = $this->findMediaByForeignIdType($media,$discussion['discussionID'],'discussion');//append media
            $discussions[$key]['categoryName'] = $categories[$discussion['categoryID']];//append category name
        }
    }
    else
    {
	    //$discussions['media'] = $media->findByForeignIdType($discussions['discussionID'],'discussion');//append media
        $discussions['media'] = $this->findMediaByForeignIdType($media,$discussions['discussionID'],'discussion');//append media
	    $discussions['categoryName'] = $categories[$discussions['categoryID']];//append category_name
    }
	    
    return $discussions;
	}

    private function findMediaByForeignIdType($media,string $foreign_id, String $foreign_type)
    {
        $mediaItems = $media->getWhere([
            'ForeignTable' => $foreign_type,
            'ForeignID' => $foreign_id,
        ])->resultArray();

        foreach ($mediaItems as $key => $row) {
            $result_media_list[] = VanillaMediaSchema::normalizeFromDbRecord($row);
        }
        return  $result_media_list;
    }
}
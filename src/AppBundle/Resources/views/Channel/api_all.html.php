<?php 
$channels_list =  array();

foreach ($channels as $key => $channel) {
	$ch["id"]=$channel->getId();
	$ch["title"]=$channel->getTitle();
	$ch["label"]=$channel->getLabel();
	$ch["sublabel"]=$channel->getSublabel();
	$ch["description"]=$channel->getDescription();
	$ch["website"]=$channel->getWebsite();
	$ch["classification"]=$channel->getClassification();
	$ch["views"]=$channel->getViews();
	$ch["shares"]=$channel->getShares();
	$ch["rating"]=$channel->getRating();
	$ch["playas"]=$channel->getPlayas();
	$ch["comment"]=$channel->getComment();
	$ch["image"] = $this['imagine']->filter($view['assets']->getUrl($channel->getMedia()->getLink()), 'channel_thumb');
	$source_channel_list = array();
	foreach ($channel->getSources() as $key => $source_channel) {
		$source_channel_obj = array();
		$source_channel_obj["id"]=$source_channel->getId();
		$source_channel_obj["title"]=$source_channel->getTitle();
		$source_channel_obj["quality"]=$source_channel->getQuality();
		$source_channel_obj["size"]=$source_channel->getSize();
		$source_channel_obj["kind"]=$source_channel->getKind();
		$source_channel_obj["premium"]=$source_channel->getPremium();
		$source_channel_obj["external"]=$source_channel->getExternal();
		$source_channel_obj["type"]=$source_channel->getType();
		$source_channel_obj["url"]=$source_channel->getUrl();
		
		$source_channel_list[] = $source_channel_obj;
	}
	$ch["sources"] = $source_channel_list;

	$category_channel_list = array();
	foreach ($channel->getCategories() as $key => $category_channel) {
		$category_channel_obj = array();
		$category_channel_obj["id"]=$category_channel->getId();
		$category_channel_obj["title"]=$category_channel->getTitle();		
		$category_channel_list[] = $category_channel_obj;
	}
	$ch["categories"] = $category_channel_list;

	$country_channel_list = array();
	foreach ($channel->getCountries() as $key => $country_channel) {
		$country_channel_obj = array();
		$country_channel_obj["id"]=$country_channel->getId();
		$country_channel_obj["title"]=$country_channel->getTitle();		
		$country_channel_obj["image"] = $this['imagine']->filter($view['assets']->getUrl($country_channel->getMedia()->getLink()), 'country_thumb');

		$country_channel_list[] = $country_channel_obj;
	}
	$ch["countries"] = $country_channel_list;

	$channels_list[]=$ch;
}
echo json_encode($channels_list, JSON_UNESCAPED_UNICODE);

 ?>